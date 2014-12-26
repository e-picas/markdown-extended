<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Helper as MDE_Helper;
use MarkdownExtended\Exception as MDE_Exception;

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
                ^[ ]{0,'.MarkdownExtended::getConfig('less_than_tab').'}\[(.+)\][ ]?:   # id = $1
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
        MarkdownExtended::addVar('urls', array($link_id=>$url));
        MarkdownExtended::addVar('titles', array($link_id=>$matches[4]));
        MarkdownExtended::addVar('attributes', array($link_id=>$matches[5]));
        MarkdownExtended::getContent()->addUrl($url, $link_id);
        return '';
    }

}

// Endfile