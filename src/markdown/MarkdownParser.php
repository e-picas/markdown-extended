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
 * Class MarkdownParser
 * @package markdown
 */
class MarkdownParser
    implements MarkdownParserCompatibilityInterface
{

    public $options = array();

    /**
     * Main function
     *
     * @param   string  $text
     * @return  string
     * @compatibility   all
     */
    public function render($text)
    {
        return \MarkdownExtended\MarkdownExtended::create()
            ->transformString($text, $this->options)
            ->getBody();
    }

    /**
     * Performs some preprocessing on the input text and pass it through the document gamut.
     *
     * @param   string  $text
     * @return  string
     * @compatibility   michelf/php-markdown
     */
    public function transform($text)
    {
        return $this->render($text);
    }

    /**
     * Transform Markdown text to HTML.
     *
     * @param   string  $text
     * @return  string
     * @compatibility   dflydev/dflydev-markdown
     */
    public function transformMarkdown($text)
    {
        return $this->render($text);
    }

    /**
     * Initialize the parser and return the result of its transform method.
     * This will work fine for derived classes too.
     *
     * @param   string  $text
     * @return  string
     * @compatibility   michelf/php-markdown
     */
    public static function defaultTransform($text)
    {
        $_class = get_called_class();
        $_this = new $_class;
        return $_this->render($text);
    }

    /**
     * Performs a text for both inline and blocks elements
     *
     * @param   string  $text
     * @return  string
     * @compatibility   erusev/parsedown
     */
    public function text($text)
    {
        return $this->render($text);
    }

    /**
     * Performs a text with ONLY inline elements
     *
     * @param   string  $text
     * @return  string
     * @compatibility   erusev/parsedown
     */
    public function line($text)
    {
        $old_options = $this->options;
        $this->options = array(

        );
        $return = $this->render($text);
        $this->options = $old_options;
        return $return;
    }

}
