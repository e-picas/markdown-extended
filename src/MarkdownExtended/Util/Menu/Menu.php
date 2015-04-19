<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util\Menu;

/**
 * Class to render a menu as a complex array of items with recursive children
 */
class Menu
{

    /**
     * @var array
     */
    protected $items;

    private $_indexes = array();

    public static function create(array $items)
    {
        $menu = new Menu;
        foreach ($items as $item) {
            if (empty($item)) {
                continue;
            }
            $menu->addItem(new MenuItem(
                $item['text'],
                $item['level'],
                array('id' => $item['id'])
            ));
        }
        return $menu;
    }

    /**
     * Initializes object
     */
    public function __construct()
    {
        $this->items = array();
        for ($i = 0; $i < 7; $i++) {
            $this->_indexes[$i] = 0;
        }
    }

    /**
     * Gets the menu items array
     *
     * @return array
     */
    public function getItems()
    {
        $data = array();
        foreach ($this->items as $key=>$item) {
            /* @var $item \MarkdownExtended\Util\Menu\MenuItem */
            $parent = $item->getParentIndex();
            if (!isset($data[$parent])) {
                $data[$parent] = array();
            }
            $data[$parent][$key] = $item;
        }

        $append_children = function ($arr, $children) use (&$append_children) {
            foreach ($arr as $key => $page) {
                if (isset($children[$key])) {
                    $arr[$key]->setChildren($append_children($children[$key], $children));
                }
            }
            return $arr;
        };

        $data = $append_children($data[0], $data);
        return $data;
    }

    /**
     * Add a menu entry
     *
     * @param \MarkdownExtended\Util\Menu\MenuItem $val
     * @return $this
     */
    public function addItem(MenuItem $val)
    {
        $level = $val->getLevel();
        $this->_indexes[0]++;
        $this->_indexes[$level] = $this->_indexes[0];
        $parent_level = $level - 1;
        while ($parent_level > 0 && $this->_indexes[$parent_level]===0) {
            $parent_level = $parent_level - 1;
        }
        if ($parent_level > 0) {
            $val->setParentIndex($this->_indexes[$parent_level]);
        }
        $this->items[$this->_indexes[0]] = $val;
        return $this;
    }
}
