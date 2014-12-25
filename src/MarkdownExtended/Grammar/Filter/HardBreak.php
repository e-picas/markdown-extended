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
 * Process Markdown hard breaks
 *
 * Hard breaks are written as one or more blank line(s).
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class HardBreak
    extends Filter
{

    /**
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback('/ {2,}\n/', array($this, '_callback'), $text);
    }

    /**
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        return parent::hashPart(MarkdownExtended::get('OutputFormatBag')->buildTag('new_line')."\n");
    }

}

// Endfile