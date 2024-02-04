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

/**
 * A special registry to manage a collection of DOM ids
 */
class DomIdRegistry
{
    /**
     * The IDs registry
     *
     * @var     \MarkdownExtended\Util\Registry
     */
    protected $dom_ids = [];

    /**
     * Initializes the registry
     */
    public function __construct()
    {
        $this->dom_ids = new Registry();
    }

    /**
     * Verifies if a reference is already defined in registry
     *
     * @param   string  $reference  The reference to search
     *
     * @return  bool
     */
    public function has($reference)
    {
        return $this->dom_ids->has($reference);
    }

    /**
     * Verifies if an ID is already defined in the registry
     *
     * @param   string  $id  The ID to search
     *
     * @return  bool
     */
    public function exists($id)
    {
        return in_array($id, $this->dom_ids->getAll());
    }

    /**
     * Gets a DOM unique ID
     *
     * @param   string       $reference  A reference used to store the ID (and retrieve it - by default, a uniqid)
     * @param   null|string  $id
     *
     * @return  string                  The unique ID created or the existing one for the reference if so
     */
    public function get($reference, $id = null)
    {
        return $this->has($reference) ?
            $this->dom_ids->get($reference) : $this->set(
                $id ?: $reference,
                $reference
            );
    }

    /**
     * Creates and get a new DOM unique ID
     *
     * @param   string      $id         A string that will be used to construct the ID
     * @param   string      $reference  A reference used to store the ID (and retrieve it - by default `$id`)
     *
     * @return  string      The unique ID created
     */
    public function set($id, &$reference = null)
    {
        $new_id     = $id;
        $counter    = 0;
        while ($this->exists($new_id)) {
            $counter++;
            $new_id = $id.'-'.$counter;
        }

        $_reference = $reference;
        if (empty($_reference)) {
            $_reference = $id;
        }
        if ($this->has($_reference)) {
            $counter = 0;
            while ($this->has($_reference)) {
                $counter++;
                $_reference = $reference.'-'.$counter;
            }
        }

        $reference = $_reference;
        $this->dom_ids->set($reference, $new_id);
        return $new_id;
    }
}
