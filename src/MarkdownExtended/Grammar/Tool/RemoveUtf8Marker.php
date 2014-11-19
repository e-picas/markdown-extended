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
 * Class RemoveUtf8Marker
 * @package MarkdownExtended\Grammar\Tool
 */
class RemoveUtf8Marker
    extends Tool
{

    /**
     * Remove UTF-8 BOM and marker character in input, if present.
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function run($text)
    {
        return preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $text);
    }

}

// Endfile