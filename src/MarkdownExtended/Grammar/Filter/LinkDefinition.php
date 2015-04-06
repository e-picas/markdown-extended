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

/**
 * Process Markdown definitions links
 *
 * Link defs are in the form:
 *
 *      ^[id]: url "optional title"
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class LinkDefinition
    extends Filter
{

    /**
     * Mandatory method
     */
    public function transform($text)
    {
        return $text;
    }

    /**
     * Strips link definitions from text, stores the URLs and titles in hash references
     *
     * @param   string  $text
     * @return  string
     * @todo    Manage attributes (not working for now)
     */
    public function strip($text)
    {
        return preg_replace_callback('{
                ^[ ]{0,'.Kernel::getConfig('less_than_tab').'}\[(.+)\][ ]?:   # id = $1
                  [ ]*
                  \n?                   # maybe *one* newline
                  [ ]*
                (?:
                  <(.+?)>               # url = $2
                |
                  (\S+?)                # url = $3
                )
                  [ ]*
                  \n?                   # maybe one newline
                  [ ]*
                (?:
                    (?<=\s)             # lookbehind for whitespace
                    ["(]
                    (.*?)               # title = $4
                    [")]
                    [ ]*
                )?                      # title is optional
                  [ ]*
                  \n?                   # maybe one newline
                  [ ]*
                (?:                     # Attributes = $5
                    (?<=\s)             # lookbehind for whitespace
                    (
                        ([ ]*\n)?
                        ((?:\S+?=\S+?)|(?:.+?=.+?)|(?:.+?=".*?")|(?:\S+?=".*?"))
                    )
                  [ ]*
                )?                      # attributes are optional
                (\n+|\Z)
            }xm',
            array($this, '_strip_callback'), $text);
    }

    /**
     * Add each link reference to `$urls` and `$titles` tables with index `$link_id`
     *
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string              Empty string
     */
    protected function _strip_callback($matches)
    {
        $link_id = strtolower($matches[1]);
        $url = $matches[2] == '' ? $matches[3] : $matches[2];
        Kernel::addConfig('urls', array($link_id=>$url));
        Kernel::addConfig('titles', array($link_id=>$matches[4]));
        Kernel::addConfig('attributes', array($link_id=>$matches[5]));
        return '';
    }

}

// Endfile