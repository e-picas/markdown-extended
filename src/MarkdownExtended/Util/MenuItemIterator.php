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

use \ArrayIterator;
use \ArrayAccess;

/**
 * This class defines each `RecursiveMenuIterator` item
 */
class MenuItemIterator 
    extends ArrayIterator
    implements ArrayAccess
{

    /**
     * @static array
     */
    public static $defaults = array(
        'level', 'content', 'attribtues', 'children'
    );

    /**
     * @param array|string $content The content of the item, or the full array of item values
     * @param int $level
     * @param array $attributes
     * @param array $children
     * @param int $flags
     *
     * @see parent::__construct()
     */
    public function __construct($content = null, $level = null, array $attributes = null, array $children = null, $flags = 0)
    {
        parent::__construct($this->getDefault(), $flags);
        $array = array();
        if (is_array($content)) {
            $array = $content;
        } else {
            if (!empty($content)) $array['content'] = $content;
            if (!empty($level)) $array['level'] = $level;
            if (!empty($attributes)) $array['attributes'] = $attributes;
            if (!empty($children)) $array['children'] = $children;
        }
        $this->init($array);
    }

    /**
     * Initialize a new item with an array of values
     *
     * The array indexes are:
     * -   "level": the level of the title item (int)
     * -   "content" or "text": the text of the title (string)
     * -   "attributes": an array of title tag attributes (array)
     * -   "children": an array or ArrayAccess object of the item children (ArrayAccess)
     *
     * @param array $values
     */
    public function init(array $values)
    {
        if (!empty($values)) {
            if (array_key_exists('level', $values)) {
                $this->setLevel($values['level']);
            }
            if (array_key_exists('content', $values)) {
                $this->setContent($values['content']);
            } elseif (array_key_exists('text', $values)) {
                $this->setContent($values['text']);
            }
            if (array_key_exists('attributes', $values)) {
                $this->setAttributes($values['attributes']);
            }
            if (array_key_exists('children', $values)) {
                $this->setChildren($values['children']);
            }
        }
    }

    /**
     * Get a default empty item
     *
     * @return array
     */
    public function getDefault()
    {
        return array(
            'level'=>null,
            'content'=>null,
            'attributes'=>array(),
            'children'=>new \ArrayIterator(array()),
        );
    }

// ----------------------
// Setters / Getters
// ----------------------

    /**
     * Set the item level
     *
     * @param int $level
     *
     * @throws OutOfBoundsException if `$level` is not in `RecursiveMenuIterator::$range_levels`
     */
    public function setLevel($level)
    {
        if (!in_array($level, RecursiveMenuIterator::$range_levels)) {
            throw new \OutOfBoundsException(
                sprintf('Invalid "level" value for %s object: %d!', __CLASS__, $level)
            );
        }
        parent::offsetSet('level', $level);
    }

    /**
     * Get the item level
     *
     * @return int
     */
    public function getLevel()
    {
        return parent::offsetGet('level');
    }

    /**
     * Set the item content
     *
     * @param string $content
     *
     * @throws InvalidArgumentException if `$content` is not a string
     */
    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid "content" value for %s object: %d (must be a string)!', __CLASS__, gettype($content))
            );
        }
        parent::offsetSet('content', $content);
    }

    /**
     * Get the item content
     *
     * @return string
     */
    public function getContent()
    {
        return parent::offsetGet('content');
    }

    /**
     * Define the item attributes array
     *
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        parent::offsetSet('attributes', $attributes);
    }

    /**
     * Add a new item attribute
     *
     * @param int|string $index
     * @param misc $value
     */
    public function addAttribute($index, $value)
    {
        $attributes = $this->getAttributes();
        $attributes[$index] = $value;
        parent::offsetSet('attributes', $attributes);
    }

    /**
     * Test if an item has one or more attribute
     *
     * @return bool
     */
    public function hasAttributes()
    {
        $attributes = parent::offsetGet('attributes');
        return (!empty($attributes));
    }

    /**
     * Retrieve the item attributes array
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->hasAttributes() ? parent::offsetGet('attributes') : array();
    }

    /**
     * Define an item children stack
     *
     * @param object $children \ArrayAccess
     */
    public function setChildren(\ArrayAccess $children)
    {
        parent::offsetSet('children', $children);
    }

    /**
     * Add a new item child
     *
     * @param int|string $index
     * @param object $value \ArrayAccess
     */
    public function addChild($index, \ArrayAccess $value)
    {
        $children = $this->getChildren();
        $children->offsetSet($index, $value);
        parent::offsetSet('children', $children);
    }

    /**
     * Test if the item has one or more children
     *
     * @return bool
     */
    public function hasChildren()
    {
        $children = parent::offsetGet('children');
        return (!empty($children) && $children->count()!==0);
    }

    /**
     * Test if a child exists by its index
     *
     * @param int|string $index
     *
     * @return bool
     */
    public function hasChild($index)
    {
        if ($this->hasChildren()) {
            $children = parent::offsetGet('children');
            return $children->offsetExists($index);
        }
        return false;
    }

    /**
     * Get the item children
     *
     * @param object \ArrayAccess
     */
    public function getChildren()
    {
        return $this->hasChildren() ? parent::offsetGet('children') : new \ArrayIterator;
    }

    /**
     * Get a child by its index
     *
     * If the index doesn't exist, an empty child is returned.
     *
     * @param int|string $index
     *
     * @return MenuItemIterator
     */
    public function getChild($index)
    {
        $_cls = __CLASS__;
        $children = $this->hasChildren() ? parent::offsetGet('children') : new \ArrayIterator;
        return $children->offsetExists($index) ? $children->offsetGet($index) : new $_cls($this->getDefault());
    }

