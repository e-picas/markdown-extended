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
class MenuItemIterator 
    extends \ArrayIterator implements \ArrayAccess
{

    public static $defaults = array(
        'level', 'content', 'attribtues', 'children'
    );

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

    public function setLevel($level)
    {
        if (!in_array($level, RecursiveMenuIterator::$range_levels)) {
            throw new \OutOfBoundsException(
                sprintf('Invalid "level" value for %s object: %d!', __CLASS__, $level)
            );
        }
        parent::offsetSet('level', $level);
    }

    public function getLevel()
    {
        return parent::offsetGet('level');
    }

    public function setContent($content)
    {
        if (!is_string($content)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid "content" value for %s object: %d (must be a string)!', __CLASS__, gettype($content))
            );
        }
        parent::offsetSet('content', $content);
    }

    public function getContent()
    {
        return parent::offsetGet('content');
    }

    public function setAttributes(array $attributes)
    {
        parent::offsetSet('attributes', $attributes);
    }

    public function addAttribute($index, $value)
    {
        $attributes = $this->getAttributes();
        $attributes[$index] = $value;
        parent::offsetSet('attributes', $attributes);
    }

    public function hasAttributes()
    {
        $attributes = parent::offsetGet('attributes');
        return (!empty($attributes));
    }

    public function getAttributes()
    {
        return $this->hasAttributes() ? parent::offsetGet('attributes') : array();
    }

    public function setChildren(\ArrayAccess $children)
    {
        parent::offsetSet('children', $children);
    }

    public function addChild($index, \ArrayAccess $value)
    {
        $children = $this->getChildren();
        $children->offsetSet($index, $value);
        parent::offsetSet('children', $children);
    }

    public function hasChildren()
    {
        $children = parent::offsetGet('children');
        return (!empty($children) && $children->count()!==0);
    }

    public function hasChild($index)
    {
        if ($this->hasChildren()) {
            $children = parent::offsetGet('children');
            return $children->offsetExists($index);
        }
        return false;
    }

    public function getChildren()
    {
        return $this->hasChildren() ? parent::offsetGet('children') : new \ArrayIterator;
    }

    public function getChild($index)
    {
        $_cls = __CLASS__;
        $children = $this->hasChildren() ? parent::offsetGet('children') : new \ArrayIterator;
        return $children->offsetExists($index) ? $children->offsetGet($index) : new $_cls($this->getDefault());
    }

// ----------------------
// ArrayAccess overrides
// ----------------------

    public function offsetSet($index, $newval)
    {
        $_meth = 'set'.ucfirst($index);
        if (method_exists($this, $_meth)) {
            call_user_func_array(
                array($this, $_meth), array($newval)
            );
        } else {
            throw new \InvalidARgumentException(
                sprintf('Invalid setter index for %s object: %d!', __CLASS__, $index)
            );
        }
    }

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
