<?php
/**
 * PHP Extended Markdown
 * Copyright (c) 2004-2012 Pierre Cassat
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

/**
 * PHP Extended Markdown Mother Class
 *
 * This is the global *Markdown_Extended* class and process. It contains mostly
 * static methods that can be called from anywhere writing something like:
 *
 *     Markdown_Extended::my_method();
 *
 */
class Markdown_Extended
{

	/**
	 * Class infos
	 */
	static $class_name = 'PHP Extended Markdown OO';
	static $class_version = '1.0';
	static $class_sources = 'https://github.com/PieroWbmstr/Extended_Markdown';

	/**
	 * Markdown Extended instance
	 */
	private static $_instance;

	/**
	 * Markdown object registry
	 */
	private static $registry;

	/**
	 * Private constructor: initialize the registry
	 */
	private function __construct()
	{
	 	self::$registry = array(
	 		// every objects instances
	 		'load'=>array(),
	 		// configuration settings
	 		'config'=>array(),
	 		// runtime parser variables
	 		'parser'=>array(),
	 	);
	}

	/**
	 * Get the Markdown Extended instance
	 */
	public static function &getInstance()
	{
	 	if (empty(self::$_instance)) {
	 		self::$_instance = new Markdown_Extended;
		}
	 	return self::$_instance;
	}

	/**
	 * Debug function
	 *
	 * WARNING: first argument is not used (to allow `debug` from Gamut stacks)
	 */
	public function debug( $a='', $what=null, $exit=true ) 
	{
		echo '<pre>';
		if (!is_null($what)) var_export($what);
		else {
			$mde = self::getInstance();
			var_export( $mde::$registry );
		}
		echo '</pre>';
		if ($exit) exit(0);
	}
	
	/**
	 * Get information string about the current Markdown Extended object
	 */
	static function info( $html=false )
	{
		return 
			( $html ? '<strong>' : '' )
			.Markdown_Extended::$class_name
			.( $html ? '</strong>' : '' )
			.' version '.Markdown_Extended::$class_version
			.' ('
			.( $html ? '<a href="'.Markdown_Extended::$class_sources.'" target="_blank" title="See online">' : '' )
			.Markdown_Extended::$class_sources
			.( $html ? '</a>' : '' )
			.')';
	}

	/**
	 * Load a dependency
	 */
	static function load( $class )
	{
		$pathes = explode('_', $class);
		$_f = join('/', $pathes).'.php';
		if (defined('MARKDOWN_EXTENDED_DIR'))
			$_f = rtrim(MARKDOWN_EXTENDED_DIR, '/').'/'.$_f;
		if (@file_exists($_f)) {
			include_once $_f;
		} else {
			throw new InvalidArgumentException(sprintf(
      	"Class '%s' not found in '%s'!", $class, $_f
      ));
		}
	}

	/**
	 * Get a dependency instance
	 */
	public function factory( $class, $params=null )
	{
		self::load($class);
		if (!is_null($params)) $_obj = new $class( $params );
		else $_obj = new $class;
		self::_setRegistryEntry( $class, $_obj, 'load' );
		return $_obj;
	}

// --------------
// REGISTRY USER INTERFACE
// --------------

	/**
	 * Get a loader object from registry / load it if absent
	 */
	static function get( $class, $params=null )
	{
		$mde =& self::getInstance();
		$obj = $mde->_getRegistryEntry( $class, 'load' );
		if (!empty($obj)) return $obj;
		else return $mde->factory( $class, $params );
	}

	/**
	 * Get a configuration entry from registry
	 */
	static function getConfig( $var )
	{
		$mde =& self::getInstance();
		return $mde->_getRegistryEntry( $var, 'config' );
	}

	/**
	 * Set a configuration entry in registry
	 */
	static function setConfig( $var, $val )
	{
		$mde =& self::getInstance();
		return $mde->_setRegistryEntry( $var, $val, 'config' );
	}

	/**
	 * Add to a configuration entry in registry
	 */
	static function addConfig( $var, $val )
	{
		$mde =& self::getInstance();
		return $mde->_addRegistryEntry( $var, $val, 'config' );
	}

	/**
	 * Get a parser entry from registry
	 */
	static function getVar( $var )
	{
		$mde =& self::getInstance();
		return $mde->_getRegistryEntry( $var, 'parser' );
	}

	/**
	 * Set a parser entry in registry
	 */
	static function setVar( $var, $val )
	{
		$mde =& self::getInstance();
		return $mde->_setRegistryEntry( $var, $val, 'parser' );
	}

	/**
	 * Add to a parser entry in registry
	 */
	static function addVar( $var, $val )
	{
		$mde =& self::getInstance();
		return $mde->_addRegistryEntry( $var, $val, 'parser' );
	}

	/**
	 * Unset a parser entry in registry
	 */
	static function unsetVar( $var, $val=null )
	{
		$mde =& self::getInstance();
		return $mde->_removeRegistryEntry( $var, $val, 'parser' );
	}

// --------------
// REGISTRY INTERNALS
// --------------

