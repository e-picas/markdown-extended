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
class Markdown_Tool_EncodeAttribute extends Markdown_Tool
{

	/**
	 * Encode text for a double-quoted HTML attribute. This function
	 * is *not* suitable for attributes enclosed in single quotes.
	 *
	 * @param string $text The attributes content
	 * @return string The attributes content processed
	 */
	public function run($text) 
	{
		$text = parent::runGamut('tool:EncodeAmpAndAngle', $text);
		$text = str_replace('"', '&quot;', $text);
		return $text;
	}
	
}

// Endfile