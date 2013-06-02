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
namespace MarkdownExtended\Grammar\Tool;

use \MarkdownExtended\MarkdownExtended,
    \MarkdownExtended\Grammar\Tool;

class Header2Label extends Tool
{

 /**
  *
  */
	public function run($text) 
	{
  	// strip all Markdown characters
  	$text = str_replace( 
  		array("'", '"', "?", "*", "`", "[", "]", "(", ")", "{", "}", "+", "-", ".", "!", "\n", "\r", "\t"), 
  		"", strtolower($text) );

  	// strip the rest for visual signification
  	$text = str_replace( array("#", " ", "__", "/", "\\"), "_", $text );

		// strip non-ascii characters
		return preg_replace("/[^\x9\xA\xD\x20-\x7F]/", "", $text);
	}

}

// Endfile