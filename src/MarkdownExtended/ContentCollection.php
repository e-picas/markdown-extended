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

use \MarkdownExtended\Content;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;
use \Countable;
use \Iterator;
use \ArrayAccess;

/**
 * Class defining a collection of content objects
 */
class ContentCollection
    implements  Countable,
                Iterator,
                ArrayAccess
{

    /**
     * @var array
     */
    private $_elements;

    /**
     * @param array $elements
     */
    public function __construct(array $elements = array())
    {
        $this->_elements = $elements;
    }

// --------------------
// Iterator manipulation
// --------------------

    public function exists()
    {
        return count($this->_elements)!==0;
    }

    public function clear()
    {
        $this->_elements = array();
    }

    /**
     * @param misc $key
     * @param object $value \MarkdownExtended\Content
     */
    public function set($key, Content $value)
    {
        $this->_elements[$key] = $value;
    }

    /**
     * @param object $value \MarkdownExtended\Content
     */
    public function add(Content $value)
    {
        $this->_elements[] = $value;
    }

    /**
     * @param misc $key
     */
    public function remove($key)
    {
        if (isset($this->_elements[$key])) {
            unset($this->_elements[$key]);
        }
    }

    /**
     * @param misc $key
     *
     * @return misc|null \MarkdownExtended\Content
     */
    public function get($key)
    {
        return array_key_exists($key, $this->_elements) ? $this->_elements[$key] : null;
    }

// -------------------------
// ArrayAccess
// -------------------------

    /*
     * @return object \MarkdownExtended\Content
     */
    public function current()
    {
        return current($this->_elements);
    }

    public function key()
    {
        return key($this->_elements);
    }

    public function next()
    {
        return current($this->_elements);
    }

    public function rewind()
    {
        return reset($this->_elements);
    }

    public function valid()
    {
        $key = $this->key();
        return !empty($key) && $this->offsetExists($key);
    }
/*
    public function previous()
    {
        return reset($this->_elements);
    }
*/
    public function first()
    {
        return reset($this->_elements);
    }

    public function last()
    {
        return end($this->_elements);
    }

// -------------------------
// ArrayAccess
// -------------------------

    /**
     * @param misc $offset
     *
     * @return bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_elements);
    }

    /**
     * @param misc $offset
     *
     * @return misc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @param misc $offset
     * @param misc $value
     */
    public function offsetSet($offset, $value)
    {
        if (empty($offset)) {
            $this->add($value);
        } else {
            $this->set($offset, $value);
        }
    }

    /**
     * @param misc $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

// -------------------------
// Countable
// -------------------------

    /**
     * @return int
     */
    public function count()
    {
        return count($this->_elements);
    }

// -------------------------
// API
// -------------------------

    public function getArrayFilter($callback)
    {
        $collection = $this->_elements;
        if (is_callable($callback)) {
            $collection = array_filter($collection, $callback);
        } else {
            throw new MDE_Exception\InvalidArgumentException(
                'Callback used to filter contents collection must be callable!'
            );
        }
        return $collection;
    }

}

// Endfile
