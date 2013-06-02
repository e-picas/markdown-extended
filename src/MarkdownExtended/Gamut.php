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

use \InvalidArgumentException, \UnexpectedValueException;

class Gamut
{

	/**
	 * Table of referenced aliases to execute gamuts
	 */
	protected $gamutAliases;

	/**
	 * Avoid MarkdownExtended `__construct()`
	 */
	public function __construct($gamut_aliases = null)
	{
		if (!empty($gamut_aliases) && is_array($gamut_aliases)) {
			$this->gamutAliases = $gamut_aliases;
		} else {
			throw new InvalidArgumentException(sprintf(
    			"Gamuts aliases must be an array, <%s> given!", gettype($gamut_aliases)
	    	));
	    }
	}

	/**
	 * Run a table of gamuts by priority
	 */
	public function runGamuts($gamuts, $text = null)
	{
		if (!is_array($gamuts)) {
			throw new InvalidArgumentException(sprintf(
    			"Gamuts list must be an array, <%s> given!", gettype($gamuts)
	    	));
        }
		asort($gamuts);
		foreach ($gamuts as $method => $priority) {
			$text = self::runGamut($method, $text);
		}
		return $text;
	}

	/**
	 * Run a table of gamuts for a specific method by priority
	 */
	public function runGamutsMethod($gamuts, $method, $text = null)
	{
		if (!is_array($gamuts)) {
			throw new InvalidArgumentException(sprintf(
    			"Gamuts list must be an array, <%s> given!", gettype($gamuts)
	    	));
        }
		if (!is_string($method)) {
			throw new InvalidArgumentException(sprintf(
    			"Gamuts method must be a string, <%s> given!", gettype($method)
	    	));
        }
		asort($gamuts);
		foreach ($gamuts as $_gmt => $priority) {
			$text = self::runGamut($_gmt, $text, $method, true);
        }
		return $text;
	}

	/**
	 * Run a single gamut
	 */
	public function runGamut($gamut, $text = null, $_method = null, $silent = false)
	{
		if (!is_string($gamut)) {
			throw new InvalidArgumentException(sprintf(
    			"Gamut name must be a string, <%s> given!", gettype($gamut)
	    	));
	    }

		$to_skip = MarkdownExtended::getConfig('skip_filters');

//echo '<br />Executing gamut : '.$gamut;
		if (substr_count($gamut, ':')) {
			@list($global_class, $class, $method) = explode(':', $gamut);
			if (!empty($class) && !empty($to_skip) && in_array($class, $to_skip)) 
				return $text;

			if (!empty($global_class) && isset($this->gamutAliases[ $global_class ])) {
				if (!empty($class)) {
					$_obj = MarkdownExtended::get($this->gamutAliases[$global_class].'\\'.$class);
					if (!empty($_method)) $method = $_method;
					if (empty($method)) $method = $_obj->getDefaultMethod();
					if (method_exists($_obj, $method)) {
						$text = $_obj->$method( $text );
					} elseif (!$silent) {
						throw new UnexpectedValueException(sprintf(
			    			"Method name in Gamut must exists for class <%s>!", $class
						));
					}
				} else {
					throw new UnexpectedValueException(sprintf(
		    			"Class name in Gamut must be a string, <%s> given!", $class
					));
				}
			} else {
				throw new UnexpectedValueException(sprintf(
	    			"Gamut name must begin by a gamut alias, <%s> given!", $global_class
				));
			}
		} elseif (empty($_method)) {
			if (method_exists($this, $gamut)) {
				$md = MarkdownExtended::get('\MarkdownExtended\Parser');
				if (method_exists($md, $gamut))
					$text = $md->$gamut( $text );
			} else {
				$gamuts = MarkdownExtended::getConfig( $gamut );
				if (!empty($gamuts) && is_array($gamuts)) {
					$text = self::runGamuts( $gamuts, $text );
				} else {
					throw new UnexpectedValueException(sprintf(
			  			"Gamut not found: <%s>!", $gamut
					));
				}
			}
		}

		return $text;
	}

}

// Endfile