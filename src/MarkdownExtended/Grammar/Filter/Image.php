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
use \MarkdownExtended\Grammar\Lexer;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\GamutLoader;

/**
 * Process Markdown images
 */
class Image
    extends Filter
{

    /**
     * Turn Markdown image shortcuts into <img> tags.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        // First, handle reference-style labeled images: ![alt text][id]
        $text = preg_replace_callback('{
            (                                       # wrap whole match in $1
              !\[
                ('.Kernel::getConfig('nested_brackets_re').') # alt text = $2
              \]

              [ ]?                                  # one optional space
              (?:\n[ ]*)?                           # one optional newline followed by spaces

              \[
                (.*?)                               # id = $3
              \]

            )
            }xs',
            array($this, '_reference_callback'), $text);

        // Next, handle inline images:  ![alt text](url "optional title")
        // Don't forget: encode * and _
        $text = preg_replace_callback('{
            (                                         # wrap whole match in $1
              !\[
                ('.Kernel::getConfig('nested_brackets_re').') # alt text = $2
              \]
              \s?                                     # One optional whitespace character
              \(                                      # literal paren
                [ \n]*
                (?:
                    <(\S*)>                           # src url = $3
                |
                    ('.Kernel::getConfig('nested_parenthesis_re').')  # src url = $4
                )
                [ \n]*
                (                                     # $5
                  ([\'"])                             # quote char = $6
                  (.*?)                               # title = $7
                  \6                                  # matching quote
                  [ \n]*
                )?                                    # title is optional
              \)
            )
            }xs',
            array($this, '_inline_callback'), $text);

        return $text;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _reference_callback($matches)
    {
        $whole_match = $matches[1];
        $alt_text    = $matches[2];
        $link_id     = strtolower($matches[3]);

        if ($link_id == "") {
            $link_id = strtolower($alt_text); // for shortcut links like ![this][].
        }

        $urls   = Kernel::getConfig('urls');
        $titles = Kernel::getConfig('titles');
        $predef_attributes = Kernel::getConfig('attributes');
        $alt_text = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $alt_text);
        if (isset($urls[$link_id])) {
            $attributes = array();
            $attributes['alt']  = $alt_text;
            $attributes['id']   = Helper::header2Label($link_id);
            $attributes['src']  = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $urls[$link_id]);
            if (!empty($titles[$link_id])) {
                $attributes['title'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $titles[$link_id]);
            }
            if (!empty($predef_attributes[$link_id])) {
                $attributes = array_merge(
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':ExtractAttributes', $predef_attributes[$link_id]),
                    $attributes);
            }
            $block = Kernel::get('OutputFormatBag')
                ->buildTag('image', null, $attributes);
            $result = parent::hashPart($block);
        } else {
            // If there's no such link ID, leave intact
            $result = $whole_match;
        }

        return $result;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _inline_callback($matches)
    {
        $alt_text       = $matches[2];
        $url            = $matches[3] == '' ? $matches[4] : $matches[3];
        $title          =& $matches[7];

        $attributes = array();
        $attributes['alt'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $alt_text);
        $attributes['src'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $url);
        if (!empty($title)) {
            $attributes['title'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $title);
        }
        $block = Kernel::get('OutputFormatBag')
            ->buildTag('image', null, $attributes);
        return parent::hashPart($block);
    }
}
