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

use \MarkdownExtended\Registry;
use \MarkdownExtended\Config;
use \MarkdownExtended\OutputFormatBag;
use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * PHP Markdown Extended Mother Class
 *
 * This is the global *MarkdownExtended* class and process. It contains mostly
 * static methods that can be called from anywhere writing something like:
 *
 *     MarkdownExtended::my_method();
 *
 * LICENSE
 *
 * Mardown
 * Copyright © 2004-2006, John Gruber
 * http://daringfireball.net/
 * All rights reserved.
 *
 * MultiMarkdown
 * Copyright © 2005-2009 Fletcher T. Penney
 * http://fletcherpenney.net/
 * All rights reserved.
 *
 * PHP Markdown & Extra
 * Copyright © 2004-2012 Michel Fortin
 * http://michelf.com/projects/php-markdown/
 * All rights reserved.
 *
 * Markdown Extended
 * Copyright © 2008-2013 Pierre Cassat & contributors
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted 
 * provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this list of conditions 
 *   and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice, this list of conditions 
 *   and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * - Neither the names “Markdown”, "Markdown Extra", "Multi Markdown", "Markdown Extended" nor the names of
 *   their contributors may be used to endorse or promote products derived from this software without specific
 *   prior written permission.
 *
 * This software is provided by the copyright holders and contributors “as is” and any express or 
 * implied warranties, including, but not limited to, the implied warranties of merchantability and 
 * fitness for a particular purpose are disclaimed. In no event shall the copyright owner or contributors 
 * be liable for any direct, indirect, incidental, special, exemplary, or consequential damages 
 * (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; 
 * or business interruption) however caused and on any theory of liability, whether in contract, 
 * strict liability, or tort (including negligence or otherwise) arising in any way out of the use of 
 * this software, even if advised of the possibility of such damage.
 *
 * @package MarkdownExtended
 */
