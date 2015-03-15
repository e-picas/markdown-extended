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

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * The API defines all required interfaces
 *
 * @package MarkdownExtended\API
 */
class API
{

    /**
     * @var     bool
     * @private
     */
    private static $__debug    = false;

    /**
     * @var     integer     Flag to use for silent errors
     */
    const FAIL_GRACEFULLY = 0;

    /**
     * @var     integer     Flag to use to throw errors
     */
    const FAIL_WITH_ERROR = 1;

    /**
     * @var     string
     */
    const MDE_NAMESPACE = 'MarkdownExtended';
    
    /**
     * @var     string
     */
    const KERNEL_INTERFACE = '\MarkdownExtended\API\KernelInterface';
    
    /**
     * @var     string
     */
    const CONTENT_INTERFACE = '\MarkdownExtended\API\ContentInterface';

    /**
     * @var     string
     */
    const COLLECTION_INTERFACE = '\MarkdownExtended\API\CollectionInterface';

    /**
     * @var     string
     */
    const OUTPUT_FORMAT_INTERFACE = '\MarkdownExtended\API\OutputFormatInterface';
    
    /**
     * @var     string
     */
    const OUTPUT_FORMAT_HELPER_INTERFACE = '\MarkdownExtended\API\OutputFormatHelperInterface';
    
    /**
     * @var     string
     */
    const PARSER_INTERFACE = '\MarkdownExtended\API\ParserInterface';
    
    /**
     * @var     string
     */
    const TEMPLATER_INTERFACE = '\MarkdownExtended\API\TemplaterInterface';

    /**
     * @var     string
     */
    const GRAMMAR_GAMUT_INTERFACE = '\MarkdownExtended\API\GamutInterface';

    /**
     * Get the internal MDE objects
     *
     * @return  array
     */
    private static function __getInternals()
    {
        return array(
            'kernel'                => '\MarkdownExtended\MarkdownExtended',
            'registry'              => '\MarkdownExtended\Registry',
            'config'                => '\MarkdownExtended\Config',
            'output_format_bag'     => '\MarkdownExtended\OutputFormatBag',
        );
    }

    /**
     * Get the current API
     *
     * @return  array
     */
    private static function __getApi()
    {
        return array(
            'content'               => self::CONTENT_INTERFACE,
            'content_collection'    => self::COLLECTION_INTERFACE,
            'output_format'         => self::OUTPUT_FORMAT_INTERFACE,
            'output_format_helper'  => self::OUTPUT_FORMAT_HELPER_INTERFACE,
            'parser'                => self::PARSER_INTERFACE,
            'templater'             => self::TEMPLATER_INTERFACE,
            'grammar\filter'        => self::GRAMMAR_GAMUT_INTERFACE,
            'grammar\tool'          => self::GRAMMAR_GAMUT_INTERFACE,
        );
    }

    /**
     * Build an API object validating it if needed
     *
     * @param   string      $name           The class name to instanciate ; will be completed with current namespace if necessary
     * @param   null|array  $params         Parameters to use for `$class` object instanciation
     * @param   null|string $type           The type of API object to load
     * @return  object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the object creation sent an error
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if the class seems not valid
     */
    public static function factory($name, $params = null, $type = null)
    {
        self::debug("<hr />", "FACTORY : ", func_get_args());

        $api        = self::__getApi();
        $internals  = self::__getInternals();
        if (is_null($type)) {
            $type   = self::getRelativeClassname($name);
            $type   = strtolower($type);
        }
        self::debug("searching to load ".$name." of type ".$type);

        // if internal, return
        if (array_key_exists($type, $internals)) {
            $class_name = $internals[$type];
            try {
                $_cls = new \ReflectionClass($class_name);
                $_obj = $_cls->newInstanceArgs(is_null($params) ? array() : $params);
            } catch (\ReflectionException $e) {
                throw new MDE_Exception\RuntimeException(sprintf(
                    "An error occurred trying to create a '%s' instance: '%s'!",
                    $name, $e->getMessage()
                ));
            }
            return $_obj;
        }

        // get the class name to create
        if (array_key_exists($type, $api)) {
            // get the class name from config
            $config = MarkdownExtended::getConfig($type.'_class');
            $class_name = (!empty($config)) ? $config : $name;
            // if class does not exists, try in MDE namespace
            if (!class_exists($class_name)) {
                $mde_class = self::getAbsoluteClassname($class_name);
                if (class_exists($mde_class)) $class_name = $mde_class;
            }
            try {
                self::isValid($class_name, $type);
            } catch (MDE_Exception\UnexpectedValueException $e) {
                throw $e;
            }

        } else {
            $class_name = self::getAbsoluteClassname($name);
        }

        // try to create the object
        self::debug("=> will create object of class ".$class_name);
        $_obj = null;
        if (class_exists($class_name)) {
            try {
                $_cls = new \ReflectionClass($class_name);
                $_obj = $_cls->newInstanceArgs(is_null($params) ? array() : $params);
            } catch (\ReflectionException $e) {
                throw new MDE_Exception\RuntimeException(sprintf(
                    "An error occurred trying to create a '%s' instance: '%s'!",
                    $name, $e->getMessage()
                ));
            }
        } else {
            throw new MDE_Exception\InvalidArgumentException(sprintf(
                "Class '%s' not found!", $class_name
            ));
        }

        self::debug("=> returning object of class ".get_class($_obj));
        return $_obj;
    }

