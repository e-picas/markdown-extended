<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

use \MarkdownExtended\API as MDE_API;

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
    public function parse(MDE_API\ContentInterface $content, $secondary = false);
    
}

// Endfile
