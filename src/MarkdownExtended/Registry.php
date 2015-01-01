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

use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Class Registry
 * @package MarkdownExtended
 */
class Registry
{

    /**
     * @var     bool
     */
    protected $is_extendable;

    /**
     * @var     bool
     */
    protected $is_removable;

    /**
     * @var     array
     */
    protected $data;

    /**
     * Initialize the registry
     *
     * @param   bool    $is_extendable
     * @param   bool    $is_removable
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
     * @param   string  $var
     * @param   mixed   $val
     * @return  void
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if `$var` seems invalid
     */
    public function set($var, $val)
    {
        try {
            if (self::validateVarname($var)) {
                $this->data[$var] = $val;
            }
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * Add something to an existing entry of the global registry, the entry is created if it not exist
     *
     * @param   string  $var
     * @param   mixed   $val
     * @return  void
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if `$var` seems invalid
     * @throws  \MarkdownExtended\Exception\RuntimeException if trying to add an entry of a non-extendable object
     */
    public function add($var, $val)
    {
        try {
            if (self::validateVarname($var)) {
                if ($this->is_extendable) {
                    if (isset($this->data[$var])) {
                        $this->data[$var] = self::extend($this->data[$var], $val);
                    } else {
                        $this->data[$var] = $val;
                    }
                } else {
                    throw new MDE_Exception\RuntimeException("Registry entry can not be extended!");
                }
            }
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        }
    }

    /**
     * Remove something to an existing entry of the global registry, the entry is created if it not exist
     *
     * @param   string      $var
     * @param   null/string $index
     * @return  void
     * @throws  \MarkdownExtended\Exception\RuntimeException if trying to remove a non-removable entry
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
     *
     * @param   string  $var
     * @param   mixed   $default
     * @return  mixed
     */
    public function get($var, $default = null)
    {
        return isset($this->data[$var]) ? $this->data[$var] : $default;
    }

// --------------
// Variables manipulation
// --------------

    /**
     * Extend a value with another, if types match
     *
     * @param   mixed   $what
     * @param   mixed   $add
     * @return  mixed
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if trying to extend an array with not an array
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if trying to extend an object
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if type unknown
     */
    public static function extend($what, $add)
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
                    throw new MDE_Exception\InvalidArgumentException(
                        "Trying to extend an array with not an array!"
                    );
                }
                break;
            case 'object':
                throw new MDE_Exception\InvalidArgumentException("Trying to extend an object!");
                break;
            default:
                throw new MDE_Exception\InvalidArgumentException(sprintf(
                    "No extending definition found for type <%s>!", gettype($what)
                ));
                break;
        }
    }

    /**
     * Validate a var name
     *
     * @param   string  $var
     * @return  bool
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if the var name is not an alpha-numeric string
     */
    public static function validateVarname($var)
    {
        if (!is_string($var) || !ctype_alnum(str_replace(array('_', '\\'), '', $var))) {
            throw new MDE_Exception\InvalidArgumentException(sprintf(
                'Registry entry must be named by alpha-numeric string, <%s> given!', $var
            ));
        }
        return true;
    }

}

// Endfile
