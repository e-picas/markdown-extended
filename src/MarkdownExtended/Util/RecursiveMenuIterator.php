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
 * see <http://thereisamoduleforthat.com/content/dealing-deep-arrays-php>
 */
class RecursiveMenuIterator 
    extends \RecursiveArrayIterator implements \RecursiveIterator
{

    public static $range_levels = array(1,2,3,4,5,6);

    protected $position = 0;
    protected $base_level = 0;
    protected $current_keys = array();
    protected $menu_iterator = null;

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
        $this->_initItem($newval);
        $this->menu_iterator->offsetSet($index, $newval);
        $this->menuKeySet($newval['level'], $index);
    }

    public function menuOffsetSetRecursive($index, $newval, array $recursive_keys)
    {
        $item =& $this->_menuGetRecursivePath($this->menu_iterator, $recursive_keys);

echo '<br />SEARCHING KEYS : '.var_export($recursive_keys,1);
echo '<br />=> ITEM IS : '.var_export($item,1);

        $this->_initItem($newval);
        $item->offsetSet($index, $newval);
        $this->menuKeySet($newval['level'], $index);
    }

    protected function _rebuild()
    {
        $old_position = $this->position();
        if ($this->base_level===0) {
            $this->_findHighestLevel();
        }

        $this->rewind();
        while ($this->valid()) {
            if ($this->_validItem($this->current())) {
            
                if ($item['level']===$this->base_level) {
                    $this->menuOffsetSet($this->key(), $this->current());
                } else {
                    $this->menuOffsetSetRecursive($this->key(), $this->current(), $this->current_keys);
                }
            
            }
            $this->next();
echo '<br />### TEMP MENU IS : ';
var_dump($this->menu_iterator);
        }
echo '<br />### FINAL MENU : ';        
var_dump($this->menu_iterator);
        $this->seek($old_position);
    }

    protected function _validItem($item) 
    {
        return !empty($item) && (
            ($item instanceof \ArrayIterator && $item->offsetExists('level')) ||
            (is_array($item) && isset($item['level']))
        );
    }
    
    protected function _initItem(&$entry, array $default = array()) 
    {
        if (empty($entry)) {
            $entry = new \ArrayIterator($default);
        }
        if (!is_object($entry)) {
            if (is_array($entry)) {
                $entry = new \ArrayIterator($entry);
            } else {
                $entry = new \ArrayIterator($default);
            }
        }
        if (!$entry->offsetExists('children')) {
            $entry->offsetSet('children', new \ArrayIterator);
        }
    }

    protected function _findHighestLevel()
    {
        $this->base_level = 1;
    }

    protected function _menuGetRecursivePath(&$entries, array $recursive_keys)
    {
        $item =& $entries;
        foreach ($recursive_keys as $i=>$_ind) {
            if (!is_null($_ind)) {
                if (!$item->offsetExists($_ind)) {
                    $entry = null;
                    $this->_initItem($entry, array(
                        'level'=>$i
                    ));
                    $item->offsetSet($_ind, $entry);
                }
                $item =& $item->offsetGet($_ind)->offsetGet('children');
            }
        }
        return $item;
    }

}

// Endfile
