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