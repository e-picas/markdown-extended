<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended;

use MarkdownExtended\API\Kernel;
use MarkdownExtended\API\OutputFormatInterface;
use MarkdownExtended\Util\Helper;
use MarkdownExtended\Exception\InvalidArgumentException;
use MarkdownExtended\Exception\UnexpectedValueException;

/**
 * This is the base object of output formatters
 */
class OutputFormatBag
{
    /**
     * @var  array   Table of grammar output tags called by filters (must be defined in the output formatter)
     */
    public static $tag_names = [
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
        'footnote_standard_item',
        'footnote_standard_link',
        'footnote_glossary_item',
        'footnote_glossary_link',
        'footnote_bibliography_item',
        'footnote_bibliography_link',
    ];

    /**
     * Current Formatter object
     *
     * @var     \MarkdownExtended\API\OutputFormatInterface
     */
    protected $formatter;

    /**
     * Loads a new formatter
     *
     * @param   string  $format     The formatter name
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the class can not be found
     */
    public function load($format)
    {
        $cls_name = $format;
        if (!class_exists($cls_name)) {
            $cls_name = '\MarkdownExtended\OutputFormat\\'.Helper::toCamelCase($format);
        }
        if (!class_exists($cls_name)) {
            throw new InvalidArgumentException(
                sprintf('Output format "%s" not found', $format)
            );
        }
        $cls = new $cls_name();
        if (Kernel::validate($cls, Kernel::TYPE_OUTPUTFORMAT, $format)) {
            $this->setFormatter($cls);
        }
    }

    /**
     * Magic method to pass any called method from the bag to its formatter
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if the method doesn't
     *          exist in the formatter class
     */
    public function __call($name, array $arguments = null)
    {
        if (empty($this->formatter)) {
            return null;
        }

        if (method_exists($this->getFormatter(), $name)) {
            if (!empty($arguments)) {
                return call_user_func_array([$this->getFormatter(), $name], $arguments);
            } else {
                return call_user_func([$this->getFormatter(), $name]);
            }
        } else {
            throw new UnexpectedValueException(
                sprintf(
                    'Call to undefined method "%s" on formatter "%s"',
                    $name,
                    get_class($this->getFormatter())
                )
            );
        }
    }

    /**
     * Set the current formatter
     *
     * @param   \MarkdownExtended\API\OutputFormatInterface $formatter
     * @return  self
     */
    public function setFormatter(OutputFormatInterface $formatter)
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
}