// ----------------------
// ArrayAccess overrides
// ----------------------

    /**
     * Set a new value for a property if it exists in the class
     *
     * @param string $index
     * @param misc $value
     *
     * @throws InvalidArgumentException if `$index` is not valid
     */
    public function offsetSet($index, $newval)
    {
        $_meth = 'set'.ucfirst($index);
        if (method_exists($this, $_meth)) {
            call_user_func_array(
                array($this, $_meth), array($newval)
            );
        } else {
            throw new \InvalidArgumentException(
                sprintf('Invalid setter index for %s object: %d!', __CLASS__, $index)
            );
        }
    }

    /**
     * Unset a property value if it exists in the class
     *
     * @param string $index
     *
     * @throws InvalidArgumentException if `$index` is not valid
     */
    public function offsetUnset($index)
    {
        $_meth = 'set'.ucfirst($index);
        if (method_exists($this, $_meth)) {
            call_user_func_array(
                array($this, $_meth), array(null)
            );
        } else {
            throw new \InvalidArgumentException(
                sprintf('Invalid unsetter index for %s object: %d!', __CLASS__, $index)
            );
        }
    }

    /**
     * Retrieve a property value if it exists in the class
     *
     * @param string $index
     *
     * @return misc
     */
    public function offsetGet($index)
    {
        $_meth = 'get'.ucfirst($index);
        if (method_exists($this, $_meth)) {
            return call_user_func(array($this, $_meth));
        } else {
            throw new \InvalidARgumentException(
                sprintf('Invalid getter index for %s object: %d!', __CLASS__, $index)
            );
        }
    }

    /**
     * Test if an index exists in the class and if the property is not empty
     *
     * @param string $index
     *
     * @return bool
     */
    public function offsetExists($index)
    {
        $_meth = 'get'.ucfirst($index);
        if (method_exists($this, $_meth)) {
            $return = call_user_func(array($this, $_meth));
            return !empty($return);
        } else {
            return false;
        }
    }

    /**
     * Append a new child to item children with a uniq item index
     *
     * @param misc $value
     */
    public function append($value)
    {
        if (!($value instanceof \ArrayAccess)) {
            if (!is_array($value)) $value = array($value);
            $iterator_value = new \ArrayIterator($value);
        } else {
            $iterator_value = $value;
        }
        if ($iterator_value->offsetExists('attibutes')) {
            $attributes = $iterator_value->offsetGet('attributes');
            if (array_key_exists('id', $attributes)) {
                $index = $attributes['id'];
            }
        }
        if (empty($index)) {
            $index = uniqid();
        }
        $this->addChild($index, $iterator_value);
    }

}

// Endfile
