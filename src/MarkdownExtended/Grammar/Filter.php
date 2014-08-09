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
namespace MarkdownExtended\Grammar;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Grammar\AbstractGamut;
use \MarkdownExtended\API\GamutInterface;

/**
 * Base class for all Filters
 */
abstract class Filter
    extends AbstractGamut
    implements GamutInterface
{

    /**
     * Must return a method name
     *
     * @return  string
     */
    public static function getDefaultMethod()
    {
        return 'transform';
    }

    /**
     * Must process the filter on a text
     *
     * @param   string  $text
     * @return  string
     */
    abstract public function transform($text);

// ----------------------------------
// HASHES
// ----------------------------------

    /**
     * Called whenever a tag must be hashed when a function insert an atomic
     * element in the text stream. Passing $text to through this function gives
     * a unique text-token which will be reverted back when calling unhash.
     *
     * The $boundary argument specify what character should be used to surround
     * the token. By convention, "B" is used for block elements that needs not
     * to be wrapped into paragraph tags at the end, ":" is used for elements
     * that are word separators and "X" is used in the general case.
     *
     * @param   string  $text       The text to be parsed
     * @param   string  $boundary   A one letter boundary
     * @return  string              The text parsed
     * @see     self::unhash()
     */
    public function hashPart($text, $boundary = 'X')
    {
        // Swap back any tag hash found in $text so we do not have to `unhash`
        // multiple times at the end.
        $text = $this->unhash($text);
        // Then hash the block.
        static $i = 0;
        $key = "$boundary\x1A" . ++$i . $boundary;
        $this->setHash($key, $text);
        return $key; // String that will replace the tag.
    }

    /**
     * Shortcut function for hashPart with block-level boundaries.
     *
     * @param   string  $text   The text to be parsed
     * @return  string          Pass results of the `hashPart()` function
     * @see     self::hashPart()
     */
    public function hashBlock($text)
    {
        return self::hashPart($text, 'B');
    }

    /**
     * Called whenever a tag must be hashed when a function insert a "clean" tag
     * in $text, it pass through this function and is automatically escaped,
     * blocking invalid nested overlap.
     *
     * @param   string  $text   Text to parse
     * @return  string          Text parsed
     * @see     self::hashPart()
     */
    public function hashClean($text)
    {
        return self::hashPart($text, 'C');
    }

    /**
     * Swap back in all the tags hashed by _HashHTMLBlocks.
     *
     * @param   string  $text   The text to be parsed
     * @return  string          Pass results of the `_unhash_callback()` function
     * @see     self::_unhash_callback()
     */
    public function unhash($text)
    {
        return preg_replace_callback('/(.)\x1A[0-9]+\1/', array($this, '_unhash_callback'), $text);
    }

    /**
     * @param   array   $matches    A set of results of the `unhash()` function
     * @return  string
     */
    protected function _unhash_callback($matches)
    {
        return $this->getHash($matches[0]);
    }

}

// Endfile