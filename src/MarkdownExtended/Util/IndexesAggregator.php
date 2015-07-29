<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \MarkdownExtended\Exception\InvalidArgumentException;

/**
 * Object to store multi-stacks of data by names
 */
class IndexesAggregator
    implements \IteratorAggregate
{
    /**
     * @var array
     */
    protected $_indexes;

    /**
     * Initialize the indexes array
     */
    public function __construct()
    {
        $this->_indexes = array();
    }

    /**
     * Get indexes as iterator
     *
     * @return \RecursiveArrayIterator
     */
    public function getIterator()
    {
        return new \RecursiveArrayIterator($this->_indexes);
    }

    /**
     * Creates or sets an indexed stack
     *
     * @param string $name
     * @param array $values
     *
     * @return $this
     */
    public function setIndexRegistry($name, array $values = array())
    {
        $this->_indexes[$name] = $values;
        return $this;
    }

    /**
     * Creates a new indexed stack
     *
     * @param string $name
     * @param array $values
     *
     * @return \MarkdownExtended\Util\IndexesAggregator
     *
     * @throws \MarkdownExtended\Exception\InvalidArgumentException if the stack already exists
     */
    public function addIndexRegistry($name, array $values = array())
    {
        if ($this->hasIndexRegistry($name)) {
            throw new InvalidArgumentException(
                sprintf('Index registry "%s" already exists', $name)
            );
        }
        return $this->setIndexRegistry($name, $values);
    }

    /**
     * Tests if a stack already exists
     *
     * @param string $name
     * @return bool
     */
    public function hasIndexRegistry($name)
    {
        return isset($this->_indexes[$name]);
    }

    /**
     * Gets a stack by name
     *
     * @param string $name
     * @return null
     */
    public function getIndexRegistry($name)
    {
        return ($this->hasIndexRegistry($name) ? $this->_indexes[$name] : null);
    }

    /**
     * Adds a value in a stack
     *
     * @param string $registry
     * @param mixed $value
     * @param null|string $index
     *
     * @return $this
     *
     * @throws \MarkdownExtended\Exception\InvalidArgumentException if the stack does not exist
     */
    public function addIndex($registry, $value, $index = null)
    {
        if (!$this->hasIndexRegistry($registry)) {
            throw new InvalidArgumentException(
                sprintf('Index registry "%s" not found', $registry)
            );
        }
        if (is_null($index)) {
            $this->_indexes[$registry][] = $value;
        } else {
            $this->_indexes[$registry][$index] = $value;
        }
        return $this;
    }
}
