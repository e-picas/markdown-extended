<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar;

use MarkdownExtended\Util\CacheRegistry;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\Exception\UnexpectedValueException;

/**
 * Central class to execute filters and tools methods on a content
 *
 * It can handle a list of gamuts, execute a specific method and run a single gamut.
 */
class GamutLoader extends CacheRegistry
{
    public const FILTER_ALIAS      = 'filter';

    public const TOOL_ALIAS        = 'tool';

    public const FILTER_NAMESPACE  = 'MarkdownExtended\Grammar\Filter';

    public const TOOL_CLASS        = 'MarkdownExtended\Grammar\Tools';

    /**
     * @var array
     */
    protected $all_gamuts;

    /**
     * Gets a gamuts' array by name
     *
     * @param string $name
     *
     * @return null|array
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if `$name` seems malformed or can not be found
     */
    public function getGamutStack($name)
    {
        if (!$this->isGamutStackName($name)) {
            throw new UnexpectedValueException(
                sprintf('A gamut stack name must follow a form like "%%_gamut", "%s" given', $name)
            );
        }

        $stack = Kernel::getConfig($name);
        if (empty($stack)) {
            throw new UnexpectedValueException(
                sprintf('Unknown gamut stack "%s"', $name)
            );
        }

        return $stack;
    }

    /**
     * Gets the type of gamut in "filter", "tools" or other
     *
     * @param string $value
     *
     * @return string
     */
    public function getGamutType($value)
    {
        return substr($value, 0, strpos($value, ':'));
    }

    /**
     * Tests if a string seems to be a gamuts' stack reference
     *
     * @param string $value
     *
     * @return bool
     */
    public function isGamutStackName($value)
    {
        return (bool) (0 !== preg_match('/^[a-zA-Z0-9_]+_gamut$/i', $value));
    }

    /**
     * Gets the "base name" of a gamut entry in "filter:class", "tools" or "custom class"
     *
     * @param string $gamut
     *
     * @return string
     */
    public function getGamutBaseName($gamut)
    {
        switch ($this->getGamutType($gamut)) {
            case self::FILTER_ALIAS:
                @[$base, $class, $method] = explode(':', $gamut);
                return self::FILTER_ALIAS . ':' . $class;
                break;
            case self::TOOL_ALIAS:
                return self::TOOL_ALIAS;
                break;
            default:
                @[$class, $method] = explode(':', $gamut);
                return $class;
        }
    }

    /**
     * Gets an array of all defined gamuts
     *
     * @return array
     */
    public function getAllGamuts()
    {
        if (empty($this->all_gamuts)) {
            $this->all_gamuts = [];

            foreach (Kernel::get('config')->getAll() as $var => $val) {
                if ($this->isGamutStackName($var)) {
                    foreach ($val as $item => $priority) {
                        if (!$this->isGamutStackName($item)) {
                            $name = $this->getGamutBaseName($item);
                            if (!in_array($name, $this->all_gamuts, true)) {
                                $this->all_gamuts[] = $name;
                            }
                        }
                    }
                }
            }
        }

        return $this->all_gamuts;
    }

    /**
     * Gets an array of all defined gamuts as keys
     *
     * @see self::getAllGamuts()
     *
     * @return array
     */
    public function getAllGamutsReversed()
    {
        return array_flip($this->getAllGamuts());
    }

    /**
     * Tests if a gamut is enabled
     *
     * This will always return `true` for tools and a gamut
     * stack name.
     *
     * @param string $gamut
     *
     * @return bool
     */
    public function isGamutEnabled($gamut)
    {
        return (bool) (
            $this->isGamutStackName($gamut) ||
            $this->getGamutBaseName($gamut) === self::TOOL_ALIAS ||
            array_key_exists($this->getGamutBaseName($gamut), $this->getAllGamuts())
        );
    }

    /**
     * Runs a list of gamuts by priority on a content
     *
     * @param   array   $gamuts     The gamuts names to execute
     * @param   string  $text       The text for gamuts execution
     *
     * @return  string
     */
    public function runGamuts(array $gamuts, $text = null)
    {
        if (!empty($gamuts)) {
            asort($gamuts);
            foreach ($gamuts as $method => $priority) {
                $text = self::runGamut($method, $text);
            }
        }
        return $text;
    }

