<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar;

use MarkdownExtended\API\Kernel;

/**
 * A basic class for filters and tools with management of parsing "hashes"
 */
abstract class AbstractGamut
{
    /**
     * Run a gamut stack from a filter or tool
     *
     * @param   string  $gamut  The name of a single Gamut or a Gamuts stack
     * @param   string  $text
     * @param   bool    $forced Forces to run the gamut event if it is disabled
     *
     * @return  string
     */
    public function runGamut($gamut, $text, $forced = false)
    {
        $loader = Kernel::get('GamutLoader');
        return ($loader->isGamutEnabled($gamut) || $forced ? $loader->runGamut($gamut, $text) : $text);
    }

    // ----------------------------------
    // Hashes management
    // ----------------------------------

    /**
     * @var array
     */
    protected static $html_hashes = [];

    /**
     * Reset the hash table
     */
    public function resetHash()
    {
        self::$html_hashes = [];
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
