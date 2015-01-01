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

use \MarkdownExtended\Content;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;
use \MarkdownExtended\API\CollectionInterface;

/**
 * Class defining a collection of content objects
 * @package MarkdownExtended
 */
class ContentCollection
    implements  CollectionInterface
{

    /**
     * @var     array
     */
    private $_elements;

    /**
     * @param   array   $elements
     */
    public function __construct(array $elements = array())
    {
        $this->_elements = $elements;
    }

// --------------------
// Iterator manipulation
// --------------------

    /**
     * @return  bool
     */
    public function exists()
    {
        return count($this->_elements)!==0;
    }

    /**
     * @return  void
     */
    public function clear()
    {
        $this->_elements = array();
    }

    /**
     * Define a new item in the collection by key
     *
     * @param   mixed   $key
     * @param   \MarkdownExtended\Content   $value
     * @return  void
     */
    public function set($key, Content $value)
    {
        $this->_elements[$key] = $value;
    }

    /**
     * Add a new item at the end of the collection
     *
     * @param   \MarkdownExtended\Content  $value
     */
    public function add(Content $value)
    {
        $this->_elements[] = $value;
    }

    /**
     * Delete an item of the collection by key
     *
     * @param   mixed    $key
     * @return  void
     */
    public function remove($key)
    {
        if (isset($this->_elements[$key])) {
            unset($this->_elements[$key]);
        }
    }

    /**
     * Get a collection item by key
     *
     * @param   mixed   $key
     * @return  mixed/null \MarkdownExtended\Content
     */
    public function get($key)
    {
        return array_key_exists($key, $this->_elements) ? $this->_elements[$key] : null;
    }

// -------------------------
// ArrayAccess
// -------------------------

    /**
     * Get the current collection item
     *
     * @return  \MarkdownExtended\Content
     */
    public function current()
    {
        return current($this->_elements);
    }

    /**
     * Get the current collection item key
     *
     * @return  string
     */
    public function key()
    {
        return key($this->_elements);
    }

    /**
     * Get next collection item
     *
     * @return  \MarkdownExtended\Content
     */
    public function next()
    {
        return current($this->_elements);
    }

    /**
     * Put the iterator on the first collection item
     *
     * @return  \MarkdownExtended\Content
     */
    public function rewind()
    {
        return reset($this->_elements);
    }

    /**
     * Test if current collection item exists
     *
     * @return  bool
     */
    public function valid()
    {
        $key = $this->key();
        return !empty($key) && $this->offsetExists($key);
    }

    /**
     * Rewind to first collection item
     *
     * @return  \MarkdownExtended\Content
     */
    public function first()
    {
        return reset($this->_elements);
    }

    /**
     * Go to last collection item
     *
     * @return  \MarkdownExtended\Content
     */
    public function last()
    {
        return end($this->_elements);
    }

// -------------------------
// ArrayAccess
// -------------------------

    /**
     * Test if a collection item exists by key
     *
     * @param   mixed   $offset
     * @return  bool
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->_elements);
    }

    /**
     * Get a collection item by key
     *
     * @param   mixed   $offset
     * @return  mixed
     */
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * Set a collection item by key
     *
     * @param   mixed   $offset
     * @param   mixed   $value
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
     * Unset a collection item by key
     *
     * @param   mixed   $offset
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

// -------------------------
// Countable
// -------------------------

    /**
     * Count number of collection items
     *
     * @return  int
     */
    public function count()
    {
        return count($this->_elements);
    }

// -------------------------
// API
// -------------------------

    /**
     * Run a callback of the collection items
     *
     * @param   callable    $callback
     * @return  array
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException
     */
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
