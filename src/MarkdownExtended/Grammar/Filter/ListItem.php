<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use \MarkdownExtended\Grammar\Filter;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\Lexer;

/**
 * Process Markdown list items
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class ListItem
    extends Filter
{

    /**
     * @var     int Retain current list level
     */
    protected static $list_level = 0;

    /**
     * @var     string  Re-usable patterns to match list item bullets and number markers
     */
    protected static $marker_ul_re  = '[*+-]';

    /**
     * @var     string  Re-usable patterns to match list item bullets and number markers
     */
    protected static $marker_ol_re  = '\d+[\.]';

    /**
     * Form HTML ordered (numbered) and unordered (bulleted) lists.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text) 
    {
        $markers_relist = array(
            self::$marker_ul_re => self::$marker_ol_re,
            self::$marker_ol_re => self::$marker_ul_re,
        );

        foreach ($markers_relist as $marker_re => $other_marker_re) {
            // Re-usable pattern to match any entirel ul or ol list:
            $whole_list_re = '
                (                                   # $1 = whole list
                  (                                 # $2
                    ([ ]{0,'.Kernel::getConfig('less_than_tab').'})   # $3 = number of spaces
                    ('.$marker_re.')                # $4 = first list item marker
                    [ ]+
                  )
                  (?s:.+?)
                  (                                 # $5
                      \z
                    |
                      \n{2,}
                      (?=\S)
                      (?!                           # Negative lookahead for another list item marker
                        [ ]*
                        '.$marker_re.'[ ]+
                      )
                    |
                      (?=                           # Lookahead for another kind of list
                        \n
                        \3                          # Must have the same indentation
                        '.$other_marker_re.'[ ]+
                      )
                  )
                )
            '; // mx
            
            // We use a different prefix before nested lists than top-level lists.
            // See extended comment in `self::transformItems()`.
            if (self::$list_level) {
                $text = preg_replace_callback('{
                        ^
                        '.$whole_list_re.'
                    }mx',
                    array($this, '_callback'), $text);
            } else {
                $text = preg_replace_callback('{
                        (?:(?<=\n)\n|\A\n?) # Must eat the newline
                        '.$whole_list_re.'
                    }mx',
                    array($this, '_callback'), $text);
            }
        }

        return $text;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _callback($matches) 
    {
        $marker_any_re = '(?:'.self::$marker_ul_re.'|'.self::$marker_ol_re.')';
        $list = $matches[1] . "\n";
        $list_type = preg_match('/'.self::$marker_ul_re.'/', $matches[4]) ? "unordered" : "ordered";        
        $marker_any_re = ( $list_type == "unordered" ? self::$marker_ul_re : self::$marker_ol_re );
        $list = self::transformItems($list, $marker_any_re);        
        $block = Kernel::get('OutputFormatBag')
            ->buildTag($list_type . '_list', $list);
        return "\n" . parent::hashBlock($block) . "\n\n";
    }

    /**
     *  Process the contents of a single ordered or unordered list, splitting it
     *  into individual list items.
     *
     * The self::$list_level global keeps track of when we're inside a list.
     * Each time we enter a list, we increment it; when we leave a list,
     * we decrement. If it's zero, we're not in a list anymore.
     *
     * We do this because when we're not inside a list, we want to treat
     * something like this:
     *
     *      I recommend upgrading to version
     *      8. Oops, now this line is treated
     *      as a sub-list.
     *
     * As a single paragraph, despite the fact that the second line starts
     * with a digit-period-space sequence.
     *
     * Whereas when we're inside a list (or sub-list), that line will be
     * treated as the start of a sub-list. What a kludge, huh? This is
     * an aspect of Markdown's syntax that's hard to parse perfectly
     * without resorting to mind-reading. Perhaps the solution is to
     * change the syntax rules such that sub-lists must start with a
     * starting cardinal number; e.g. "1." or "a.".
     *
     * @param   string  $list_str       The list string to parse
     * @param   string  $marker_any_re  The marker we are processing
     * @return  string                  The list string parsed
     */
    public function transformItems($list_str, $marker_any_re) 
    {
        self::$list_level++;

        // trim trailing blank lines:
        $list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

        $list_str = preg_replace_callback('{
            (\n)?                           # leading line = $1
            (^[ ]*)                         # leading whitespace = $2
            ('.$marker_any_re.'             # list marker and space = $3
                (?:[ ]+|(?=\n))             # space only required if item is not empty
            )
            ((?s:.*?))                      # list item text   = $4
            (?:(\n+(?=\n))|\n)              # trailing blank line = $5
            (?= \n* (\z | \2 ('.$marker_any_re.') (?:[ ]+|(?=\n))))
            }xm',
            array($this, '_items_callback'), $list_str);

        self::$list_level--;
        return $list_str;
    }

    /**
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _items_callback($matches) 
    {
        $item                   =   $matches[4];
        $leading_line           =&  $matches[1];
        $leading_space          =&  $matches[2];
        $marker_space           =   $matches[3];
        $trailing_blank_line    =&  $matches[5];

        if ($leading_line || $trailing_blank_line || preg_match('/\n{2,}/', $item)) {
            // Replace marker with the appropriate whitespace indentation
            $item = $leading_space . str_repeat(' ', strlen($marker_space)) . $item;
            $item = Lexer::runGamut('html_block_gamut', Lexer::runGamut('tools:Outdent', $item)."\n");
        } else {
            // Recursion for sub-lists:
            $item = self::transform(Lexer::runGamut('tools:Outdent', $item));
            $item = preg_replace('/\n+$/', '', $item);
            $item = Lexer::runGamut('span_gamut', $item);
        }

        return Kernel::get('OutputFormatBag')
            ->buildTag('list_item', $item)/* . "\n"*/;
    }

}
