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
namespace MarkdownExtended\Grammar;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Grammar\AbstractGamut;
use \MarkdownExtended\Grammar\GamutInterface;

/**
 * Abstract base class for Tools
 */
abstract class Tool
    extends AbstractGamut
    implements GamutInterface
{

    /**
     * Must return a method name
     * @return string
     */
	public static function getDefaultMethod()
	{
		return 'run';
	}

	/**
     * Must process the tool on a text
     * @param string
     * @return string
	 */
	abstract public function run($text);

}

// Endfile