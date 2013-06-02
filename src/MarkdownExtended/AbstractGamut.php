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
namespace MarkdownExtended;

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
		$_gmt = MarkdownExtended::get('\MarkdownExtended\Gamut');
		return $_gmt->runGamut($gamut, $text);
	}
	
}

// Endfile