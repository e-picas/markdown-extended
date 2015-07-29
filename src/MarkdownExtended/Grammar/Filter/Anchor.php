<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use \MarkdownExtended\Grammar\Filter;
use \MarkdownExtended\Grammar\Lexer;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\GamutLoader;

/**
 * Process Markdown links
 *
 * Process the links written like:
 *
 * -    reference-style links: `[link text] [id]`
 * -    inline-style links: `[link text](url "optional title")`
 * -    reference-style shortcuts: `[link text]` with a reference
 *
 * Each link attributes will be completed if needed adding it a `title` constructed using
 * the `link_mask_title` config entry, filled with the link URL.
 *
 */
class Anchor
    extends Filter
{
    /**
     * Set up the `in_anchor` config flag on `false`
     */
    public function _setup()
    {
        Kernel::setConfig('in_anchor', false);
    }

    /**
     * Turn Markdown link shortcuts into XHTML <a> tags.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        if (Kernel::getConfig('in_anchor') === true) {
            return $text;
        }

        Kernel::setConfig('in_anchor', true);

        // First, handle reference-style links: [link text] [id]
        $text = preg_replace_callback('{
            (                                       # wrap whole match in $1
              \[
                ('.Kernel::getConfig('nested_brackets_re').') # link text = $2
              \]

              [ ]?                                  # one optional space
              (?:\n[ ]*)?                           # one optional newline followed by spaces

              \[
                (.*?)                               # id = $3
              \]
            )
            }xs',
            array($this, '_reference_callback'), $text);

        // Next, inline-style links: [link text](url "optional title")
        $text = preg_replace_callback('{
            (                                               # wrap whole match in $1
              \[
                ('.Kernel::getConfig('nested_brackets_re').') # link text = $2
              \]
              \(                                            # literal paren
                [ \n]*
                (?:
                    <(.+?)>                                 # href = $3
                |
                    ('.Kernel::getConfig('nested_parenthesis_re').') # href = $4
                )
                [ \n]*
                (                                           # $5
                  ([\'"])                                   # quote char = $6
                  (.*?)                                     # Title = $7
                  \6                                        # matching quote
                  [ \n]*                                    # ignore any spaces/tabs between closing quote and )
                )?                                          # title is optional
              \)
            )
            }xs',
            array($this, '_inline_callback'), $text);

        // Last, handle reference-style shortcuts: [link text]
        // These must come last in case you've also got [link text][1]
        // or [link text](/foo)
        $text = preg_replace_callback('{
            (                   # wrap whole match in $1
              \[
                ([^\[\]]+)      # link text = $2; can\'t contain [ or ]
              \]
            )
            }xs',
            array($this, '_reference_callback'), $text);

        Kernel::setConfig('in_anchor', false);
        return $text;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _reference_callback($matches)
    {
        $whole_match =  $matches[1];
        $link_text   =  $matches[2];
        $link_id     =& $matches[3];

        // for shortcut links like [this][] or [this]
        if (empty($link_id)) {
            $link_id = $link_text;
        }

        // lower-case and turn embedded newlines into spaces
        $link_id = preg_replace('{[ ]?\n}', ' ', strtolower($link_id));

        $urls   = Kernel::getConfig('urls');
        $titles = Kernel::getConfig('titles');
        $predef_attributes = Kernel::getConfig('attributes');

        if (isset($urls[$link_id])) {
            $attributes = array();
            $attributes['href'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $urls[$link_id]);
            if (!empty($titles[$link_id])) {
                $attributes['title'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $titles[$link_id]);
            }
            if (!empty($predef_attributes[$link_id])) {
                $attributes = array_merge(
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':ExtractAttributes', $predef_attributes[$link_id]),
                    $attributes
                );
            }
            $block = Kernel::get('OutputFormatBag')
                ->buildTag('link', Lexer::runGamut('span_gamut', $link_text), $attributes);
            $result = parent::hashPart($block);
        } else {
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
        $link_text      =  $matches[2];
        $url            =  $matches[3] === '' ? $matches[4] : $matches[3];
        $title          =& $matches[7];

        $attributes = array();
        $attributes['href'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $url);
        if (!empty($title)) {
            $attributes['title'] = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $title);
        }
        $block = Kernel::get('OutputFormatBag')
            ->buildTag('link', Lexer::runGamut('span_gamut', $link_text), $attributes);
        return parent::hashPart($block);
    }
}
