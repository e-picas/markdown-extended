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

use \InvalidArgumentException, \RuntimeException;
use \MarkdownExtended\Registry;

/**
 * PHP Extended Markdown Mother Class
 *
 * This is the global *MarkdownExtended* class and process. It contains mostly
 * static methods that can be called from anywhere writing something like:
 *
 *     MarkdownExtended::my_method();
 *
 */
class MarkdownExtended
{

	/**
	 * Class infos
	 */
	public static $class_name = 'PHP Markdown Extended';
	public static $class_version = '0.0.2';
	public static $class_sources = 'https://github.com/atelierspierrot/markdown-extended';

	/**
	 * Default options INI file (path from __DIR__)
	 */
    const MARKDOWN_CONFIGFILE = 'config/markdown_config.ini';

	/**
	 * @static \MarkdownExtended\MarkdownExtended
	 */
	private static $_instance;

	/**
	 * @static \MarkdownExtended\Registry
	 */
	private static $registry;

    /**
     * @static array
     */
    private static $contents;

	/**
	 * Private constructor: initialize the registry
	 */
	private function __construct()
	{
	 	self::$registry = new Registry;
        self::$contents = array();
	}

	/**
	 * Get the Markdown Extended instance
	 */
	public static function getInstance()
	{
	 	if (empty(self::$_instance)) {
	 		self::$_instance = new self;
	 	}
	 	return self::$_instance;
	}

// ----------------------------------
// DEBUG & INFO
// ----------------------------------

	/**
	 * Debug function
	 *
	 * WARNING: first argument is not used (to allow `debug` from Gamut stacks)
	 */
	public function debug($a = '', $what = null, $exit = true) 
	{
		echo '<pre>';
		if (!is_null($what)) var_export($what);
		else {
			$mde = self::getInstance();
			var_export($mde::$registry);
		}
		echo '</pre>';
		if ($exit) exit(0);
	}
	
	/**
	 * Get information string about the current Markdown Extended object
	 */
	public static function info($html = false)
	{
		return 
			( $html ? '<strong>' : '' )
			.MarkdownExtended::$class_name
			.( $html ? '</strong>' : '' )
			.' version '.MarkdownExtended::$class_version
			.' ('
			.( $html ? '<a href="'.MarkdownExtended::$class_sources.'" target="_blank" title="See online">' : '' )
			.MarkdownExtended::$class_sources
			.( $html ? '</a>' : '' )
			.')';
	}

// ----------------------------------
// PROCESSED CONTENTS
// ----------------------------------

    /**
     * Add a new processed content in the contents stack
     */
    public static function addProcessedContent(Parser $markdown, $key = null)
    {

echo '<pre>';
var_export(func_get_args());
echo '</pre>';
exit('yo');

        if (empty($key)) $key = uniqid();
        self::$contents[$key] = array(
            'original'=>$original,
            'markdown'=>$markdown
        );
    }
	
// --------------
// REGISTRY USER INTERFACE
// --------------

	/**
	 * Load a dependency
	 */
	public static function load($class)
	{
		if (@class_exists($class)) return true;
        throw new InvalidArgumentException(sprintf(
            "Class '%s' not found in '%s'!", $class, $_f
        ));
	}

	/**
	 * Get a dependency instance
	 */
	public static function factory($class, $params = null)
	{
		self::load($class);
		if (!is_null($params)) {
		    $_obj = new $class($params);
		} else {
		    $_obj = new $class;
		}
		self::$registry->set($class, $_obj, 'loaded');
		return $_obj;
	}

	/**
	 * Get a loader object from registry / load it if absent
	 */
	public static function get($class, $params = null)
	{
		$obj = self::$registry->get($class, 'loaded');
		if (!empty($obj)) return $obj;
		else return self::factory($class, $params);
	}

	/**
	 * Get a configuration entry from registry
	 */
	public static function getConfig($var)
	{
		return self::$registry->get($var, 'config');
	}

	/**
	 * Set a configuration entry in registry
	 */
	public static function setConfig($var, $val)
	{
		return self::$registry->set($var, $val, 'config');
	}

	/**
	 * Add to a configuration entry in registry
	 */
	public static function addConfig($var, $val)
	{
		return self::$registry->add($var, $val, 'config');
	}

	/**
	 * Get a parser entry from registry
	 */
	public static function getVar($var)
	{
		return self::$registry->get($var, 'parser');
	}

	/**
	 * Set a parser entry in registry
	 */
	public static function setVar($var, $val)
	{
		return self::$registry->set($var, $val, 'parser');
	}

	/**
	 * Add to a parser entry in registry
	 */
	public static function addVar($var, $val)
	{
		return self::$registry->add($var, $val, 'parser');
	}

	/**
	 * Unset a parser entry in registry
	 */
	public static function unsetVar($var, $val = null)
	{
		return self::$registry->remove($var, $val, 'parser');
	}

}

// Endfile
