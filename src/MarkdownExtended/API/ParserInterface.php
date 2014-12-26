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
 * Interface for a content parser
 *
 * @package MarkdownExtended\API
 */
interface ParserInterface
{

    /**
     * Constructor function: Initialize the parser object
     *
     * The `$config` arguments accept both a string (a config INI file path) or an array
     * if you want to override config options ; in this case, you can set a config file path
     * with the `config_file` index.
     *
     * @param   array/string    $config
     */
    public function __construct($config = null);

    /**
     * Main function. Performs some preprocessing on the input text
     * and pass it through the document gamut.
     *
     * @param   \MarkdownExtended\API\ContentInterface  $content
     * @param   bool                                    $secondary
     * @return  \MarkdownExtended\MarkdownExtended
     */
    public function parse(\MarkdownExtended\API\ContentInterface $content, $secondary = false);
    
}

// Endfile
