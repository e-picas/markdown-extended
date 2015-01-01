<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace markdown;

/**
 * Interface MarkdownParserCompatibilityInterface
 * @package markdown
 */
interface MarkdownParserCompatibilityInterface
{

    /**
     * Main function
     *
     * @param   string  $text
     * @return  string
     * @compatibility   all
     */
    public function render($text);

    /**
     * Performs some preprocessing on the input text and pass it through the document gamut.
     *
     * @param   string  $text
     * @return  string
     * @compatibility   michelf/php-markdown
     */
    public function transform($text);

    /**
     * Transform Markdown text to HTML.
     *
     * @param   string  $text
     * @return  string
     * @compatibility   dflydev/dflydev-markdown
     */
    public function transformMarkdown($text);

    /**
     * Initialize the parser and return the result of its transform method.
     * This will work fine for derived classes too.
     *
     * @param   string  $text
     * @return  string
     * @compatibility   michelf/php-markdown
     */
    public static function defaultTransform($text);

    /**
     * Performs a text for both inline and blocks elements
     *
     * @param   string  $text
     * @return  string
     * @compatibility   erusev/parsedown
     */
    public function text($text);

    /**
     * Performs a text with ONLY inline elements
     *
     * @param   string  $text
     * @return  string
     * @compatibility   erusev/parsedown
     */
    public function line($text);

}
