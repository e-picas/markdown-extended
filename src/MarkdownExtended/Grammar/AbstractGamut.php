<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2004-2013 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar;

use MarkdownExtended\MarkdownExtended;

/**
 * The base class for all Filters and Tools
 */
abstract class AbstractGamut
{

	/**
	 * Run a gamut stack from a filter or tool
	 *
	 * @param string $gamut The name of a single Gamut or a Gamuts stack
	 * @param string $text
	 *
	 * @return string
	 */
	public function runGamut($gamut, $text)
	{
		return MarkdownExtended::get('Grammar\Gamut')->runGamut($gamut, $text);
	}
	
// ----------------------------------
// HASHES
// ----------------------------------

    /**
     * @static array
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
     * @param string $key
     * @param string $text
     */
    public function setHash($key, $text)
    {
        self::$html_hashes[$key] = $text;
    }
	
    /**
     * Retrieve a hash by its key
     *
     * @param string $key
     */
    public function getHash($key)
    {
        return self::$html_hashes[$key];
    }

}

// Endfile