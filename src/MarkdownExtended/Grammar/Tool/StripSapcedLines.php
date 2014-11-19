<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
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

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

/**
 * Class StripSpacedLines
 * @package MarkdownExtended\Grammar\Tool
 */
class StripSpacedLines extends Tool
{

    /**
     * Strip any lines consisting only of spaces and tabs.
     * This makes subsequent regex easier to write, because we can
     * match consecutive blank lines with /\n+/ instead of something
     * contorted like /[ ]*\n+/ .
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function run($text)
    {
        return preg_replace('/^[ ]+$/m', '', $text);
    }

}

// Endfile