<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \MarkdownExtended\Exception\InvalidArgumentException;

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

    public function getIterator()
    {
        return new \RecursiveArrayIterator($this->_indexes);
    }

    public function setIndexRegistry($name, array $values = array())
    {
        $this->_indexes[$name] = $values;
        return $this;
    }

    public function addIndexRegistry($name, array $values = array())
    {
        if ($this->hasIndexRegistry($name)) {
            throw new InvalidArgumentException(
                sprintf('Index registry "%s" already exists', $name)
            );
        }
        return $this->setIndexRegistry($name, $values);
    }

    public function hasIndexRegistry($name)
    {
        return isset($this->_indexes[$name]);
    }

    public function getIndexRegistry($name)
    {
        return ($this->hasIndexRegistry($name) ? $this->_indexes[$name] : null);
    }

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
