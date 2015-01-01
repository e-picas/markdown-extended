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