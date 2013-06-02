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

class BuildMetaData extends Tool
{
	
	/**
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 */
	public function run($text) 
	{
		return preg_replace_callback('{^([0-9a-zA-Z_-]*?):(.*)$}', 
			array($this, '_callback'), $text);
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
			return "<meta name=\"$meta_name\" content=\"$meta_value\" />";
	}
	
}

// Endfile