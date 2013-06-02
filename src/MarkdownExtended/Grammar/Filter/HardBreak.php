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
namespace MarkdownExtended\Grammar\Filter;

use \MarkdownExtended\MarkdownExtended,
    \MarkdownExtended\Grammar\Filter;

class HardBreak extends Filter
{

	/**
	 * @param string $text The text to be parsed
	 * @return string The text parsed
	 * @see _doHardBreaks_callback()
	 */
	public function transform($text) 
	{
		// Do hard breaks:
		return preg_replace_callback('/ {2,}\n/', array($this, '_callback'), $text);
	}

	/**
	 * @param array $matches A set of results of the `doHardBreak()` function
	 * @return string The text parsed
	 * @see hashPart()
	 */
	protected function _callback($matches) 
	{
		return parent::hashPart("<br".MarkdownExtended::getConfig('empty_element_suffix')."\n");
	}

}

// Endfile