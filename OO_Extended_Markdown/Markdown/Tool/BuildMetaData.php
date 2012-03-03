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
class Markdown_Tool_BuildMetaData extends Markdown_Tool
{
	
	/**
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 */
	public function run($text) 
	{
		return preg_replace_callback('{^([0-9a-zA-Z_-]*?):(.*)$}', 
			array(&$this, '_callback'), $text);
	}

	protected function _callback($matches)
	{
		return self::buildMetaDataString( $matches[1], $matches[2] );
	}

	public function buildMetaDataString( $meta_name, $meta_value )
	{
		if ($meta_name=='title')
			return "<title>$meta_value</title>";
		else
			return "<meta name=\"$meta_name\" value=\"$meta_value\" />";
	}
	
}

// Endfile