<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
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
