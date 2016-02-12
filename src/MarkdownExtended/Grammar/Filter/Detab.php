<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use \MarkdownExtended\Grammar\Filter;
use \MarkdownExtended\API\Kernel;

/**
 * Class Detab
 */
class Detab
    extends Filter
{
    /**
     * String length function for detab. `_initDetab` will create a function to
     * hanlde UTF-8 if the default function does not exist.
     */
    public $utf8_strlen = 'mb_strlen';

    /**
     * Check for the availability of the function in the `utf8_strlen` property
     * (initially `mb_strlen`). If the function is not available, create a
     * function that will loosely count the number of UTF-8 characters with a
     * regular expression.
     */
    public function init()
    {
        if (!function_exists($this->utf8_strlen)) {
            $this->utf8_strlen = create_function('$text', 'return preg_match_all(
            "/[\\\\x00-\\\\xBF]|[\\\\xC0-\\\\xFF][\\\\x80-\\\\xBF]*/",
            $text, $m);');
        }
        Kernel::setConfig('utf8_strlen', $this->utf8_strlen);
    }

    /**
     * Replace tabs with the appropriate amount of space.
     *
     * For each line we separate the line in blocks delemited by
     * tab characters. Then we reconstruct every line by adding the
     * appropriate number of space between each blocks.
     *
     * @param   string  $text   The text to be parsed
     * @return  string          The text parsed
     * @see     self::_detab_callback()
     */
    public function transform($text)
    {
        $text = preg_replace_callback('/^.*\t.*$/m', array($this, '_callback'), $text);
        return $text;
    }

    /**
     * Process tabs replacement
     *
     * @param   array   $matches    A set of results of the `detab()` function
     * @return  string              The line rebuilt
     */
    protected function _callback($matches)
    {
        $line = $matches[0];
        $strlen = $this->utf8_strlen; // strlen function for UTF-8.

        // Split in blocks.
        $blocks = explode("\t", $line);
        // Add each blocks to the line.
        $line = $blocks[0];
        unset($blocks[0]); // Do not add first block twice.
        foreach ($blocks as $block) {
            // Calculate amount of space, insert spaces, insert block.
            $amount = Kernel::getConfig('tab_width') - $strlen($line, 'UTF-8') % Kernel::getConfig('tab_width');
            $line .= str_repeat(" ", $amount) . $block;
        }
        return $line;
    }
}
