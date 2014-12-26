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
namespace MarkdownExtended\Grammar;

use \MarkdownExtended\MarkdownExtended;

/**
 * The base class for all Filters and Tools
 * @package MarkdownExtended\Grammar
 */
abstract class AbstractGamut
{

    /**
     * Run a gamut stack from a filter or tool
     *
     * @param   string  $gamut  The name of a single Gamut or a Gamuts stack
     * @param   string  $text
     * @return  string
     */
    public function runGamut($gamut, $text)
    {
        return MarkdownExtended::get('Grammar\Gamut')->runGamut($gamut, $text);
    }

// ----------------------------------
// HASHES
// ----------------------------------

    /**
     * @var array
     */
    protected static $html_hashes = array();

    /**
     * Reset the hash table
     */
    public function resetHash()
    {
        self::$html_hashes = array();
    }

    /**
     * Reference a new hash
     *
     * @param   string  $key
     * @param   string  $text
     */
    public function setHash($key, $text)
    {
        self::$html_hashes[$key] = $text;
    }

    /**
     * Retrieve a hash by its key
     *
     * @param   string  $key
     */
    public function getHash($key)
    {
        return self::$html_hashes[$key];
    }

}

// Endfile