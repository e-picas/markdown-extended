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
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\Grammar\Filter,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * Construct the global Table of Contents (hierarchical list of anchors)
 *
 * @todo A REVOIR : IL FAUT D'ABORD RE-ARRANGER LE TABLEAU EN METTANT LES ENFANTS A CHAQUE ITEM ...
 */
class TableOfContents extends Filter
{

    protected $formater;
    protected $iterator;
    protected $toc;
    protected $attributes = array();

	/**
	 * Reset all properties at the begining
	 */
	public function _setup()
	{
	    $this->reset();
	}

	/**
	 * Reset all properties at the end
	 */
	public function _teardown()
	{
	    $this->reset();
	}

	/**
	 * Do reset all properties
	 */
	public function reset()
	{
        $this->formater = null;
        $this->iterator = null;
        $this->toc = null;
	}

    /**
     * @param string $text
     * @return string
     */
    public function transform($text) 
    {
        $menu = MarkdownExtended::getContent()->getMenu();
        if (empty($menu) || !is_array($menu)) $menu = array($menu);
        $this->formater = MarkdownExtended::get('OutputFormatBag');
        $this->iterator = new \ArrayIterator($menu);
        $this->toc = new \MarkdownExtended\Util\RecursiveMenuIterator(
            $this->iterator
        );
//if ($this->dbg) var_dump($this->toc->getArrayCopy());
//if ($this->dbg) var_dump($this->toc);
        $toc_html = '';
        $toc_html .= $this->formater->buildTag('title', 'Table of contents', array(
            'level'=>isset($attributes['title_level']) ? $attributes['title_level'] : '4',
            'id'=>isset($attributes['title_id']) ? $attributes['title_id'] : 'toc'
        ));
        $toc_html .= $this->_doItems();
//echo $toc_html;
//exit('yo');
        MarkdownExtended::getContent()
            ->setMenuHtml($this->toc);
        return $text;
    }

    /**
     */
    protected function _doItems() 
    {
        $content = '';
        if (!empty($this->toc)) {
            foreach ($this->toc->getArrayCopy() as $item_id=>$menu_item) {
                $content .= $this->_doItemsRecursive($menu_item, $item_id);
            }
            if (!empty($content)) {
                $content = $this->formater->buildTag('unordered_list', $content, array(
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
                 $item_content = $this->formater->buildTag('link', $entry->getContent(), array_merge($attributes, array(
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
                    $item_content .= $this->formater->buildTag('unordered_list', $children_content);
                }
            }
            if (!empty($item_content)) {
                return $this->formater->buildTag('unordered_list_item', $item_content);
            }
        }
        return $item_content;
    }

}

// Endfile