final class MarkdownExtended
    implements MDE_API\KernelInterface
{

    /**
     * Class infos ; can be automatically updated by `pre-commit-hook.sh`
     */
    const MDE_NAME      = 'PHP Markdown Extended';
    const MDE_VERSION   = '0.1-gamma4';
    const MDE_DATE      = '26-12-2014';
    const MDE_SOURCES   = 'http://github.com/piwi/markdown-extended';

    /**
     * @var  array  table of \MarkdownExtended\MarkdownExtended instances
     */
    private static $_instances = array();

    /**
     * @var  \MarkdownExtended\Registry  Dependencies registry
     */
    private $registry;

    /**
     * @var  array   Table of \MarkdownExtended\Content processed items
     */
    private $contents;

    /**
     * @var  mixed
     */
    private $current;

    /**
     * @var  array
     */
    private $global_options;

    /**
     * Initialize the registry and flush the contents stack
     *
     * The best practice is to use the class as a singleton calling `getInstance()` or
     * `create()`.
     *
     * @param   null/array  $options
     */
    public function __construct(array $options = null)
    {
        $this->registry = new Registry(false,false);
        if (!empty($options)) {
            $this->global_options = $options;
        }
        // load dependencies
        $_registry = MDE_API::factory('Registry');
        $this->registry->set('Registry', $_registry);
        $_config = MDE_API::factory('Config');
        $this->registry->set('Config', $_config);
        // init contents collection
        $this->contents = array();
    }

    /**
     * Get a Markdown Extended instance (last created one by default)
     *
     * @param   int         $instance_id    The ID of the MDE instance to get
     * @param   null/array  $options
     * @return  \MarkdownExtended\MarkdownExtended
     */
    public static function getInstance($instance_id = null, array $options = null)
    {
        if (!is_null($instance_id)) {
            if (!isset(self::$_instances[$instance_id])) {
                return self::create($options, $instance_id);
            }
            $return = self::$_instances[$instance_id];
        } else {
            if (empty(self::$_instances)) {
                return self::create($options);
            }
            $return = end(self::$_instances);
        }
        return $return;
    }

    /**
     * Create a new MDE instance
     *
     * @param   null/array  $options
     * @param   int         $instance_id    The ID of the MDE instance to create
     * @return  \MarkdownExtended\MarkdownExtended
     */
    public static function create(array $options = null, $instance_id = null)
    {
        if (!is_null($instance_id)) {
            self::$_instances[$instance_id] = new self($options);
            $return = self::$_instances[$instance_id];
        } else {
            self::$_instances[] = new self($options);
            $return = end(self::$_instances);
        }
        return $return;
    }

// ----------------------------------
// CONTENTS PROCESSOR
// ----------------------------------

    /**
     * Transform a Markdown source string
     *
     * @param   string          $source
     * @param   string/array    $parser_options
     * @param   string/null     $key
     * @param   bool            $secondary  Set it to `true` if parsed content may not be stored as current one
     * @return  \MarkdownExtended\API\ContentInterface
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the parser class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the parser class is not valid
     */
    public static function transformString($source, $parser_options = null, $key = null, $secondary = false)
    {
        $_this = self::getInstance();
        try {
            $content = MDE_API::factory('Content', array($source));
            $content->setId($key);
            if (!empty($_this->global_options)) {
                if (is_null($parser_options)) $parser_options = array();
                if (!is_array($parser_options)) $parser_options = array($parser_options);
                $parser_options = array_merge($_this->global_options, $parser_options);
            }
            $parser = $_this->get('Parser', array($parser_options), MDE_API::FAIL_WITH_ERROR);
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        }
        return $parser
            ->parse($content, $secondary)
            ->getContent();
    }
    
    /**
     * Transform a Markdown source file content
     *
     * @param   string          $filename
     * @param   string/array    $parser_options
     * @param   string/null     $key
     * @param   bool            $secondary  Set it to `true` if parsed content may not be stored as current one
     * @return  \MarkdownExtended\API\ContentInterface
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the parser class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the parser class is not valid
     */
    public static function transformSource($filename, $parser_options = null, $key = null, $secondary = false)
    {
        $_this = self::getInstance();
        try {
            $content = MDE_API::factory('Content', array(null, $filename));
            $content->setId($key);
            if (!empty($_this->global_options)) {
                if (is_null($parser_options)) $parser_options = array();
                if (!is_array($parser_options)) $parser_options = array($parser_options);
                $parser_options = array_merge($_this->global_options, $parser_options);
            }
            $parser = $_this->get('Parser', array($parser_options), MDE_API::FAIL_WITH_ERROR);
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        }
        return $parser
            ->parse($content, $secondary)
            ->getContent();
    }
    
// --------------
// CONTENTS
// --------------

    /**
     * Add a new processed content in the contents stack
     *
     * @param   \MarkdownExtended\API\ContentInterface   $content
     * @param   bool    $secondary          Set it to `true` if parsed content may not be stored as current one
     * @return  void
     */
    public static function addProcessedContent(MDE_API\ContentInterface $content, $secondary = false)
    {
        $_this = self::getInstance();
        $_this->contents[$content->getId()] =& $content;
        if (!$secondary) $_this->current = $content->getId();
    }
    
    /**
     * Get a processed content, current by default
     *
     * @param   mixed   $id     The id of the content to get
     * @return  \MarkdownExtended\API\ContentInterface
     */
    public static function getContent($id = null)
    {
        $_this = self::getInstance();
        if (is_null($id)) $id = $_this->current;
        return isset($_this->contents[$id]) ? $_this->contents[$id] : null;
    }
    
    /**
     * Get a full version of a processed content, current by default
     *
     * @param   mixed   $id     The id of the content to get
     * @return  string
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the OutputFormatter class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the OutputFormatter class is not valid
     */
    public static function getFullContent($id = null)
    {
        $content = self::getContent($id);
        try {
            $output_bag = self::getInstance()
                ->get('OutputFormatBag', null, MDE_API::FAIL_WITH_ERROR);
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        }
        return $output_bag->getHelper()
            ->getFullContent($content, $output_bag->getFormatter());
    }
    
    /**
     * Get current processed content in a Templater
     *
     * @param   null/array   $config
     * @return  \MarkdownExtended\API\TemplaterInterface
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the Templater class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the Templater class is not valid
     */
    public static function getTemplater(array $config = null)
    {
        try {
            $templater = self::getInstance()
                ->get('Templater', $config, MDE_API::FAIL_WITH_ERROR);
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        }
        return $templater->load(self::getContent());
    }
    
// --------------
// REGISTRY USER INTERFACE
// --------------

    /**
     * Load a dependency
     *
     * @param   string  $class_name     The class name to instanciate ; will be completed with current namespace if necessary
     * @return  bool
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException it the class doesn't exist
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException it the creation of the object throws an exception
     */
    public static function load($class_name)
    {
        if (!class_exists($class_name)) {
            $class_name = MDE_API::getAbsoluteClassname($class_name);
        }
        if (class_exists($class_name)) {
            try {
                if (MDE_API::isValid($class_name)) {
                    return true;
                }
            } catch (MDE_Exception\UnexpectedValueException $e) {
                throw $e;
            }
        } else {
            throw new MDE_Exception\InvalidArgumentException(
                sprintf('Class "%s" not found!', $class_name)
            );
        }
    }

    /**
     * Build, retain and get a dependency instance
     *
     * @param   string      $class_name     The class name to instanciate ; will be completed with current namespace if necessary
     * @param   null/array  $params         Parameters to use for `$class` object instanciation
     * @param   null/string $type           The type of API object to load
     * @return  object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the object creation sent an error
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the registry entry can't be set
     */
    public static function factory($class_name, $params = null, $type = null)
    {
        try {
            $_obj = MDE_API::factory($class_name, $params, $type);
            self::getInstance()->registry->set($class_name, $_obj);
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        }
        return $_obj;
    }

    /**
     * Get a loader object from registry / load it if absent
     *
     * @param   string  $class_name     The class name to instantiate ; will be completed with current namespace if necessary
     * @param   array   $params         Parameters to use for `$class` object instantiation
     * @param   int     $flag
     * @return  object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the class can not be found
     * @throws  \MarkdownExtended\Exception\RuntimeException if the object creation sent an error
     */
    public static function get($class_name, $params = null, $flag = MDE_API::FAIL_GRACEFULLY)
    {
        $_class_name_bkp = MDE_API::getRelativeClassname($class_name);
        $obj = self::getInstance()->registry->get($_class_name_bkp);
        if (!empty($obj)) {
            return $obj;
        } else {
            $_fact = null;
            try {
                $_fact = self::factory($_class_name_bkp, $params);
            } catch (MDE_Exception\InvalidArgumentException $e) {
                if ($flag & MDE_API::FAIL_WITH_ERROR) {
                    throw $e;
                }
            } catch (MDE_Exception\RuntimeException $e) {
                if ($flag & MDE_API::FAIL_WITH_ERROR) {
                    throw $e;
                }
            }
            return $_fact;
        }
    }

// --------------
// ALIASES
// --------------

    /**
     * Get a configuration entry from registry
     *
     * @param   string  $var
     * @return  mixed
     */
    public static function getConfig($var)
    {
        return self::get('Config')->get($var);
    }

    /**
     * Set a configuration entry in registry
     *
     * @param   string          $var
     * @param   mixed           $val
     * @param   null|string     $stack
     * @return  mixed
     */
    public static function setConfig($var, $val, $stack = null)
    {
        if (!empty($stack)) {
            $config_stack = self::getConfig($stack);
            if (empty($config_stack)) $config_stack = array();
            $config_stack[$var] = $val;
            $var = $stack;
            $val = $config_stack;
        }
        return self::get('Config')->set($var, $val);
    }

    /**
     * Add to a configuration entry in registry
     *
     * @param   string  $var
     * @param   mixed   $val
     * @return  mixed
     */
    public static function addConfig($var, $val)
    {
        return self::get('Config')->add($var, $val);
    }

    /**
     * Get a parser entry from registry
     *
     * @param   string  $var
     * @return  mixed
     */
    public static function getVar($var)
    {
        return self::get('Registry')->get($var);
    }

    /**
     * Set a parser entry in registry
     *
     * @param   string  $var
     * @param   mixed   $val
     * @return  mixed
     */
    public static function setVar($var, $val)
    {
        return self::get('Registry')->set($var, $val);
    }

    /**
     * Add to a parser entry in registry
     *
     * @param   string  $var
     * @param   mixed   $val
     * @return  mixed
     */
    public static function addVar($var, $val)
    {
        return self::get('Registry')->add($var, $val);
    }

    /**
     * Unset a parser entry in registry
     *
     * @param   string  $var
     * @param   mixed   $val
     * @return  mixed
     */
    public static function unsetVar($var, $val = null)
    {
        return self::get('Registry')->remove($var, $val);
    }

}

// Endfile
