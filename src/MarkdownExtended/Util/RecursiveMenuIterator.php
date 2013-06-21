<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
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
namespace MarkdownExtended\Util;

/**
 */
class RecursiveMenuIterator 
    extends \RecursiveArrayIterator implements \RecursiveIterator
{

    public static $range_levels = array(1,2,3,4,5,6);

    protected $position = 0;
    protected $base_level = 0;
    protected $current_keys = array();
    protected $menu_iterator = null;

    private $base_level_tmp;

    public function __construct(\ArrayAccess $menu = null, $base_level = 0, $flags = 0)
    {
        $this->menu_iterator = new \ArrayIterator;
        foreach (self::$range_levels as $index) {
            $this->current_keys[$index] = null;
        }
        parent::__construct($menu, $flags);
        if ($base_level!==0 && !in_array($base_level, self::$range_levels)) {
            throw new \OutOfBoundsException(
                sprintf('Invalid "base level" value for %s object: %d!', __CLASS__, $base_level)
            );
        }
        $this->base_level = $base_level;
        if ($this->count()!=0) {
            $this->_rebuild();
        }
    }

    public function next()
    {
        $this->position++;
        parent::next();
    }

    public function previous()
    {
        $this->position--;
        parent::seek($this->position);
    }

    public function rewind()
    {
        $this->position = 0;
        parent::rewind();
    }

    public function seek($position)
    {
        $this->position = $position;
        parent::seek($this->position);
    }

    public function position()
    {
        return $this->position;
    }

    public function append($value)
    {
        parent::append($value);
        $this->_rebuild();
    }

    public function getArrayCopy()
    {
        return (array) $this->menu_iterator;
    }

    public function menuKeySet($index, $key)
    {
        if (!in_array($index, self::$range_levels)) {
            throw new \OutOfBoundsException(
                sprintf('Invalid key "index" value for %s object: %d!', __CLASS__, $index)
            );
        }
        $this->current_keys[$index] = $key;
    }

    public function menuOffsetSet($index, $newval)
    {
        $this->_initItem($newval, array('level'=>$this->base_level));
        $this->menu_iterator->offsetSet($index, $newval);
        $this->_setRecursiveKey($index, $newval['level']);
    }

    public function menuOffsetSetRecursive($index, $newval)
    {
        $this->_setRecursiveKey(null, $newval['level']);
        $item =& $this->_menuGetRecursivePath();
        $this->_initItem($newval);
        $item->addChild($index, $newval);
        $this->_setRecursiveKey($index, $newval['level']);
    }

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

    protected function _validItem($item) 
    {
        return !empty($item) && (
            ($item instanceof MenuItemIterator && $item->offsetExists('level')) ||
            (is_array($item) && isset($item['level']))
        );
    }
    
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

    protected function _filter($item) 
    {
        return (isset($item['level']) && $item['level']===$this->base_level_tmp);
    }
    
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
