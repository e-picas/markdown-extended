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

class EncodeAttribute
    extends Tool
{

    /**
     * Encode text for a double-quoted HTML attribute. This function
     * is *not* suitable for attributes enclosed in single quotes.
     *
     * @param   string  $text   The attributes content
     * @return  string          The attributes content processed
     */
    public function run($text)
    {
        $text = parent::runGamut('tool:EncodeAmpAndAngle', $text);
        $text = str_replace('"', '&quot;', $text);
        return $text;
    }

}

// Endfile