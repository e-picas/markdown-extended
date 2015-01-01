<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\OutputFormatInterface;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Class AbstractOutputFormat
 *
 * @package MarkdownExtended\OutputFormat
 */
abstract class AbstractOutputFormat
    implements OutputFormatInterface
{

    /**
     * @var array An array of items constructed like:
     *
     *      interface tag name => array(
     *          'tag' => HTML tag name,
     *          'closable' => bool, // is the tag closed without content (i.e. "<br />")
     *      )
     */
    protected $tags_map = array();

    /**
     * This will try to call a method `build{TagName}()` if it exists, then will try to use
     * the object `$tags_map` static to automatically find what to do, and then call the
     * default `getTagString()` method passing it the arguments.
     *
     * @param   string  $tag_name
     * @param   string  $content
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    public function buildTag($tag_name, $content = null, array $attributes = array())
    {
        $_method = 'build'.MDE_Helper::toCamelCase($tag_name);
        if (isset($this->tags_map[$tag_name]) && isset($this->tags_map[$tag_name]['prefix'])) {
            $attributes['mde-prefix'] = $this->tags_map[$tag_name]['prefix'];
        }
        if (method_exists($this, $_method)) {
            return call_user_func_array(
                array($this, $_method),
                array($content, $attributes)
            );
        } elseif (isset($this->tags_map[$tag_name])) {
            $new_tag_name = isset($this->tags_map[$tag_name]['tag']) ?
                $this->tags_map[$tag_name]['tag'] : $tag_name;
            $closable = isset($this->tags_map[$tag_name]['closable']) ?
                $this->tags_map[$tag_name]['closable'] : false;
            return call_user_func_array(
                array($this, 'getTagString'),
                array($content, $new_tag_name, $attributes, $closable)
            );
        } else {
            return call_user_func_array(
                array($this, 'getTagString'),
                array($content, $tag_name, $attributes)
            );
        }
    }

    /**
     * @param   string  $content
     * @param   string  $tag_name
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    abstract public function getTagString($content, $tag_name, array $attributes = array());

}

// Endfile