    /**
     * Validate an API object checking its implemented interfaces
     *
     * @param   string/object   $object
     * @param   string          $type
     * @param   bool            $bool   Return a boolean or an Exception on failure
     * @return  bool
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException
     */
    public static function isValid($object, $type = null, $bool = false)
    {

        self::debug("isValid ? ", func_get_args());
        $ok         = false;
        $api        = self::__getApi();
        $class_name = self::getAbsoluteClassname(
            is_object($object) ? get_class($object) : $object
        );
        if (!empty($class_name) && class_exists($class_name)) {
            if (is_null($type)) {
                $type = self::getRelativeClassname($class_name);
            }
            $type = strtolower($type);
            if (array_key_exists($type, $api) && !is_null($api[$type])) {
                self::debug("=> validating if ".$class_name." implements ". $api[$type]);
                $api_interface  = $api[$type];
                $interfaces     = class_implements($class_name);
                $ok = (in_array($api_interface, $interfaces) || in_array(trim($api_interface, '\\'), $interfaces));
                if (false===$ok && true!==$bool) {
                    throw new MDE_Exception\UnexpectedValueException(
                        sprintf('Class "%s" must implement API interface "%s"!', $class_name, $api_interface)
                    );
                }
            } elseif (array_key_exists($type, $api) && is_null($api[$type])) {
                $ok = true;
            } elseif (true!==$bool) {
                throw new MDE_Exception\UnexpectedValueException(
                    sprintf('API type "%s" (guessed from class "%s") does not exist!', $type, $class_name)
                );
            }
        }
        self::debug("=> validation returns : ".$ok);
        return $ok;
    }
    
    /**
     * Get a class name without the current namespace if present
     *
     * @param   string  $class_name
     * @return  string
     */
    public static function getRelativeClassname($class_name)
    {
        if (strstr($class_name, self::MDE_NAMESPACE)) {
            return trim(
                str_replace(self::MDE_NAMESPACE.'\\', '', $class_name)
            , '\\');
        }
        return $class_name;
    }

    /**
     * Get a class name with the current namespace
     *
     * @param   string  $class_name
     * @return  string
     */
    public static function getAbsoluteClassname($class_name)
    {
        if (!strstr($class_name, self::MDE_NAMESPACE) && !class_exists($class_name)) {
            return '\\'.self::MDE_NAMESPACE.'\\'.$class_name;
        }
        return $class_name;
    }

    /**
     * Debug infos on screen during parsing
     */
    public static function debug()
    {
        $nl = (strpos(php_sapi_name(),'cli')===false) ? "<br />" : PHP_EOL;
        if (true===self::$__debug) {
            foreach (func_get_args() as $arg) {
                switch (gettype($arg)) {
                    case 'string': case 'integer': case 'double':
                        echo $nl, $arg; break;
                    case 'boolean': case 'array':
                        echo $nl, var_export($arg,1); break;
                    case 'object':
                        if (is_callable(array($arg, '__toString'))) {
                            echo $nl, $arg;
                        } else {
                            echo $nl; var_dump($arg);
                        }
                        break;
                    default: break;
                }
            }
        }
    }

}

// Endfile
