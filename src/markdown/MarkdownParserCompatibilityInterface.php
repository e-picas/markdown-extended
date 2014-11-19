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
