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