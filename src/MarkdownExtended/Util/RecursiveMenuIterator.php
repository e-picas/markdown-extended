<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Util;

use \MarkdownExtended\Exception as MDE_Exception;
use \RecursiveArrayIterator;
use \RecursiveIterator;

/**
 * Class RecursiveMenuIterator
 * @package MarkdownExtended\Util
 */
class RecursiveMenuIterator
    extends RecursiveArrayIterator
    implements RecursiveIterator
{

    /**
     * @var  array
     */
    public static $range_levels = array(1,2,3,4,5,6);

    /**
     * @var     int
     */
    protected $position = 0;

    /**
     * @var     int     This must exist in `self::$range_levels`
     */
    protected $base_level = 0;

    /**
     * @var     array   This reminds all current hierarchic keys we are working on
     */
    protected $current_keys = array();

    /**
     * @var     \ArrayIterator
     */
    protected $menu_iterator = null;

    /**
     * @var     int     Internal usage
     */
    private $base_level_tmp;

    /**
     * @param   \ArrayAccess    $menu
     * @param   int             $base_level
     * @param   int             $flags
     * @see     parent::__construct()
     * @throws  \MarkdownExtended\Exception\DomainException if `$level` is not in `self::$range_levels`
     */
    public function __construct(\ArrayAccess $menu = null, $base_level = 0, $flags = 0)
    {
        $this->menu_iterator = new \ArrayIterator;
        foreach (self::$range_levels as $index) {
            $this->current_keys[$index] = null;
        }
        parent::__construct($menu, $flags);
        if ($base_level!==0 && !in_array($base_level, self::$range_levels)) {
            throw new MDE_Exception\DomainException(
                sprintf('Invalid "base level" value for %s object: %d!', __CLASS__, $base_level)
            );
        }
        $this->base_level = $base_level;
        if ($this->count()!=0) {
            $this->_rebuild();
        }
    }

// ----------------------------
// Iterator methods
// ----------------------------

    /**
     * Pass to next iteration
     */
    public function next()
    {
        $this->position++;
        parent::next();
    }

    /**
     * Back to previous iteration
     */
    public function previous()
    {
        $this->position--;
        parent::seek($this->position);
    }

    /**
     * Back to first iteration
     */
    public function rewind()
    {
        $this->position = 0;
        parent::rewind();
    }

    /**
     * Seek to an iteration by its position
     *
     * @param int $position
     */
    public function seek($position)
    {
        $this->position = $position;
        parent::seek($this->position);
    }

    /**
     * Get the current iterator position
     *
     * @return int
     */
    public function position()
    {
        return $this->position;
    }

    /**
     * Append a new value to iterator and then rebuild menu
     *
     * @param mixed $value
     */
    public function append($value)
    {
        parent::append($value);
        $this->_rebuild();
    }

    /**
     * Get an array of the current menu
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return (array) $this->menu_iterator;
    }

    /**
     * Set a new key entry by its depth
     *
     * @param   int         $index
     * @param   int/string  $key
     * @throws  \MarkdownExtended\Exception\DomainException if `$index` is not in `self::$range_levels`
     */
    public function menuKeySet($index, $key)
    {
        if (!in_array($index, self::$range_levels)) {
            throw new MDE_Exception\DomainException(
                sprintf('Invalid key "index" value for %s object: %d!', __CLASS__, $index)
            );
        }
        $this->current_keys[$index] = $key;
    }

    /**
     * Define a new first level entry
     *
     * @param   int/string      $index
     * @param   \ArrayAccess    $newval
     */
    public function menuOffsetSet($index, $newval)
    {
        $this->_initItem($newval, array('level'=>$this->base_level));
        $this->menu_iterator->offsetSet($index, $newval);
        $this->_setRecursiveKey($index, $newval['level']);
    }

    /**
     * Define a new entry recursively
     *
     * @param   int/string      $index
     * @param   \ArrayAccess    $newval
     */
    public function menuOffsetSetRecursive($index, $newval)
    {
        $this->_setRecursiveKey(null, $newval['level']);
        $item = $this->_menuGetRecursivePath();
        $this->_initItem($newval);
        $item->addChild($index, $newval);
        $this->_setRecursiveKey($index, $newval['level']);
    }

    /**
     * Rebuild the menu iterator based on current iterator
     */
    protected function _rebuild()
    {
        $old_position = $this->position();
        if ($this->base_level===0) {
            $this->_findHighestLevel();
        }

        $this->rewind();
        while ($this->valid()) {
            $item = $this->current();
            $item_key = $this->key();
            if ($this->_validItem($item)) {
                if ($item['level']===$this->base_level) {
                    $this->menuOffsetSet($item_key, $item);
                } else {
                    $this->menuOffsetSetRecursive($item_key, $item);
                }
            }
            $this->next();
        }

        $this->seek($old_position);
    }

    /**
     * Test an item entry
     *
     * @param   mixed   $item
     * @return  bool
     */
    protected function _validItem($item) 
    {
        return !empty($item) && (
            ($item instanceof MenuItemIterator && $item->offsetExists('level')) ||
            (is_array($item) && isset($item['level']))
        );
    }
    
    /**
     * Initialize an item entry (a default one is created if so)
     *
     * @param   \ArrayAccess    $entry
     * @param   array           $default    Values used if `$entry` is empty
     */
    protected function _initItem(&$entry, array $default = array()) 
    {
        if (empty($entry)) {
            $entry = new MenuItemIterator($default);
        }
        if (!is_object($entry)) {
            if (is_array($entry)) {
                $entry = new MenuItemIterator($entry);
            } else {
                $entry = new MenuItemIterator($default);
            }
        }
    }

    /**
     * Defines the global object first level (highest title entry)
     */
    protected function _findHighestLevel()
    {
        $this->base_level = 0;
        $this->base_level_tmp = 1;
        while ($this->base_level===0 && in_array($this->base_level_tmp, self::$range_levels)) {
            $first_levels_array = array_filter(parent::getArrayCopy(), array($this, '_filter'));
            if (!empty($first_levels_array)) break;
            $this->base_level_tmp++;
        }
        $this->base_level = $this->base_level_tmp;
        $this->rewind();
    }

    /**
     * Internal filter for the `_findHighestLevel()` method
     */
    private function _filter($item) 
    {
        return (isset($item['level']) && $item['level']===$this->base_level_tmp);
    }
    
    /**
     * Find the current menu entry to work on, based on the `$current_keys` array
     *
     * @return  \ArrayAccess    The item to work on
     */
    protected function _menuGetRecursivePath()
    {
        foreach ($this->current_keys as $i=>$_ind) {
            if (!is_null($_ind)) {
                if ($i===$this->base_level) {
                    if (!$this->menu_iterator->offsetExists($_ind)) {
                        $entry = null;
                        $this->_initItem($entry, array(
                            'level'=>$i
                        ));
                        $this->menu_iterator->offsetSet($_ind, $entry);
                    }
                    $item = $this->menu_iterator->offsetGet($_ind);
                } elseif($i>$this->base_level) {
                    if (!$item->hasChild($_ind)) {
                        $entry = null;
                        $this->_initItem($entry, array(
                            'level'=>$i
                        ));
                        $item->addChild($_ind, $entry);
                    }
                    $item = $item->getChild($_ind);
                }
            }
        }
        return $item;
    }

    /**
     * Set a recursive key value, deleting all next keys
     *
     * @param   int         $index
     * @param   int/string  $key
     */
    protected function _setRecursiveKey($index, $key)
    {
        foreach (self::$range_levels as $i) {
            if ($i===$key) {
                $this->menuKeySet($key, $index);
            } elseif ($i>$key) {
                $this->menuKeySet($i, null);
            }
        }
    }
    
}

// Endfile
