<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
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
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Helper as MDE_Helper;
use MarkdownExtended\Exception as MDE_Exception;

/**
 * Construct the global Table of Contents (hierarchical list of anchors)
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
        $this->attributes = MarkdownExtended::getConfig('table_of_contents');
    }

    /**
     * @param   string  $text
     * @return  string
     */
    public function transform($text) 
    {
        $menu = MarkdownExtended::getContent()->getMenu();
        if (empty($menu) || !is_array($menu)) $menu = array($menu);

        $toc = new \MarkdownExtended\Util\RecursiveMenuIterator(
            new \ArrayIterator($menu)
        );

        $toc_tostring = '';
        $toc_tostring .= MarkdownExtended::get('OutputFormatBag')
            ->buildTag(
                'title',
                isset($this->attributes['title']) ? $this->attributes['title'] : 'Table of contents',
                array(
                    'level'=>isset($this->attributes['title_level']) ? $this->attributes['title_level'] : '4',
                    'id'=>isset($this->attributes['title_id']) ? $this->attributes['title_id'] : 'toc'
                ));
        $toc_tostring .= $this->_doItems($toc);

        MarkdownExtended::getContent()
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
                $content = MarkdownExtended::get('OutputFormatBag')
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
                 $item_content = MarkdownExtended::get('OutputFormatBag')
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
                    $item_content .= MarkdownExtended::get('OutputFormatBag')
                        ->buildTag('unordered_list', $children_content);
                }
            }
            if (!empty($item_content)) {
                return MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('unordered_list_item', $item_content);
            }
        }
        return $item_content;
    }

}

// Endfile