    /**
     * Runs a specific method of a list of gamuts by priority on a content
     *
     * @param   array   $gamuts     The gamuts names to execute
     * @param   string  $method     The method name to execute in each gamut
     * @param   string  $text       The text for gamuts execution
     *
     * @return  string
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if `$method` is not a string
     */
    public function runGamutsMethod(array $gamuts, $method, $text = null)
    {
        if (!is_string($method)) {
            throw new UnexpectedValueException(
                sprintf('Gamuts method must be a string, <%s> given', gettype($method))
            );
        }

        if (!empty($gamuts)) {
            asort($gamuts);
            foreach ($gamuts as $_gmt => $priority) {
                $_text = $text;
                try {
                    $text = self::runGamut($_gmt, $text, $method);
                } catch (\Exception $e) {
                    $text = $_text;
                }
            }
            unset($_text);
        }

        return $text;
    }

    /**
     * Runs a single gamut on a content
     *
     * @param   string  $gamut      The gamut name to execute
     * @param   string  $text       The text for gamuts execution
     * @param   string  $_method    The method name to execute in each gamut
     *
     * @return  string
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if `$gamut` is not a string
     */
    public function runGamut($gamut, $text = null, $_method = null)
    {
        if (!is_string($gamut)) {
            throw new UnexpectedValueException(
                sprintf('Gamut name must be a string, <%s> given', gettype($gamut))
            );
        }

        // if calling back a gamuts stack
        if ($this->isGamutStackName($gamut)) {
            return $this->runGamuts($this->getGamutStack($gamut), $text);
        }

        switch ($this->getGamutType($gamut)) {
            case self::FILTER_ALIAS:
                @[$base, $class, $method] = explode(':', $gamut);
                return $this->_runGamutFilterMethod($class, $_method ?: $method, $text);
                break;
            case self::TOOL_ALIAS:
                @[$base, $method] = explode(':', $gamut);
                return $this->_runToolsMethod($method, $text);
                break;
            default:
                @[$class, $method] = explode(':', $gamut);
                return $this->_runClassMethod($class, $_method ?: $method, $text);
        }
    }

    /**
     * Actually runs a gamut's method on a content
     *
     * @param   string  $gamut      The gamut name to execute
     * @param   string  $text       The text for gamuts execution
     * @param   string  $method     The method name to execute in each gamut
     *
     * @return  string
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if `$gamut` can not be found
     */
    protected function _runGamutFilterMethod($gamut, $method, $text)
    {
        $obj_name = self::FILTER_NAMESPACE . '\\' . $gamut;

        if (!$this->isCached($obj_name)) {
            if (!class_exists($obj_name)) {
                throw new UnexpectedValueException(
                    sprintf('Filter class "%s" not found', $obj_name)
                );
            }

            $_obj = new $obj_name();
            Kernel::validate($_obj, Kernel::TYPE_GAMUT, $obj_name);
            $this->setCache($obj_name, $_obj);
        }

        return $this->_runClassMethod($obj_name, $method, $text);
    }

    /**
     * Actually runs a tools method
     *
     * @param string $method
     * @param string $text
     *
     * @return string
     *
     * @see self::_runClassMethod()
     */
    protected function _runToolsMethod($method, $text)
    {
        return $this->_runClassMethod(self::TOOL_CLASS, $method, $text);
    }

    /**
     * Global gamut's method runner
     *
     * @param string $class
     * @param string $method
     * @param string $text
     *
     * @return string
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if `$gamut` doesn't implement the required method
     *              or class can not be found
     */
    protected function _runClassMethod($class, $method, $text)
    {
        if ($this->isCached($class)) {
            $_obj = $this->getCache($class);
        } else {
            if (!class_exists($class)) {
                throw new UnexpectedValueException(
                    sprintf('Gamut class "%s" not found', $class)
                );
            }

            $_obj = new $class();
            Kernel::validate($_obj, Kernel::TYPE_GAMUT, $class);
            $this->setCache($class, $_obj);
        }

        $method = $method ?: $_obj->getDefaultMethod();

        if (!method_exists($_obj, $method)) {
            throw new UnexpectedValueException(
                sprintf('Method "%s" does not exist in class "%s"', $method, $class)
            );
        }

        $text = call_user_func([$_obj, $method], $text);
        return $text;
    }
}
