<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
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

// Show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// Namespaces loader
require __DIR__.'/../src/bootstrap.php';

// silent errors
@error_reporting(-1);

// standard markdown functions for compatibility

/**
 * Transform an input text by the MarkdownExtended
 *
 * @param string $text
 * @param misc $options
 * @param string $type The part of the content to get ; can be 'full', 'body' (default)
 *                      or false to get the `Content` object
 *
 * @return string
 */
function Markdown($text, $options = null, $type = 'body') {
    \MarkdownExtended\MarkdownExtended::getInstance()
        ->transformString($text, $options);
    if ($type==='full') {
        return \MarkdownExtended\MarkdownExtended::getFullContent();
    } elseif ($type==='body') {
        return \MarkdownExtended\MarkdownExtended::getContent()->getBody();
    } else {
        return \MarkdownExtended\MarkdownExtended::getContent();
    }
}

/**
 * Transform an input file name source by the MarkdownExtended
 *
 * @param string $file_name
 * @param misc $options
 * @param string $type The part of the content to get ; can be 'full', 'body' (default)
 *                      or false to get the `Content` object
 *
 * @return string
 */
function MarkdownFromSource($file_name, $options = null, $type = 'body') {
    \MarkdownExtended\MarkdownExtended::getInstance()
        ->transformSource($file_name, $options);
    if ($type==='full') {
        return \MarkdownExtended\MarkdownExtended::getFullContent();
    } elseif ($type==='body') {
        return \MarkdownExtended\MarkdownExtended::getContent()->getBody();
    } else {
        return \MarkdownExtended\MarkdownExtended::getContent();
    }
}

/**
 * Use the MarkdownExtended command line interface
 */
function MarkdownCli() {
    \MarkdownExtended\MarkdownExtended::getInstance()
        ->get('CommandLine\Console')
        ->run();
}

// Endfile
