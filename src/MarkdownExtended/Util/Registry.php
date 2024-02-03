<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use MarkdownExtended\Exception\UnexpectedValueException;

/**
 * Basic registry object
 */
class Registry
{
    /**
     * @var     array
     */
    protected $data;

    /**
     * Initialize the registry
     *
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    // ------------------
    // Setters / Getters
    // ------------------

    /**
     * Sets or resets a new instance in global registry
     *
     * @param   string  $var
     * @param   mixed   $val
     *
     * @return  void
     */
    public function set($var, $val)
    {
        $this->data[$var] = $val;
    }

    /**
     * Adds something to an existing entry of the global registry, the entry is created if it not exist
     *
     * @param   string  $var
     * @param   mixed   $val
     *
     * @return  void
     */
    public function add($var, $val)
    {
        if (isset($this->data[$var])) {
            $this->data[$var] = self::extend($this->data[$var], $val);
        } else {
            $this->data[$var] = $val;
        }
    }

    /**
     * Tests if an index exists in the registry
     *
     * @param string $var
     * @return bool
     */
    public function has($var)
    {
        return (bool) isset($this->data[$var]);
    }

    /**
     * Removes something to an existing entry of the global registry, the entry is created if it not exist
     *
     * @param   string      $var
     * @param   null|string $index
     *
     * @return  void
     */
    public function remove($var, $index = null)
    {
        if (isset($this->data[$var])) {
            if ($index) {
                if (isset($this->data[$var][$index])) {
                    unset($this->data[$var][$index]);
                }
            } else {
                unset($this->data[$var]);
            }
        }
    }

    /**
     * Gets an entry from the global registry
     *
     * @param   string  $var
     * @param   mixed   $default
     *
     * @return  mixed
     */
    public function get($var, $default = null)
    {
        return $this->has($var) ? $this->data[$var] : $default;
    }

    /**
     * Gets the global registry
     *
     * @return  array
     */
    public function getAll()
    {
        return $this->data;
    }

    /**
     * Counts the registry entries
     *
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    // --------------
    // Variables manipulation
    // --------------

    /**
     * Extends a value with another, if types match
     *
     * @param   mixed   $what
     * @param   mixed   $add
     *
     * @return  mixed
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if trying to extend an array with not an array
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if trying to extend an object
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if type unknown
     */
    public static function extend($what, $add)
    {
        if (empty($what)) {
            return $add;
        }
        switch (gettype($what)) {
            case 'string': return $what.$add;
                break;
            case 'numeric': return ($what + $add);
                break;
            case 'array':
                if (is_array($add)) {
                    $what += $add;
                    return $what;
                } else {
                    throw new UnexpectedValueException(
                        "Trying to extend an array with not an array"
                    );
                }
                break;
            case 'object':
                throw new UnexpectedValueException("Trying to extend an object");
                break;
            default:
                throw new UnexpectedValueException(sprintf(
                    "No extending definition found for type <%s>",
                    gettype($what)
                ));
                break;
        }
    }
}
