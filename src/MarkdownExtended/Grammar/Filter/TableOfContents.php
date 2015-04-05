<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use \MarkdownExtended\API\Kernel;

/**
 * Construct the global Table of Contents (hierarchical list of anchors)
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class TableOfContents
    extends Filter
{

    /**
     * @var     array
     */
    protected $attributes = array();

    /**
     * Reset all properties at the begining
     */
    public function _setup()
    {
        $this->attributes = Kernel::getConfig('table_of_contents');
    }

    /**
     * @param   string  $text
     * @return  string
     */
    public function transform($text) 
    {
        $menu = Kernel::get('Content')->getMenu();
        if (empty($menu) || !is_array($menu)) $menu = array($menu);

        $toc = new \MarkdownExtended\Util\RecursiveMenuIterator(
            new \ArrayIterator($menu)
        );

        $toc_tostring = '';
        $toc_tostring .= Kernel::get('OutputFormatBag')
            ->buildTag(
                'title',
                isset($this->attributes['title']) ? $this->attributes['title'] : 'Table of contents',
                array(
                    'level'=>isset($this->attributes['title_level']) ? $this->attributes['title_level'] : '4',
                    'id'=>isset($this->attributes['title_id']) ? $this->attributes['title_id'] : 'toc'
                ));
        $toc_tostring .= $this->_doItems($toc);

        Kernel::get('Content')
            ->setToc($toc)
            ->setTocToString($toc_tostring)
            ;
        return $text;
    }

    /**
     */
    protected function _doItems(\MarkdownExtended\Util\RecursiveMenuIterator $toc) 
    {
        $content = '';
        if (!empty($toc)) {
            foreach ($toc->getArrayCopy() as $item_id=>$menu_item) {
                $content .= $this->_doItemsRecursive($menu_item, $item_id);
            }
            if (!empty($content)) {
                $content = Kernel::get('OutputFormatBag')
                    ->buildTag('unordered_list', $content, array(
                        'class'=>isset($this->attributes['class']) ? $this->attributes['class'] : 'toc-menu',
                    ));
            }
        }
        return $content;
    }

    /**
     */
    protected function _doItemsRecursive($entry, $id) 
    {
        $item_content = '';
        if (!empty($entry)) {
//            if ($entry->getContent()) {
                 $attributes = $entry->getAttributes();
                 $item_content = Kernel::get('OutputFormatBag')
                     ->buildTag('link', $entry->getContent(), array_merge($attributes, array(
                        'href'=>'#'.$id,
                        'title'=>'Reach this section'
                    )));
//            }
            if ($entry->hasChildren()) {
                $children_content = '';
                foreach ($entry->getChildren() as $item_id=>$menu_item) {
                    $children_content .= $this->_doItemsRecursive($menu_item, $item_id);
                }
                if (!empty($children_content)) {
                    $item_content .= Kernel::get('OutputFormatBag')
                        ->buildTag('unordered_list', $children_content);
                }
            }
            if (!empty($item_content)) {
                return Kernel::get('OutputFormatBag')
                    ->buildTag('unordered_list_item', $item_content);
            }
        }
        return $item_content;
    }

}

// Endfile
