<?php
/**
 * PHP Extended Markdown
 * Copyright (c) 2004-2012 Pierre Cassat
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

/**
 *
 */
class Markdown_Tool_Header2Label extends Markdown_Tool
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