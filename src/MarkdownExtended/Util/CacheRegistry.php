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


class CacheRegistry
{

    protected $_cache;

    public function __construct()
    {
        $this->_cache = new Registry;
    }

    public function isCached($index)
    {
        return $this->_cache->has($index);
    }

    public function getCache($index)
    {
        return $this->_cache->get($index);
    }

    public function setCache($index, $object)
    {
        $this->_cache->set($index, $object);
        return $this;
    }

}

// Endfile