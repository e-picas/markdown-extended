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
class Markdown_Tool_StripSapcedLines extends Markdown_Tool
{
	
	/**
	 * Strip any lines consisting only of spaces and tabs.
	 * This makes subsequent regexen easier to write, because we can
	 * match consecutive blank lines with /\n+/ instead of something
	 * contorted like /[ ]*\n+/ .
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 */
	public function run($text) 
	{
		return preg_replace('/^[ ]+$/m', '', $text);
	}
	
}

// Endfile