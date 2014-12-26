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
namespace MarkdownExtended\API;

/**
 * PHP Markdown Extended OutputFormat interface
 *
 * @package MarkdownExtended\API
 */
interface OutputFormatInterface
{

    /**
     * @param   string  $tag_name
     * @param   string  $content
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    public function buildTag($tag_name, $content = null, array $attributes = array());

    /**
     * @param   string  $content
     * @param   string  $tag_name
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    public function getTagString($content, $tag_name, array $attributes = array());

}

// Endfile
