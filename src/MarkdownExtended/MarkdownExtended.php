<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended;

use MarkdownExtended\Registry,
    MarkdownExtended\Config,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * PHP Markdown Extended Mother Class
 *
 * This is the global *MarkdownExtended* class and process. It contains mostly
 * static methods that can be called from anywhere writing something like:
 *
 *     MarkdownExtended::my_method();
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
 * - Neither the name “Markdown” nor the names of its contributors may be used to endorse or promote 
 *   products derived from this software without specific prior written permission.
 *
 * This software is provided by the copyright holders and contributors “as is” and any express or 
 * implied warranties, including, but not limited to, the implied warranties of merchantability and 
 * fitness for a particular purpose are disclaimed. In no event shall the copyright owner or contributors 
 * be liable for any direct, indirect, incidental, special, exemplary, or consequential damages 
 * (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; 
 * or business interruption) however caused and on any theory of liability, whether in contract, 
 * strict liability, or tort (including negligence or otherwise) arising in any way out of the use of 
 * this software, even if advised of the possibility of such damage.
 */
final class MarkdownExtended
{

    /**
     * Class infos
     */
    const MDE_NAME = 'PHP Markdown Extended';
    const MDE_VERSION = '0.0.3';
    const MDE_SOURCES = 'http://github.com/atelierspierrot/markdown-extended';

    /**
     * Default full options INI file
     */
    const FULL_CONFIGFILE = 'markdown_config.full.ini';

    /**
     * Default simple options INI file (i.e. for fields)
     */
    const SIMPLE_CONFIGFILE = 'markdown_config.simple.ini';

    /**
     * @static \MarkdownExtended\MarkdownExtended
     */
    private static $_instance;

    /**
     * Dependencies registry
     * @static \MarkdownExtended\Registry
     */
    private static $registry;

    /**
     * @static array of \MarkdownExtended\Content processed items
     */
    private static $contents;

    /**
     * @static misc
     */
    private static $current;

    /**
     * Initialize the registry and flush the contents stack
     *
     * The best practice is to use the class as a singleton calling `getInstance()` or
     * `create()`.
     */
    public function __construct()
    {
        self::$registry = new Registry(false,false);
        // load dependencies
        self::factory('Registry');
        self::factory('Config');
        self::$contents = array();
    }

    /**
     * Get the Markdown Extended instance
     * @return self
     */
    public static function getInstance()
    {
        if (empty(self::$_instance)) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }

    /**
     * Create a new singleton instance
     * @return self
     */
    public static function create()
    {
        self::$_instance = new self;
        return self::$_instance;
    }

// ----------------------------------
// CONTENTS PROCESSOR
// ----------------------------------

    /**
     * Transform a Markdown source string
     *
     * @param string $source
     * @param string|array $parser_options
     * @param string|null $key
     * @param bool $secondary Set it to `true` if parsed content may not be stored as current one
     *
     * @return \MarkdownExtended\Content
     */
    public static function transformString($source, $parser_options = null, $key = null, $secondary = false)
    {
        $content = new \MarkdownExtended\Content($source);
        $content->setId($key);
        return self::getInstance()
            ->get('Parser', $parser_options)
            ->parse($content, $secondary)
            ->getContent();
    }
    
    /**
     * Transform a Markdown source file content
     *
     * @param string $filename
     * @param string|array $parser_options
     * @param string|null $key
     * @param bool $secondary Set it to `true` if parsed content may not be stored as current one
     *
     * @return \MarkdownExtended\Content
     */
    public static function transformSource($filename, $parser_options = null, $key = null, $secondary = false)
    {
        $content = new \MarkdownExtended\Content(null, $filename);
        $content->setId($key);
        return self::getInstance()
            ->get('Parser', $parser_options)
            ->parse($content, $secondary)
            ->getContent();
    }
    
// --------------
// CONTENTS
// --------------

    /**
     * Add a new processed content in the contents stack
     *
     * @param object MarkdownExtended\Content object
     * @param bool $secondary Set it to `true` if parsed content may not be stored as current one
     */
    public static function addProcessedContent(Content $content, $secondary = false)
    {
        self::$contents[$content->getId()] =& $content;
        if (!$secondary) self::$current = $content->getId();
    }
    
