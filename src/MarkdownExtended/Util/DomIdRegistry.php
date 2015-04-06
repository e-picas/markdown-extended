<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;


class DomIdRegistry
{

    /**
     * @var     \MarkdownExtended\Util\Registry
     */
    protected $dom_ids = array();

    public function __construct()
    {
        $this->dom_ids = new Registry;
    }

    /**
     * Verify if a reference is already defined in the DOM IDs register
     *
     * @param   string  $reference  The reference to search
     * @return  bool    True if the reference exists in the register, false otherwise
     */
    public function has($reference)
    {
        return $this->dom_ids->has($reference);
    }

    /**
     * Get a DOM unique ID
     *
     * @param   string       $reference  A reference used to store the ID (and retrieve it - by default, a uniqid)
     * @param   null|string  $id
     * @return  string                  The unique ID created or the existing one for the reference if so
     */
    public function get($reference, $id = null)
    {
        return $this->has($reference) ?
            $this->dom_ids->get($reference) : $this->set(
                $id ?: $reference, $reference
            );
    }

    /**
     * Create and get a new DOM unique ID
     *
     * @param   string      $id         A string that will be used to construct the ID
     * @param   string      $reference  A reference used to store the ID (and retrieve it - by default `$id`)
     * @param   bool        $return_array   Allow to return an array in case of existing reference
     * @return  array|string    The unique ID created if the reference was empty
     *                          An array like (id=>XXX, reference=>YYY) if it was not
     */
    public function set($id, $reference = null, $return_array = true)
    {
        $_reference = $reference;
        if (empty($_reference)) {
            $_reference = $id;
        }

        $new_id = $id;
        while ($this->has($new_id)) {
            $new_id = $id.'_'.uniqid();
        }

        if ($this->has($_reference)) {
            while ($this->has($_reference)) {
                $_reference = $reference.'_'.uniqid();
            }
            $return = true===$return_array ? array(
                'id'=>$new_id, 'reference'=>$_reference
            ) : $new_id;
        } else {
            $return = $new_id;
        }

        $this->dom_ids->set($_reference, $new_id);
        return $return;
    }

}

// Endfile
