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
 * Class AppendEndingNewLines
 * @package MarkdownExtended\Grammar\Tool
 */
class AppendEndingNewLines
    extends Tool
{

    /**
     * Make sure $text ends with a couple of newlines
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function run($text)
    {
        return $text."\n\n";
    }

}

// Endfile