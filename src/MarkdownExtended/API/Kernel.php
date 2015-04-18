<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

use MarkdownExtended\Exception\DomainException;
use \MarkdownExtended\Exception\InvalidArgumentException;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Util\Registry;

class Kernel
{

    /**
     * Identify an OutputFormat object
     */
    const TYPE_OUTPUTFORMAT         = 'output_format';

    /**
     * Identify a Gamut object
     */
    const TYPE_GAMUT                = 'gamut';

    /**
     * Identify a Content object
     */
    const TYPE_CONTENT              = 'content';

    /**
     * Identify a Template object
     */
    const TYPE_TEMPLATE             = 'template';

    /**
     * Interface all OutputFormat objects must implement
     */
    const OUTPUTFORMAT_INTERFACE    = 'MarkdownExtended\API\OutputFormatInterface';

    /**
     * Interface all Gamut (filter) objects must implement
     */
    const GAMUT_INTERFACE           = 'MarkdownExtended\API\GamutInterface';

    /**
     * Interface all Content objects must implement
     */
    const CONTENT_INTERFACE         = 'MarkdownExtended\API\ContentInterface';

    /**
     * Interface all Template objects must implement
     */
    const TEMPLATE_INTERFACE        = 'MarkdownExtended\API\TemplateInterface';

    /**
     * Dirname of internal resources
     */
    const RESOURCE_TEMPLATE         = 'template';

    /**
     * Dirname of internal configuration files
     */
    const RESOURCE_CONFIG           = 'config';

    /**
     * Internal templates mask
     */
    const RESOURCE_TEMPLATE_MASK    = 'default-%s.tpl';

    /**
     * Internal configuration mask
     */
    const RESOURCE_CONFIG_MASK      = 'config-%s.ini';

    /**
     * @var \MarkdownExtended\Util\Registry
     */
    private $_registry;

    /**
     * @var self Singleton instance of the Kernel
     */
    private static $_instance;

    /**
     * Private constructor
     */
    private function __construct()
    {
        $this->_registry = new Registry;
    }

    /**
     * Get Kernel's instance
     *
     * @return \MarkdownExtended\API\Kernel
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::createInstance();
        }
        return self::$_instance;
    }

    /**
     * Create a Kernel instance
     */
    public static function createInstance()
    {
        self::$_instance = new self;
        self::set('config', new Registry);
    }

    /**
     * Get the API's interface by object's type
     *
     * @param string $type
     * @return null|string
     */
    public static function getApiFromType($type)
    {
        switch ($type) {
            case self::TYPE_OUTPUTFORMAT:
                return self::OUTPUTFORMAT_INTERFACE;
                break;
            case self::TYPE_GAMUT:
                return self::GAMUT_INTERFACE;
                break;
            case self::TYPE_CONTENT:
                return self::CONTENT_INTERFACE;
                break;
            case self::TYPE_TEMPLATE:
                return self::TEMPLATE_INTERFACE;
                break;
            default:
                return null;
        }
    }

    /**
     * Gets a service by name
     *
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        $return = self::getInstance()->_registry->get($name);
        if (is_callable($return)) {
            $return = call_user_func($return);
        }
        return $return;
    }

    /**
     * Tests if a service exists
     *
     * @param string $name
     * @return mixed
     */
    public static function has($name)
    {
        return self::getInstance()->_registry->has($name);
    }

    /**
     * Sets a service
     *
     * @param string $name
     * @param mixed $value
     * @return \MarkdownExtended\API\Kernel
     */
    public static function set($name, $value)
    {
        self::getInstance()->_registry->set($name, $value);
        return self::getInstance();
    }

    /**
     * Removes an existing service
     *
     * @param string $name
     * @return \MarkdownExtended\API\Kernel
     */
    public static function remove($name)
    {
        self::getInstance()->_registry->remove($name);
        return self::getInstance();
    }

