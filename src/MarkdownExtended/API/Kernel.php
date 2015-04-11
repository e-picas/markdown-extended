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

    const TYPE_OUTPUTFORMAT         = 'output_format';
    const TYPE_GAMUT                = 'gamut';
    const TYPE_CONTENT              = 'content';
    const TYPE_TEMPLATE             = 'template';

    const OUTPUTFORMAT_INTERFACE    = 'MarkdownExtended\API\OutputFormatInterface';
    const GAMUT_INTERFACE           = 'MarkdownExtended\API\GamutInterface';
    const CONTENT_INTERFACE         = 'MarkdownExtended\API\ContentInterface';
    const TEMPLATE_INTERFACE        = 'MarkdownExtended\API\TemplateInterface';

    const RESOURCE_TEMPLATE         = 'template';
    const RESOURCE_CONFIG           = 'config';
    const RESOURCE_TEMPLATE_MASK    = 'default-%s.tpl';
    const RESOURCE_CONFIG_MASK      = 'config-%s.ini';

    /**
     * @var \MarkdownExtended\Util\Registry
     */
    private $_registry;

    /**
     * @var self
     */
    private static $_instance;

    private function __construct()
    {
        $this->_registry = new Registry;
    }

    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::createInstance();
        }
        return self::$_instance;
    }

    public static function createInstance()
    {
        self::$_instance = new self;
        self::set('config', new Registry);
    }

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

    public static function get($name)
    {
        $return = self::getInstance()->_registry->get($name);
        if (is_callable($return)) {
            $return = call_user_func($return);
        }
        return $return;
    }

    public static function has($name)
    {
        return self::getInstance()->_registry->has($name);
    }

    public static function set($name, $value)
    {
        self::getInstance()->_registry->set($name, $value);
        return self::getInstance();
    }

    public static function remove($name)
    {
        self::getInstance()->_registry->remove($name);
        return self::getInstance();
    }

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

    public static function getConfig($name, $default = null)
    {
        if (false === strpos($name, '.')) {
            return self::get('config')->get($name, $default);
        }
        return self::_configRecursiveIterator('get', $name, null, false, $default);
    }

    public static function setConfig($name, $value)
    {
        if (false === strpos($name, '.')) {
            return self::get('config')->set($name, $value);
        }
        return self::_configRecursiveIterator('set', $name, $value);
    }

    public static function addConfig($name, $value)
    {
        return self::_configRecursiveIterator('set', $name, $value, true);
    }

    protected static function _configRecursiveIterator(
        $type = 'get', $index, $value = null, $merge = false, $default = null
    ) {
        $result     = null;
        $indexer    = new \ArrayIterator(explode('.', $index));
        $iterator   = function (&$item, $key) use (&$iterator, &$accessor, &$result, $indexer, $value, $type, $merge) {
            if ($key === $indexer->current()) {
                $indexer->next();
                if ($indexer->valid() && is_array($item)) {
                    array_walk($item, $iterator);
                    return;
                }
                if ($type === 'set') {
                    if ($merge && is_array($item)) {
                        if (is_array($value)) {
                            $item = array_merge($item, $value);
                        } else {
                            $item[] = $value;
                        }
                    } elseif ($merge && is_string($item)) {
                        $item .= $value;
                    } else {
                        $item = $value;
                    }
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
