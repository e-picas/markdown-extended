<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
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
use \MarkdownExtended\Grammar\GamutLoader;

/**
 * Process Markdown definitions lists
 *
 * Definitions lists may be written as:
 *
 *       Term 1
 *        :   This is a definition with two paragraphs. Lorem ipsum
 *            dolor sit amet, consectetuer adipiscing elit. Aliquam
 *            hendrerit mi posuere lectus.
 *
 *           Vestibulum enim wisi, viverra nec, fringilla in, laoreet
 *          vitae, risus.
 *
 *        :   Second definition for term 1, also wrapped in a paragraph
 *            because of the blank line preceding it.
 *
 *        Term 2
 *        :   This definition has a code block, a blockquote and a list.
 *
 */
class DefinitionList
    extends Filter
{

    /**
     * Form HTML definition lists.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        $less_than_tab = Kernel::getConfig('less_than_tab');
        // Re-usable pattern to match any entire dl list:
        $whole_list_re = '(?>
            (                                           # $1 = whole list
              (                                         # $2
                [ ]{0,'.$less_than_tab.'}
                ((?>.*\S.*\n)+)                         # $3 = defined term
                \n?
                [ ]{0,'.$less_than_tab.'}:[ ]+          # colon starting definition
              )
              (?s:.+?)
              (                                         # $4
                  \z
                |
                  \n{2,}
                  (?=\S)
                  (?!                                   # Negative lookahead for another term
                    [ ]{0,'.$less_than_tab.'}
                    (?: \S.*\n )+?                      # defined term
                    \n?
                    [ ]{0,'.$less_than_tab.'}:[ ]+      # colon starting definition
                  )
                  (?!                                   # Negative lookahead for another definition
                    [ ]{0,'.$less_than_tab.'}:[ ]+      # colon starting definition
                  )
              )
            )
        )'; // mx
        return preg_replace_callback('{
                (?>\A\n?|(?<=\n\n))
                '.$whole_list_re.'
            }mx',
            array($this, '_callback'), $text);
    }

    /**
     * Turn double returns into triple returns, so that we can make a
     * paragraph for the last item in a list, if necessary
     *
     * @param   array   $matches    The results form the `transform()` method
     * @return  string
     */
    protected function _callback($matches)
    {
        // Re-usable patterns to match list item bullets and number markers:
        $result = trim(self::transformItems($matches[1]));
        $result = str_replace('<!--dt-->', '', $result);
        $result = Kernel::get('OutputFormatBag')
//            ->buildTag('definition_list', "\n$result\n");
            ->buildTag('definition_list', $result);
        return parent::hashBlock($result) . "\n\n";
    }


    /**
     * Process the contents of a single definition list, splitting it
     * into individual term and definition list items.
     *
     * @param   string  $list_str   The result string form the `_callback()` function
     * @return  string
     */
    public function transformItems($list_str)
    {
        $less_than_tab = Kernel::getConfig('less_than_tab');
        // trim trailing blank lines:
        $list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

        // Process definition terms.
        $list_str = preg_replace_callback('{
            (?>\A\n?|\n\n+)                     # leading line
            (                                   # definition term = $1
                [ ]{0,'.$less_than_tab.'}       # leading whitespace
                (?![:][ ]|[ ])                  # negative lookahead for a definition mark (colon) or more whitespace.
                (?> \S.* \n)+?                  # actual term (not whitespace).
            )
            (?=\n?[ ]{0,3}:[ ])                 # lookahead for following line feed with a definition mark.
            }xm',
            array($this, '_item_callback_dt'), $list_str);

        // Process actual definitions.
        $list_str = preg_replace_callback('{
            \n(\n+)?                            # leading line = $1
            (                                   # marker space = $2
                [ ]{0,'.$less_than_tab.'}       # whitespace before colon
                [:][ ]+                         # definition mark (colon)
            )
            ((?s:.+?))                          # definition text = $3
            (?= \n+                             # stop at next definition mark,
                (?:                             # next term or end of text
                    [ ]{0,'.$less_than_tab.'} [:][ ]    |
                    <!--dt--> | \z
                )
            )
            }xm',
            array($this, '_item_callback_dd'), $list_str);

        return $list_str;
    }

    /**
     * Process the dt contents.
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _item_callback_dt($matches)
    {
        $terms = explode("\n", trim($matches[1]));
        $text = '';
        foreach ($terms as $term) {
            $term = Lexer::runGamut('span_gamut', trim($term));
            $text .= "\n" . Kernel::get('OutputFormatBag')
                ->buildTag('definition_list_item_term', $term);
        }
        return $text . "\n";
    }

    /**
     * Process the dd contents.
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _item_callback_dd($matches)
    {
        $leading_line   = $matches[1];
        $marker_space   = $matches[2];
        $def            = $matches[3];

        if ($leading_line || preg_match('/\n{2,}/', $def)) {
            // Replace marker with the appropriate whitespace indentation
            $def = str_repeat(' ', strlen($marker_space)) . $def;
            $def = Lexer::runGamut('html_block_gamut', Lexer::runGamut(GamutLoader::TOOL_ALIAS.':Outdent', $def . "\n\n"));
//            $def = "\n$def\n";
        } else {
            $def = rtrim($def);
            $def = Lexer::runGamut('span_gamut', Lexer::runGamut(GamutLoader::TOOL_ALIAS.':Outdent', $def));
        }
        return /*"\n" .*/ Kernel::get('OutputFormatBag')
            ->buildTag('definition_list_item_definition', $def)/* . "\n"*/;
    }
}
