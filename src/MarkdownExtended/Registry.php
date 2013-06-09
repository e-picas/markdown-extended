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

use MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 */
class Registry
{

    /**
     * @var bool
     */
    protected $is_extendable;

    /**
     * @var bool
     */
    protected $is_removable;

	/**
	 * @var array
	 */
	protected $data;

	/**
	 * Initialize the registry
	 *
	 * @param bool $is_extendable
	 * @param bool $is_removable
	 */
	public function __construct($is_extendable = true, $is_removable = true)
	{
	    $this->is_extendable = $is_extendable;
	    $this->is_removable = $is_removable;
        $this->data = array();
	}

// ------------------
// Setters / Getters
// ------------------

	/**
	 * Set or reset a new instance in global registry
	 *
	 * @throws MarkdownExtended\Exception\InvalidArgumentException if `$val` is not an 
	 *          object in the 'loaded' stack
	 */
	public function set($var, $val)
	{
		if (MDE_Helper::validateVarname($var)) {
            $this->data[$var] = $val;
		}
	}

	/**
	 * Add something to an existing entry of the global registry, the entry is created if it not exist
	 *
	 * @throws MarkdownExtended\Exception\RuntimeException if trying to add an entry of a non-extendable object
	 */
	public function add($var, $val)
	{
		if (MDE_Helper::validateVarname($var)) {
		    if ($this->is_extendable) {
		        if (isset($this->data[$var])) {
                    $this->data[$var] = MDE_Helper::extend($this->data[$var], $val);
                } else {
                    $this->data[$var] = $val;
                }
		    } else {
                throw new MDE_Exception\RuntimeException("Registry entry can not be extended!");
		    }
		}
	}

	/**
	 * Remove something to an existing entry of the global registry, the entry is created if it not exist
	 */
	public function remove($var, $index = null)
	{
		if ($this->is_removable) {
            if (isset($this->data[$var])) {
                if ($index) {
                    if (isset($this->data[$var][$index])) {
                        unset($this->data[$var][$index]);
                    }
                } else {
                    unset($this->data[$var]);
                }
            }
        } else {
            throw new MDE_Exception\RuntimeException("Registry entry can not be removed!");
        }
	}

	/**
	 * Get an entry from the global registry
	 */
	public function get($var, $default = null)
	{
        return isset($this->data[$var]) ? $this->data[$var] : $default;
	}

}

// Endfile
