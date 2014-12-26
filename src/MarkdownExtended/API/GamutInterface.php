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
 * Interface GamutInterface
 *
 * @package MarkdownExtended\API
 */
interface GamutInterface
{

    /**
     * Must return a method name
     *
     * @return  string
     */
    public static function getDefaultMethod();

}

// Endfile