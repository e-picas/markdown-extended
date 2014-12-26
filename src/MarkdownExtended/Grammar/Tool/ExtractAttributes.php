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
namespace MarkdownExtended\Grammar\Tool;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

/**
 * Class ExtractAttributes
 * @package MarkdownExtended\Grammar\Tool
 */
class ExtractAttributes
    extends Tool
{

    /**
     * Extract attributes from string 'a="b"'
     *
     * @param   string  $attributes The attributes to parse
     * @return  string              The attributes processed
     */
    public function run($attributes)
    {
        $this->img_attrs = array();
        $text = preg_replace_callback('{
            (\S+)=
            (["\']?)                  # $2: simple or double quote or nothing
            (?:
                ([^"|\']\S+|.*?[^"|\']) # anything but quotes
            )
            \\2                       # rematch $2
            }xsi', array($this, '_callback'), $attributes);
        return $this->img_attrs;
    }

    /**
     * @param   array   $matches
     */
    protected function _callback($matches)
    {
        $this->img_attrs[$matches[1]] = $matches[3];
    }

}

// Endfile