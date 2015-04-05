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

use \MarkdownExtended\Exception\InvalidArgumentException;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Util\Registry;

class Kernel
{

    const TYPE_OUTPUTFORMAT         = 0;
    const TYPE_GAMUT                = 1;
    const TYPE_CONTENT              = 2;

    const OUTPUTFORMAT_INTERFACE    = 'MarkdownExtended\API\OutputFormatInterface';
    const GAMUT_INTERFACE           = 'MarkdownExtended\API\GamutInterface';
    const CONTENT_INTERFACE         = 'MarkdownExtended\API\ContentInterface';

    const RESOURCE_TEMPLATE         = 'template';
    const RESOURCE_CONFIG           = 'config';
    const RESOURCE_TEMPLATE_MASK    = 'default-%s.tpl';
    const RESOURCE_CONFIG_MASK      = 'config-%s.ini';

    private $_registry;

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

    public static function get($name)
    {
        return self::getInstance()->_registry->get($name);
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
        switch ($type) {
            case self::TYPE_OUTPUTFORMAT:
                $api = self::OUTPUTFORMAT_INTERFACE;
                break;
            case self::TYPE_GAMUT:
                $api = self::GAMUT_INTERFACE;
                break;
            case self::TYPE_CONTENT:
                $api = self::CONTENT_INTERFACE;
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Unknown API type "%s"', $type)
                );
        }
        return (bool) in_array($api, class_implements($class_name), true);
    }

// -----------------
// Aliases
// -----------------

    public static function getConfig($name, $default = null)
    {
        return self::getInstance()->_registry->get('config')->get($name, $default);
    }

    public static function setConfig($name, $value)
    {
        return self::getInstance()->_registry->get('config')->set($name, $value);
    }

    public static function addConfig($name, $value)
    {
        $val = self::getConfig($name);
        if (is_array($val)) {
            if (is_array($value)) {
                $val = array_merge($val, $value);
            } else {
                $val[] = $value;
            }
        } elseif (is_string($val)) {
            $val .= $value;
        } else {
            $val = $value;
        }
        return self::setConfig($name, $val);
    }

    public static function getOption($name, $default = null)
    {
        return self::getConfig($name, $default);
    }

    public static function setOption($name, $value)
    {
        return self::setConfig($name, $value);
    }

    public static function addOption($name, $value)
    {
        return self::addConfig($name, $value);
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
