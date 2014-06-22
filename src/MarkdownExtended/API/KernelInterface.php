<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
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
namespace MarkdownExtended\API;

/**
 * Interface that defines a "kernel" principle
 *
 * It mostly forces static methods and works like a services container for:
 * - a "contents" collection
 * - a parser object
 * - a configuration object
 * - an optional templater
 *
 * It can be used as a classic class object or as a singleton instance.
 *
 */
interface KernelInterface
{

    /**
     * Initialize the registry and flush the contents stack
     *
     * The best practice is to use the class as a singleton calling `getInstance()` or
     * `create()`.
     *
     * @param  array   $options     A set of user options values
     */
    public function __construct(array $options = null);

    /**
     * Get the instance, which will be created if required
     *
     * @param   int     $instance_id    The ID of the MDE instance to get
     * @param   array   $options        A set of user options values
     * @return  self                    Must return the object itself
     */
    public static function getInstance($instance_id = null, array $options = null);

    /**
     * Create a new singleton instance
     *
     * @param   int     $instance_id    The ID of the MDE instance to create
     * @param   array   $options        A set of user options values
     * @return  self                    Must return the object itself for method chaining
     */
    public static function create(array $options = null, $instance_id = null);

// ----------------------------------
// Service Container
// ----------------------------------

    /**
     * Load a dependency
     *
     * @param   string  $class_name     The class name to instantiate ; will be completed with current
     *                                  namespace if necessary
     * @return  bool                    Must return a boolean
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException   Must throw an exception it the class doesn't exist
     */
//    public static function load($class_name);

    /**
     * Build, retain and return a dependency instance
     *
     * @param   string  $class_name     The class name to instantiate ; will be completed with current
     *                                  namespace if necessary
     * @param   array   $params         Parameters to use for `$class_name` object instantiation
     *
     * @return  object                  Must return a service object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException   Must throw an exception it the class doesn't exist
     */
    public static function factory($class_name, $params = null);

    /**
     * Get a service from the container ; load it if absent
     *
     * @param   string  $class_name     The class name to instantiate ; will be completed with current
     *                                  namespace if necessary
     * @param   array   $params         Parameters to use for `$class_name` object instantiation
     * @param   mixed   $flag           One of the interface flags
     * @return  object                  Must return a service object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException   Must throw an exception it the class doesn't exist
     */
    public static function get($class_name, $params = null, $flag = \MarkdownExtended\API::FAIL_GRACEFULLY);

// ----------------------------------
// Parsing
// ----------------------------------

    /**
     * Transform a source string
     *
     * @param   string          $source
     * @param   string|array    $parser_options
     * @param   string|null     $key
     * @param   bool            $secondary      Set it to `true` if parsed content may not be
     *                                          stored as the current one of the collection
     * @return  \MarkdownExtended\API\ContentInterface  Must return a content object
     */
    public static function transformString($source, $parser_options = null, $key = null, $secondary = false);
    
    /**
     * Transform a source file content
     *
     * @param   string          $filename
     * @param   string|array    $parser_options
     * @param   string|null     $key
     * @param   bool            $secondary      Set it to `true` if parsed content may not be
     *                                          stored as the current one of the collection
     * @return  \MarkdownExtended\API\ContentInterface  Must return a content object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException   Must throw an exception if the `$filename` can't be found
     */
    public static function transformSource($filename, $parser_options = null, $key = null, $secondary = false);
    
}

// Endfile
