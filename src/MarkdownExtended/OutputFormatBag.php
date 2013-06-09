<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
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
namespace MarkdownExtended;

use MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * PHP Markdown Extended OutputFormat container
 */
class OutputFormatBag
{

    /**
     * Table of grammar output tags called by filters (must be defined in the output formater)
     * @static array
     */
    public static $tag_names = array(
        'meta_title', 'meta_data',
        'bold', 'italic',
        'new_line', 'horizontal_rule',
        'paragraph', 'title', 'block', 'span',
        'link', 'image',
        'blockquote', 'abbreviation',
        'preformated', 'code',
        'list', 'list_item',
        'ordered_list', 'ordered_list_item',
        'unordered_list', 'unordered_list_item',
        'definition_list', 'definition_list_item_term', 'definition_list_item_definition',
        'table', 'table_caption', 'table_header', 'table_body', 'table_footer', 'table_line', 'table_cell', 'table_cell_head',
    );

    /**
     * @var object MarkdownExtended\OutputFormatInterface
     */
    protected $formater;

    /**
     * Loads a new formater
     *
     * @throws MarkdownExtended\Exception\DomainException if the formater class doesn't
     *          implement `\MarkdownExtended\OutputFormatInterface`
     */    
    public function load($format)
    {
        $class_name = 'MarkdownExtended\OutputFormat\\'.MDE_Helper::toCamelCase($format);
        $_obj = MarkdownExtended::get($class_name);
        $interfaces = class_implements($_obj);
        if (in_array('MarkdownExtended\OutputFormatInterface', $interfaces)) {
                $this->formater =& $_obj;
        } else {
            throw new MDE_Exception\DomainException(sprintf(
                'Gamut class "%s" must implements interface "%s"!',
                $class, '\MarkdownExtended\OutputFormatInterface'
            ));
        }
    }

    /**
     * Magic method to pass any called method from the bag to its formater
     *
     * @throws MarkdownExtended\Exception\InvalidArgumentException if the method doesn't
     *          exist in the formater class
     */
    public function __call($name, array $arguments = null)
    {
        if (empty($this->formater)) return;

        if (method_exists($this->formater, $name)) {
            if (!empty($arguments)) {
                return call_user_func_array(array($this->formater, $name), $arguments);
            } else {
                return call_user_func(array($this->formater, $name));
            }
        } else {
            throw new MDE_Exception\InvalidArgumentException(sprintf(
                'Call to undefined method "%s" on formater "%s"!',
                $name, get_class($this->formater)
            ));
        }
    }

}

// Endfile
