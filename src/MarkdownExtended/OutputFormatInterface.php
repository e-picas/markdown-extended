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
namespace MarkdownExtended;

/**
 * PHP Markdown Extended OutputFormat interface
 */
interface OutputFormatInterface
{

	/**
	 * @param string $tag_name
	 * @param string $content
	 * @param array $attributes An array of attributes constructed like "variable=>value" pairs
	 *
	 * @return string
	 */
	public function buildTag($tag_name, $content, array $attributes = array());

	/**
	 * @param string $content
	 * @param string $tag_name
	 * @param array $attributes An array of attributes constructed like "variable=>value" pairs
	 *
	 * @return string
	 */
	public function getTagString($content, $tag_name, array $attributes = array());

}

// Endfile
