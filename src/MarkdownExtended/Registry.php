<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2004-2013 Pierre Cassat
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

/**
 */
class Registry
{

	/**
	 * Loaded objects stack registry
	 * @var array
	 */
	private $loaded;

	/**
	 * Config entries stack registry
	 * @var array
	 */
	private $config;

	/**
	 * Parser variables stack registry
	 * @var array
	 */
	private $parser;

	/**
	 * Initialize the registry
	 */
	public function __construct()
	{
	 	$this->loaded = array();
	 	$this->config = array();
	 	$this->parser = array();
	}

	/**
	 * Set or reset a new instance in global registry
	 */
	public function set($var, $val, $stack)
	{
		if (!empty($stack) && is_string($stack)) {
			if (is_string($var) && ctype_alnum( str_replace(array('_', '\\'), '', $var) )) {
				if (isset($this->{$stack})){
					switch ($stack) {
						case 'loaded':
							if (is_object($val))
								$this->loaded[$var] = $val;
							else
								throw new InvalidArgumentException(sprintf(
  			  	  					"New registry entry in the 'loaded' stack must be an object instance, <%s> given!", gettype($val)
			  	  	  			));
							break;
						case 'config':
							$this->config[$var] = $val;
							break;
						case 'parser':
							$this->parser[$var] = $val;
							break;
						default: break;
					}
				} else{
					throw new InvalidArgumentException(sprintf(
  		  				"Unknown stack <%s> in registry!", $stack
		  	  		));
				}
			} else {
				throw new InvalidArgumentException(sprintf(
    				"New registry entry must be named by alpha-numeric string, <%s> given!", $var
	    		));
			}
		} else {
			throw new InvalidArgumentException(sprintf(
    			"No stack for new registry entry <%s>!", $var
	    	));
		}
	}

	/**
	 * Add something to an existing entry of the global registry, the entry is created if it not exist
	 */
	public function add($var, $val, $stack)
	{
		if (!empty($stack) && is_string($stack)) {
			if (is_string($var) && ctype_alnum( str_replace(array('_', '\\'), '', $var) )) {
				if (isset($this->{$stack})){
					switch ($stack) {
						case 'loaded':
							throw new RuntimeException(
  			  					"Registry entry in the 'load' stack can not be extended!"
			  		  		);
							break;
						case 'config': case 'parser':
							$this->{$stack}[$var] = $this->extend($this->{$stack}[$var], $val);
							break;
						default: break;
					}
				} else{
					throw new InvalidArgumentException(sprintf(
  		  				"Unknown stack <%s> in registry!", $stack
		  	  		));
				}
			} else {
				throw new InvalidArgumentException(sprintf(
    				"New registry entry must be named by alpha-numeric string, <%s> given!", $var
	    		));
			}
		} else {
			throw new InvalidArgumentException(sprintf(
    			"No stack for new registry entry <%s>!", $var
	    	));
		}
	}

	/**
	 * Remove something to an existing entry of the global registry, the entry is created if it not exist
	 */
	public function remove($var, $val = null, $stack = null)
	{
		if (!empty($stack) && is_string($stack)) {
			if (is_string($var) && ctype_alnum( str_replace(array('_', '\\'), '', $var) )) {
				if (isset($this->{$stack})) {
					switch ($stack) {
						case 'loaded':
							throw new RuntimeException(
  			  					"Registry entry in the 'load' stack can not be extended!"
			  		  		);
							break;
						case 'config': case 'parser':
							if ($val) {
								if (isset($this->{$stack}[$var]) && isset($this->{$stack}[$var][$val]))
									unset($this->{$stack}[$var][$val]);
							} else {
								if (isset($this->{$stack}[$var]))
									unset($this->{$stack}[$var]);
							}
							break;
						default: break;
					}
				} else {
					throw new InvalidArgumentException(sprintf(
  		  				"Unknown stack <%s> in registry!", $stack
		  	  		));
				}
			} else {
				throw new InvalidArgumentException(sprintf(
    				"New registry entry must be named by alpha-numeric string, <%s> given!", $var
	    		));
			}
		} else {
			throw new InvalidArgumentException(sprintf(
    			"No stack for new registry entry <%s>!", $var
	    	));
		}
	}

	/**
	 * Get an entry from the global registry
	 */
	public function get($var, $stack, $default = null)
	{
		if (!empty($stack) && is_string($stack)) {
			if (is_string($var) && ctype_alnum( str_replace(array('_', '\\'), '', $var) )) {
				if (isset($this->{$stack})){
					if (isset($this->{$stack}[$var]))
						return $this->{$stack}[$var];
				} else {
					throw new InvalidArgumentException(sprintf(
  		  				"Unknown stack <%s> in registry!", $stack
		  	  		));
				}
			} else {
				throw new InvalidArgumentException(sprintf(
  	  				"Registry entry must be retrieved by alpha-numeric string, <%s> given!", $var
	  	  		));
			}
		} else {
			throw new InvalidArgumentException(sprintf(
    			"No stack for retreiving registry entry <%s>!", $var
	    	));
		}
		return $default;
	}

	/**
	 * Extend a value with another, if types match
	 */
	public function extend($what, $add)
	{
		if (empty($what)) return $add;
		switch (gettype($what)) {
			case 'string': return $what.$add; break;
			case 'numeric': return ($what+$add); break;
			case 'array': 
				if (is_array($add)) {
					$what += $add;
					return $what; 
				} else {
					throw new InvalidArgumentException(
    					"Trying to extend an array with not an array!"
			    	);
				}
				break;
			case 'object': 
				throw new InvalidArgumentException("Trying to extend an object!");
				break;
			default: 
				throw new InvalidArgumentException(sprintf(
  	  				"No extending definition found for type <%s>!", gettype($what)
		    	));
				break;
		}
	}

}

// Endfile
