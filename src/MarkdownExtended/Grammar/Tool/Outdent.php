<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Tool;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

/**
 * Class Outdent
 * @package MarkdownExtended\Grammar\Tool
 */
class Outdent
    extends Tool
{

    /**
     * Remove one level of line-leading tabs or spaces
     *
     * @param   string  $text   The text to be parsed
     * @return  string          The text parsed
     */
    function run($text)
    {
        return preg_replace('/^(\t|[ ]{1,'.MarkdownExtended::getConfig('tab_width').'})/m', '', $text);
    }

}

// Endfile