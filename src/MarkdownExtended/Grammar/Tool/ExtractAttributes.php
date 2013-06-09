<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
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

class ExtractAttributes extends Tool
{

	/**
	 * Extract attributes from string 'a="b"'
	 *
	 * @param string $attributes The attributes to parse
	 * @return string The attributes processed
	 */
	public function run($attributes)
	{
	    $this->img_attrs = array();
		$text = preg_replace_callback('{
			(\S+)=
			(["\']?)                  # $2: simple or double quote or nothing
			(?:
				([^"|\']\S+|.*?[^"|\']) # anything but quotes
			)
			\\2                       # rematch $2
			}xsi', array($this, '_callback'), $attributes);
		return $this->img_attrs;
	}

    protected function _callback($matches)
    {
	    $this->img_attrs[$matches[1]] = $matches[3];
	}

}

// Endfile