	/**
	 * Set or reset a new instance in global registry
	 */
	protected function _setRegistryEntry( $var, $val, $stack )
	{
		if (!empty($stack) && is_string($stack)) 
		{
			if (is_string($var) && ctype_alnum( str_replace('_', '', $var) )) 
			{
				if (isset(self::$registry[$stack]))
				{
					switch($stack) 
					{
						case 'load':
							if (is_object($val))
								self::$registry['load'][$var] = $val;
							else
								throw new InvalidArgumentException(sprintf(
  			  	  		"New registry entry in 'load' stack must be an object instance, <%s> given!", gettype($val)
			  	  	  ));
							break;
						case 'config':
							self::$registry['config'][$var] = $val;
							break;
						case 'parser':
							self::$registry['parser'][$var] = $val;
							break;
						default: break;
					}
				}
				else
				{
					throw new InvalidArgumentException(sprintf(
  		  		"Unknown stack <%s> in registry!", $stack
		  	  ));
				}
		  } 
		  else 
		  {
				throw new InvalidArgumentException(sprintf(
    			"New registry entry must be named by alpha-numeric string, <%s> given!", $var
	    	));
		  }
	  } 
	  else 
	  {
			throw new InvalidArgumentException(sprintf(
    		"No stack for new registry entry <%s>!", $var
	    ));
	  }
	}

	/**
	 * Add something to an existing entry of the global registry, the entry is created if it not exist
	 */
	protected function _addRegistryEntry( $var, $val, $stack )
	{
		if (!empty($stack) && is_string($stack)) 
		{
			if (is_string($var) && ctype_alnum( str_replace('_', '', $var) )) 
			{
				if (isset(self::$registry[$stack]))
				{
					switch($stack) 
					{
						case 'load':
							throw new RuntimeException(
  			  			"Registry entry in 'load' stack can not be extended!"
			  		  );
							break;
						case 'config': case 'parser':
							self::$registry[$stack][$var] = self::_extend(self::$registry[$stack][$var], $val);
							break;
						default: break;
					}
				}
				else
				{
					throw new InvalidArgumentException(sprintf(
  		  		"Unknown stack <%s> in registry!", $stack
		  	  ));
				}
		  } 
		  else 
		  {
				throw new InvalidArgumentException(sprintf(
    			"New registry entry must be named by alpha-numeric string, <%s> given!", $var
	    	));
		  }
	  } 
	  else 
	  {
			throw new InvalidArgumentException(sprintf(
    		"No stack for new registry entry <%s>!", $var
	    ));
	  }
	}

	/**
	 * Remove something to an existing entry of the global registry, the entry is created if it not exist
	 */
	protected function _removeRegistryEntry( $var, $val=null, $stack )
	{
		if (!empty($stack) && is_string($stack)) 
		{
			if (is_string($var) && ctype_alnum( str_replace('_', '', $var) )) 
			{
				if (isset(self::$registry[$stack]))
				{
					switch($stack) 
					{
						case 'load':
							throw new RuntimeException(
  			  			"Registry entry in 'load' stack can not be extended!"
			  		  );
							break;
						case 'config': case 'parser':
							if ($val) {
								if (isset(self::$registry[$stack][$var]) && isset(self::$registry[$stack][$var][$val]))
									unset(self::$registry[$stack][$var][$val]);
							} else {
								if (isset(self::$registry[$stack][$var]))
									unset(self::$registry[$stack][$var]);
							}
							break;
						default: break;
					}
				}
				else
				{
					throw new InvalidArgumentException(sprintf(
  		  		"Unknown stack <%s> in registry!", $stack
		  	  ));
				}
		  } 
		  else 
		  {
				throw new InvalidArgumentException(sprintf(
    			"New registry entry must be named by alpha-numeric string, <%s> given!", $var
	    	));
		  }
	  } 
	  else 
	  {
			throw new InvalidArgumentException(sprintf(
    		"No stack for new registry entry <%s>!", $var
	    ));
	  }
	}

	/**
	 * Get an entry from the global registry
	 */
	protected function _getRegistryEntry( $var, $stack, $default=null )
	{
		if (!empty($stack) && is_string($stack)) 
		{
			if (is_string($var) && ctype_alnum( str_replace('_', '', $var) )) 
			{
				if (isset(self::$registry[$stack]))
				{
					if (isset(self::$registry[$stack][$var]))
						return self::$registry[$stack][$var];
				}
				else
				{
					throw new InvalidArgumentException(sprintf(
  		  		"Unknown stack <%s> in registry!", $stack
		  	  ));
				}
		  } 
		  else 
		  {
				throw new InvalidArgumentException(sprintf(
  	  		"Registry entry must be retrieved by alpha-numeric string, <%s> given!", $var
	  	  ));
		  }
	  } 
	  else 
	  {
			throw new InvalidArgumentException(sprintf(
    		"No stack for retreiving registry entry <%s>!", $var
	    ));
	  }
	  return $default;
	}

	/**
	 * Extend a value with another, if types match
	 */
	protected function _extend( $what, $add )
	{
		if (empty($what)) return $add;
		switch(gettype($what))
		{
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
				throw new InvalidArgumentException(
    			"Trying to extend an object!"
		    );
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
