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