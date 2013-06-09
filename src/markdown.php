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

// -----------------------------------
// COMPOSER
// -----------------------------------

// get the Composer autoloader
if (file_exists($a = __DIR__.'/../../../autoload.php')) {
    require_once $a;
} elseif (file_exists($b = __DIR__.'/../vendor/autoload.php')) {
    require_once $b;

// else try to register `MarkdownExtended` namespace
} elseif (file_exists($c = __DIR__.'/src/SplClassLoader.php')) {
    require_once $c;
    $classLoader = new SplClassLoader('MarkdownExtended', __DIR__.'/src');
    $classLoader->register();

// else error, classes can't be found
} else {
    throw new \Exception(
        'You need to run Composer on the project to build dependencies and auto-loading'
        .' (see: <a href="http://getcomposer.org/doc/00-intro.md#using-composer">http://getcomposer.org/doc/00-intro.md#using-composer</a>)!'
    );
}

// -----------------------------------
// STANDARD FUNCTIONS INTERFACE
// -----------------------------------

/**
 * Transform an input text by the MarkdownExtended
 *
 * @param string $text
 * @param misc $options
 *
 * @return string
 */
function Markdown($text, $options = null) {
	\MarkdownExtended\MarkdownExtended::getInstance()
	    ->transformString($text, $options)
	    ->getFullContent();
}

/**
 * Transform an input file name source by the MarkdownExtended
 *
 * @param string $file_name
 * @param misc $options
 *
 * @return string
 */
function MarkdownFromSource($file_name, $options = null) {
	\MarkdownExtended\MarkdownExtended::getInstance()
	    ->transformSource($file_name, $options)
	    ->getFullContent();
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