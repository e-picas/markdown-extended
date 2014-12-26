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
 * Class StandardizeLineEnding
 * @package MarkdownExtended\Grammar\Tool
 */
class StandardizeLineEnding
    extends Tool
{

    /**
     * Standardize line endings: DOS to Unix and Mac to Unix
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function run($text)
    {
        return preg_replace('{\r\n?}', "\n", $text);
    }

}

// Endfile