    /**
     * Tests if a class implements concerned API's interface
     *
     * @param string|object $class_name
     * @param string $type
     *
     * @return bool
     *
     * @throws \MarkdownExtended\Exception\InvalidArgumentException if `$type` is not a valid API's type
     */
    public static function valid($class_name, $type)
    {
        $api = self::getApiFromType($type);
        if (empty($api)) {
            throw new InvalidArgumentException(
                sprintf('Unknown API type "%s"', $type)
            );
        }
        return (bool) in_array($api, class_implements($class_name), true);
    }

    /**
     * Tests if a class implements concerned API's interface and throws an exception if not
     *
     * @param string|object $class_name
     * @param string $type
     * @param null $real_name
     *
     * @return bool
     *
     * @throws \MarkdownExtended\Exception\DomainException if validation of the object fails
     */
    public static function validate($class_name, $type, $real_name = null)
    {
        if (!self::valid($class_name, $type)) {
            throw new DomainException(
                sprintf(
                    'Object "%s" of type "%s" must implement API interface "%s"',
                    ($real_name ?: $class_name), $type, self::getApiFromType($type)
                )
            );
        }
        return true;
    }

// -----------------
// Configuration aliases
// -----------------

    /**
     * Gets a configuration entry
     *
     * @param string $name
     * @param null $default
     * @return null
     */
    public static function getConfig($name, $default = null)
    {
        if (false === strpos($name, '.')) {
            return self::get('config')->get($name, $default);
        }
        return self::_configRecursiveIterator('get', $name, null, $default);
    }

    /**
     * Sets a configuration entry
     *
     * @param string $name
     * @param mixed $value
     * @return null
     */
    public static function setConfig($name, $value)
    {
        if (false === strpos($name, '.')) {
            return self::get('config')->set($name, $value);
        }
        return self::_configRecursiveIterator('set', $name, $value);
    }

    /**
     * Merges a configuration entry (concatenates string or merges array)
     *
     * @param string $name
     * @param mixed $value
     * @return null
     */
    public static function addConfig($name, $value)
    {
        $item = self::getConfig($name);
        if (is_array($item)) {
            if (is_array($value)) {
                $item = array_merge($item, $value);
            } else {
                $item[] = $value;
            }
        } elseif (is_string($item)) {
            $item .= $value;
        } else {
            $item = $value;
        }
        return self::setConfig($name, $item);
    }

    /**
     * Internal configuration iterator
     *
     * This method is in charge to handle the "index.subindex" notation
     *
     * @param string $type
     * @param $index
     * @param null $value
     * @param null $default
     * @return null
     */
    protected static function _configRecursiveIterator(
        $type = 'get', $index, $value = null, $default = null
    ) {
        $result     = null;
        $indexer    = new \ArrayIterator(explode('.', $index));
        $iterator   = function (&$item, $key) use (&$iterator, &$accessor, &$result, $indexer, $value, $type) {
            if ($key === $indexer->current()) {
                $indexer->next();
                if ($indexer->valid() && is_array($item)) {
                    array_walk($item, $iterator);
                    return;
                }
                if ($type === 'set') {
                    $item = $value;
                    $result = true;
                } elseif ($type === 'get') {
                    $result = $item;
                }
            }
            return;
        };
        $config = self::get('config')->getAll();
        array_walk($config, $iterator);
        if ($type === 'set') {
            self::set('config', new Registry($config));
        }
        return $result ?: $default;
    }

// -----------------
// App resources finder
// -----------------

    /**
     * Finds an internal resource file by type
     *
     * @param string $name
     * @param string $type
     * @return null|string
     */
    public static function getResourcePath($name, $type)
    {
        if ($type === self::RESOURCE_CONFIG || $type === self::RESOURCE_TEMPLATE) {
            $local_path = realpath(Helper::getPath(array(
                __DIR__, '..', 'Resources', strtolower($type)
            )));

            if (file_exists($local = $local_path . DIRECTORY_SEPARATOR . $name)) {
                return $local;
            }

            $mask = ($type === self::RESOURCE_TEMPLATE) ? self::RESOURCE_TEMPLATE_MASK : self::RESOURCE_CONFIG_MASK;
            $final_name = sprintf($mask, $name);
            if (file_exists($final = $local_path . DIRECTORY_SEPARATOR . $final_name)) {
                return $final;
            }
        }
        return null;
    }
}
