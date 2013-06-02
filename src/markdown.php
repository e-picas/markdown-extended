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

// -----------------------------------
// COMPOSER
// -----------------------------------

// get the Composer autoloader
if (file_exists($a = __DIR__.'/../../../autoload.php')) {
    require_once $a;
} elseif (file_exists($b = __DIR__.'/../vendor/autoload.php')) {
    require_once $b;
} else {
    throw new \Exception('You need to run Composer on your project to use this interface!');
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
	    ->get('\MarkdownExtended\Parser', $options)
	    ->transform($text);
}

/**
 * Transform an input file name source by the MarkdownExtended
 *
 * @param string $file_name
 * @param misc $options
 *
 * @return string
 *
 * @throws InvalidArumgentException if $file_name not found
 */
function MarkdownFromSource($file_name, $options = null) {
    if (file_exists($file_name)) {
    	\MarkdownExtended\MarkdownExtended::getInstance()
	        ->get('\MarkdownExtended\Parser', $options)
	        ->transform(file_get_contents($file_name));
	} else {
	    throw new \InvalidArgumentException(
	        sprintf('Source file "%s" not found!', $file_name)
	    );
	}
}

/**
 * Use the MarkdownExtended command line interface
 */
function Markdown_CLI() {
	\MarkdownExtended\MarkdownExtended::getInstance()
	    ->get('\MarkdownExtended\Console')
	    ->run();
}

// Endfile