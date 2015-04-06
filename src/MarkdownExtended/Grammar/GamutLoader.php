<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Util\CacheRegistry;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Exception\InvalidArgumentException;
use \MarkdownExtended\Exception\UnexpectedValueException;
use \MarkdownExtended\Exception\BadMethodCallException;
use \MarkdownExtended\Exception\DomainException;

/**
 * Central class to execute Filters and Tools methods on a content
 *
 * It can handle a list of gamuts, execute a specific method and run a single gamut.
 * @package MarkdownExtended\Grammar
 */
class GamutLoader
    extends CacheRegistry
{

    const FILTER_ALIAS      = 'filter';
    const TOOLS_ALIAS       = 'tools';
    const FILTER_NAMESPACE  = 'MarkdownExtended\Grammar\Filter';
    const TOOLS_CLASS       = 'MarkdownExtended\Grammar\Tools';

    /**
     * @var array
     */
    protected $all_gamuts;

    /**
     * Set the gamuts aliases from config
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getGamutStack($name)
    {
        if (!$this->isGamutStackName($name)) {
            throw new BadMethodCallException(
                sprintf('A gamut stack name must follow a form like "%%_gamut", "%s" given', $name)
            );
        }

        $stack = Kernel::getConfig($name);
        if (empty($stack)) {
            throw new InvalidArgumentException(
                sprintf('Unknown gamut stack "%s"', $name)
            );
        }

        return $stack;
    }

    public function getGamutType($value)
    {
        return substr($value, 0, strpos($value, ':'));
    }

    public function isGamutStackName($value)
    {
        return (bool) (0 !== preg_match('/^[a-zA-Z0-9_]+_gamut$/i', $value));
    }

    public function getGamutBaseName($gamut)
    {
        switch ($this->getGamutType($gamut)) {
            case self::FILTER_ALIAS:
                @list($base, $class, $method) = explode(':', $gamut);
                return self::FILTER_ALIAS . ':' . $class;
                break;
            case self::TOOLS_ALIAS:
                return self::TOOLS_ALIAS;
                break;
            default:
                @list($class, $method) = explode(':', $gamut);
                return $class;
        }
    }

    public function getAllGamuts()
    {
        if (empty($this->all_gamuts)) {
            $this->all_gamuts = array();

            foreach (Kernel::get('config')->getAll() as $var=>$val) {
                if ($this->isGamutStackName($var)) {
                    foreach ($val as $item=>$priority) {
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

    public function getAllGamutsReversed()
    {
        return array_flip($this->getAllGamuts());
    }

    public function isGamutEnabled($gamut)
    {
        return (bool) (
            $this->isGamutStackName($gamut) ||
            $this->getGamutBaseName($gamut) === self::TOOLS_ALIAS ||
            array_key_exists($this->getGamutBaseName($gamut), $this->getAllGamuts())
        );
    }

    /**
     * Run a table of gamuts by priority
     *
     * @param   array   $gamuts     The gamuts names to execute
     * @param   string  $text       The text for gamuts execution
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
     * Run a table of gamuts for a specific method by priority
     *
     * @param   array   $gamuts     The gamuts names to execute
     * @param   string  $method     The method name to execute in each gamut
     * @param   string  $text       The text for gamuts execution
     * @return  string
     * @throws  \MarkdownExtended\Exception\BadMethodCallException if $method is not a string
     */
    public function runGamutsMethod(array $gamuts, $method, $text = null)
    {
        if (!is_string($method)) {
            throw new BadMethodCallException(
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
     * Run a single gamut
     *
     * @param   string  $gamut      The gamut name to execute
     * @param   string  $text       The text for gamuts execution
     * @param   string  $_method    The method name to execute in each gamut
     * @return  string
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if $gamut doesn't implement the required method
     * @throws  \MarkdownExtended\Exception\DomainException if $gamut doesn't implement interface `MarkdownExtended\Grammar\GamutInterface`
     * @throws  \MarkdownExtended\Exception\BadMethodCallException if the gamut class name is not a string
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if gamut not defined
     */
    public function runGamut($gamut, $text = null, $_method = null)
    {
        if (!is_string($gamut)) {
            throw new BadMethodCallException(
                sprintf('Gamut name must be a string, <%s> given', gettype($gamut))
            );
        }

        // if calling back a gamuts stack
        if ($this->isGamutStackName($gamut)) {
            return $this->runGamuts($this->getGamutStack($gamut), $text);
        }

        switch ($this->getGamutType($gamut)) {
            case self::FILTER_ALIAS:
                @list($base, $class, $method) = explode(':', $gamut);
                return $this->_runGamutFilterMethod($class, $_method ?: $method, $text);
                break;
            case self::TOOLS_ALIAS:
                @list($base, $method) = explode(':', $gamut);
                return $this->_runToolsMethod($method, $text);
                break;
            default:
                @list($class, $method) = explode(':', $gamut);
                return $this->_runClassMethod($class, $_method ?: $method, $text);
        }
    }

    protected function _runGamutFilterMethod($gamut, $method, $text)
    {
        $obj_name = self::FILTER_NAMESPACE . '\\' . $gamut;

        if (!$this->isCached($obj_name)) {
            if (!class_exists($obj_name)) {
                throw new UnexpectedValueException(
                    sprintf('Filter class "%s" not found', $obj_name)
                );
            }

            $_obj = new $obj_name;
            Kernel::validate($_obj, Kernel::TYPE_GAMUT, $obj_name);
            $this->setCache($obj_name, $_obj);
        }

        return $this->_runClassMethod($obj_name, $method, $text);
    }

    protected function _runToolsMethod($method, $text)
    {
        return $this->_runClassMethod(self::TOOLS_CLASS, $method, $text);
    }

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

            $_obj = new $class;
            Kernel::validate($_obj, Kernel::TYPE_GAMUT, $class);
            $this->setCache($class, $_obj);
        }

        $method = $method ?: $_obj->getDefaultMethod();

        if (!method_exists($_obj, $method)) {
            throw new UnexpectedValueException(
                sprintf('Method "%s" does not exist in class "%s"', $method, $class)
            );
        }

        $text = call_user_func(array($_obj, $method), $text);
        return $text;
    }

}

// Endfile