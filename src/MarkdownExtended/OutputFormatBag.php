<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MarkdownExtended;

use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;
use \MarkdownExtended\API as MDE_API;

/**
 * PHP Markdown Extended OutputFormat container
 * @package MarkdownExtended
 */
class OutputFormatBag
{

    /**
     * @var  array   Table of grammar output tags called by filters (must be defined in the output formatter)
     */
    public static $tag_names = array(
        'abbreviation',
        'block',
        'blockquote',
        'bold',
        'code',
        'comment',
        'definition_list',
        'definition_list_item_term',
        'definition_list_item_definition',
        'horizontal_rule',
        'image',
        'italic',
        'link',
        'list',
        'list_item',
        'maths_block',
        'maths_span',
        'meta_title',
        'meta_data',
        'new_line',
        'ordered_list',
        'ordered_list_item',
        'paragraph',
        'preformatted',
        'span',
        'table',
        'table_body',
        'table_caption',
        'table_cell',
        'table_cell_head',
        'table_footer',
        'table_header',
        'table_line',
        'title',
        'unordered_list',
        'unordered_list_item',
    );

    /**
     * @var     \MarkdownExtended\API\OutputFormatInterface
     */
    protected $formatter;

    /**
     * @var     \MarkdownExtended\API\OutputFormatHelperInterface
     */
    protected $helper;

    /**
     * Loads a new formatter
     *
     * @param   string  $format     The formatter name
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the object creation sent an error
     */
    public function load($format)
    {
        $class_name = $format;
        if (!class_exists($class_name)) {
            $class_name = '\MarkdownExtended\OutputFormat\\'.MDE_Helper::toCamelCase($format);
        }
        try {
            $_obj = MarkdownExtended::factory($class_name, null, 'output_format');
            $this->setFormatter($_obj);
            $this->loadHelper($format);
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * Loads a formatter helper if it exists
     *
     * @param   string  $format     The formatter name
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the object creation sent an error
     */
    public function loadHelper($format)
    {
        $class_name = $format.'Helper';
        if (!class_exists($class_name)) {
            $class_name = '\MarkdownExtended\OutputFormat\\'.MDE_Helper::toCamelCase($format).'Helper';
        }
        if (!class_exists($class_name)) {
            $class_name = '\MarkdownExtended\OutputFormat\\DefaultHelper';
        }
        try {
            $_obj = MarkdownExtended::factory($class_name, null, 'output_format_helper');
            $this->setHelper($_obj);
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * Magic method to pass any called method from the bag to its formatter
     *
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the method doesn't
     *          exist in the formatter class
     */
    public function __call($name, array $arguments = null)
    {
        if (empty($this->formatter)) return;

        if (method_exists($this->getFormatter(), $name)) {
            if (!empty($arguments)) {
                return call_user_func_array(array($this->getFormatter(), $name), $arguments);
            } else {
                return call_user_func(array($this->getFormatter(), $name));
            }
        } else {
            throw new MDE_Exception\InvalidArgumentException(sprintf(
                'Call to undefined method "%s" on formatter "%s"!',
                $name, get_class($this->getFormatter())
            ));
        }
    }

    /**
     * Set the current formatter
     *
     * @param   \MarkdownExtended\API\OutputFormatInterface $formatter
     * @return  self
     */
    public function setFormatter(MDE_API\OutputFormatInterface $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Get current formatter
     *
     * @return  \MarkdownExtended\API\OutputFormatInterface
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * Set the current formatter helper
     *
     * @param   \MarkdownExtended\API\OutputFormatHelperInterface
     * @return  self
     */
    public function setHelper(MDE_API\OutputFormatHelperInterface $helper)
    {
        $this->helper = $helper;
        return $this;
    }

    /**
     * Get current formatter helper
     *
     * @return  \MarkdownExtended\API\OutputFormatHelperInterface
     */
    public function getHelper()
    {
        return $this->helper;
    }

}

// Endfile