    /**
     * Get a processed content, current by default
     *
     * @param misc $id The id of the content to get
     *
     * @return object \MarkdownExtended\Content
     */
    public static function getContent($id = null)
    {
        if (is_null($id)) $id = self::$current;
        return isset(self::$contents[$id]) ? self::$contents[$id] : null;
    }
    
    /**
     * Get a full version of a processed content, current by default
     *
     * @param misc $id The id of the content to get
     *
     * @return string
     */
    public static function getFullContent($id = null)
    {
        if (is_null($id)) $id = self::$current;
        $content = isset(self::$contents[$id]) ? self::$contents[$id] : null;
        $output_bag = self::get('OutputFormatBag');
        return $output_bag->getHelper()
            ->getFullContent($content, $output_bag->getFormater());
    }
    
    /**
     * Get current processed content in a Templater
     *
     * @param array $config
     *
     * @return object \MarkdownExtended\Templater
     */
    public static function getTemplater(array $config = null)
    {
        return self::get('Templater', $config)->load(self::getContent());
    }
    
// --------------
// REGISTRY USER INTERFACE
// --------------

    /**
     * Load a dependency
     *
     * @param string $class The class name to instanciate ; will be completed with current
     *                      namespace if necessary
     *
     * @throws MarkdownExtended\Exception\InvalidArgumentException it the class doesn't exist
     *
     * @return bool
     */
    public static function load($class)
    {
        $class = MDE_Helper::getAbsoluteClassname($class);
        if (class_exists($class)) return true;
        throw new MDE_Exception\InvalidArgumentException(
            sprintf('Class "%s" not found in "%s"!', $class, $_f)
        );
    }

    /**
     * Build, retain and get a dependency instance
     *
     * @param string $class The class name to instanciate ; will be completed with current
     *                      namespace if necessary
     * @param array $params Parameters to use for `$class` object instanciation
     *
     * @return object
     */
    public static function factory($class, $params = null)
    {
        $class = MDE_Helper::getAbsoluteClassname($class);
        $class_name = MDE_Helper::getRelativeClassname($class);
        self::load($class);
        $_obj = !is_null($params) ? new $class($params) : new $class;
        self::$registry->set($class_name, $_obj);
        return $_obj;
    }

    /**
     * Get a loader object from registry / load it if absent
     *
     * @param string $class The class name to instanciate ; will be completed with current
     *                      namespace if necessary
     * @param array $params Parameters to use for `$class` object instanciation
     *
     * @return object
     */
    public static function get($class, $params = null)
    {
        $class_name = MDE_Helper::getRelativeClassname($class);
        $obj = self::$registry->get($class_name);
        if (!empty($obj)) return $obj;
        else return self::factory($class_name, $params);
    }

    /**
     * Get a configuration entry from registry
     *
     * @param string $var
     * @return misc
     */
    public static function getConfig($var)
    {
        return self::get('Config')->get($var);
    }

    /**
     * Set a configuration entry in registry
     *
     * @param string $var
     * @param misc $val
     * @return misc
     */
    public static function setConfig($var, $val)
    {
        return self::get('Config')->set($var, $val);
    }

    /**
     * Add to a configuration entry in registry
     *
     * @param string $var
     * @param misc $val
     * @return misc
     */
    public static function addConfig($var, $val)
    {
        return self::get('Config')->add($var, $val);
    }

    /**
     * Get a parser entry from registry
     *
     * @param string $var
     * @return misc
     */
    public static function getVar($var)
    {
        return self::get('Registry')->get($var);
    }

    /**
     * Set a parser entry in registry
     *
     * @param string $var
     * @param misc $val
     * @return misc
     */
    public static function setVar($var, $val)
    {
        return self::get('Registry')->set($var, $val);
    }

    /**
     * Add to a parser entry in registry
     *
     * @param string $var
     * @param misc $val
     * @return misc
     */
    public static function addVar($var, $val)
    {
        return self::get('Registry')->add($var, $val);
    }

    /**
     * Unset a parser entry in registry
     *
     * @param string $var
     * @param misc $val
     * @return misc
     */
    public static function unsetVar($var, $val = null)
    {
        return self::get('Registry')->remove($var, $val);
    }

}

// Endfile
