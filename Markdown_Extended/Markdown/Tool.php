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
abstract class Markdown_Tool implements Markdown_Extended_Gamut_Interface
{

 /**
  *
  */
	public static function getDefaultMethod()
	{
		return 'run';
	}

 /**
  *
  */
	abstract public function run($text);

// ----------------------------------
// GAMUTS
// ----------------------------------
	
 /**
  *
  */
	public function runGamut( $gamut, $text )
	{
		$_gmt = Markdown_Extended::get( 'Markdown_Extended_Gamut' );
		return $_gmt->runGamut( $gamut, $text );
	}
	
}

// Endfile