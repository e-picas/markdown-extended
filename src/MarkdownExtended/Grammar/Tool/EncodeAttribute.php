<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MarkdownExtended\Grammar\Tool;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

/**
 * Class EncodeAttribute
 * @package MarkdownExtended\Grammar\Tool
 */
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