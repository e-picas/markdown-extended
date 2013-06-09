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

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\Grammar\Tool;

class Outdent extends Tool
{

	/**
	 * Remove one level of line-leading tabs or spaces
	 *
	 * @param string $text The text to be parsed
	 * @return string The text parsed
	 */
	function run($text) 
	{
		return preg_replace('/^(\t|[ ]{1,'.MarkdownExtended::getConfig('tab_width').'})/m', '', $text);
	}

}

// Endfile