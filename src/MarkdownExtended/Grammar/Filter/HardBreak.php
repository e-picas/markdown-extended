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

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\Grammar\Filter,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * Process Markdown hard breaks
 *
 * Hard breaks are written as one or more block line(s).
 */
class HardBreak extends Filter
{

	/**
	 * @param string $text
	 * @return string
	 */
	public function transform($text) 
	{
		return preg_replace_callback('/ {2,}\n/', array($this, '_callback'), $text);
	}

	/**
	 * @param array $matches A set of results of the `transform()` function
	 * @return string
	 */
	protected function _callback($matches) 
	{
		return parent::hashPart(MarkdownExtended::get('OutputFormatBag')->buildTag('new_line')."\n");
	}

}

// Endfile