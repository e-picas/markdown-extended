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

/**
 * Object to manage a cache registry
 */
class CacheRegistry
{

    /**
     * @var \MarkdownExtended\Util\Registry
     */
    protected $_cache;

    /**
     * Initialize the cache registry
     */
    public function __construct()
    {
        $this->_cache = new Registry;
    }

    /**
     * Tests if an index is cached
     *
     * @param string $index
     * @return bool
     */
    public function isCached($index)
    {
        return $this->_cache->has($index);
    }

    /**
     * Gets current cached content of an index
     *
     * @param string $index
     * @return mixed
     */
    public function getCache($index)
    {
        return $this->_cache->get($index);
    }

    /**
     * Sets a cached content for an index
     *
     * @param string $index
     * @param mixed $object
     *
     * @return $this
     */
    public function setCache($index, $object)
    {
        $this->_cache->set($index, $object);
        return $this;
    }

}
