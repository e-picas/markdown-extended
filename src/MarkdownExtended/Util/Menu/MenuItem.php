<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util\Menu;

/**
 * Class to use for each menu item
 */
class MenuItem
{
    /**
     * @var int Level of the item
     */
    protected $level;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $attributes   = array();

    /**
     * @var array
     */
    protected $children     = array();

    /**
     * @var int
     */
    protected $parent_index = 0;

    /**
     * Initializes object
     *
     * @param string|null $content
     * @param int $level
     * @param array $attributes
     */
    public function __construct($content = null, $level = 1, array $attributes = array())
    {
        $this->setLevel($level);
        if (!empty($content)) {
            $this->setContent($content);
        }
        if (!empty($attributes)) {
            foreach ($attributes as $var=>$val) {
                $this->setAttribute($var, $val);
            }
        }
    }

    /**
     * Exports a menu item to an array of data
     *
     * @return array
     */
    public function __toArray()
    {
        $data = array(
            'content'       => $this->getContent(),
            'attributes'    => $this->getAttributes(),
        );
        if ($this->hasChildren()) {
            $children = array();
            foreach ($this->getChildren() as $i=>$child) {
                $children[$i] = $child->__toArray();
            }
            $data['children'] = $children;
        }
        return $data;
    }

    /**
     * Sets item's level
     *
     * @param int $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->level = (int) $level;
        return $this;
    }

    /**
     * Gets item's level
     *
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Sets item's parent index
     *
     * @param int $index
     * @return $this
     */
    public function setParentIndex($index)
    {
        $this->parent_index = $index;
        return $this;
    }

    /**
     * Gets item's parent index
     *
     * @return int
     */
    public function getParentIndex()
    {
        return $this->parent_index;
    }

    /**
     * Sets item's content (menu link text)
     *
     * @param string $content
     * @return $this
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * Gets item's content (menu link text)
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Sets an item's attribute value
     *
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    /**
     * Gets an item's attribute value
     *
     * @param string $name
     * @return mixed|null
     */
    public function getAttribute($name)
    {
        return isset($this->attributes[$name]) ? $this->attributes[$name] : null;
    }

    /**
     * Gets item's attributes array
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Sets item's children as an array of `MenuItem` objects
     *
     * @param array $children
     * @return $this
     */
    public function setChildren(array $children)
    {
        foreach ($children as $child) {
            $this->addChildren($child);
        }
        return $this;
    }

    /**
     * Adds a menu item
     *
     * @param \MarkdownExtended\Util\Menu\MenuItem $child
     * @return $this
     */
    public function addChildren(MenuItem $child)
    {
        $this->children[] = $child;
        return $this;
    }

    /**
     * Tests if the object contains children
     *
     * @return bool
     */
    public function hasChildren()
    {
        return (bool) (count($this->children) > 0);
    }

    /**
     * Gets the whole children items array
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }
}
