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

class EncodeAmpAndAngle extends Tool
{

	/**
	 * Smart processing for ampersands and angle brackets that need to 
	 * be encoded. Valid character entities are left alone unless the
	 * no-entities mode is set.
	 *
	 * @param string $text The text to encode
	 * @return string The encoded text
	 */
	public function run($text) 
	{
		if (MarkdownExtended::getConfig('no_entities')) {
			$text = str_replace('&', '&amp;', $text);
		} else {
			// Ampersand-encoding based entirely on Nat Irons's Amputator
			// MT plugin: <http://bumppo.net/projects/amputator/>
			$text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', '&amp;', $text);
		}
		// Encode remaining <'s
		$text = str_replace('<', '&lt;', $text);
		return $text;
	}

}

// Endfile