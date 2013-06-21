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
class TableOfContentsBIS extends Filter
{

    private $dbg = true;

    protected $formater;
    protected $iterator;
    protected $toc;
    protected $base_level = 1;
    protected $current_key;
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
        $this->base_level = 1;
        $this->current_key = null;
	}

    /**
     * @param string $text
     * @return string
     */
    public function transform($text) 
    {
        $menu = MarkdownExtended::getContent()->getMenu();
        $this->formater = MarkdownExtended::get('OutputFormatBag');
        
echo '<pre>';

        $this->iterator = new \ArrayIterator($menu);

        // first levels: try with 1
        $first_levels_array = array_filter((array) $this->iterator, array($this, '_filter'));
        if (empty($first_levels_array)) {
            $first_item = $this->iterator->current();
            $this->base_level = $first_item['level'];
            $this->iterator->rewind();
            $first_levels_array = array_filter((array) $this->iterator, array($this, '_filter'));
        }
        $this->toc = new \ArrayIterator($first_levels_array);

        $this->_apply($this->toc, $this->iterator, $this->base_level);

if ($this->dbg) var_export($this->toc);

        $toc_html = '';
        $toc_html .= $this->formater->buildTag('title', 'Table of contents', array(
            'level'=>isset($attributes['title_level']) ? $attributes['title_level'] : '4',
            'id'=>isset($attributes['title_id']) ? $attributes['title_id'] : 'toc'
        ));
        $toc_html .= $this->_doItems();

echo $toc_html;

exit('yo');

        MarkdownExtended::getContent()
            ->setMenuHtml($toc);
        return $text;
    }

    /**
     */
    protected function _filter($item) 
    {
        return (isset($item['level']) && $item['level']===$this->base_level);
    }
    
    /**
     */
    protected function _apply(
        \ArrayIterator &$target_iterator, \ArrayIterator $source_iterator, 
        $current_level, $current_key = 0
    ) {
if ($this->dbg) echo '<br />#########################################<br />WORKING ON TARGET '.var_export($target_iterator,1);
        
        $position = 0;
        while ($source_iterator->valid()) { 
            $item = $source_iterator->current();
            $key = $source_iterator->key();
            $diff = ($item['level']-$current_level);

if ($this->dbg) echo '<hr />';
if ($this->dbg) echo '<br />POSITION IS '.$position;
if ($this->dbg) echo '<br />KEY IS '.$key;
if ($this->dbg) echo '<br />CURRENT KEY IS '.$current_key;
if ($this->dbg) echo '<br />CURRENT LEVEL IS '.$current_level;
if ($this->dbg) echo '<br />DIFF IS '.$diff;
if ($this->dbg) echo '<br />ITEM IS '.var_export($item,1);

            if ($diff===0) {
                $this->_initItem(
                    $item,
                    array('level'=>$item['level'])
                );
                $target_iterator->offsetSet($key, $item);
                $current_key = $key;
if ($this->dbg) echo '<br />APPENDING ITEM ...';
if ($this->dbg) echo '<br />CURRENT KEY SET TO '.$current_key;

            } elseif ($diff>0) {
                $child_item =& $target_iterator;
                for ($i=0; $i<$diff; $i++) {
                    if ($current_key!==0 && $child_item->offsetExists($current_key)) {
                        $child_item =& $child_item->offsetGet($current_key);
                        $child_key = $current_key;
                    } elseif($child_item->current()!==null) {
                        $child_item =& $child_item->current();
                        $child_key = $child_item->key();
                    }
                    $this->_initItem(
                        $child_item,
                        array('level'=>$current_level+$i)
                    );
                }
                $child_item['children']->offsetSet($key, $item);
//                $target_iterator->offsetSet($current_key, $global_item);

if ($this->dbg) echo '<br />ADDING ITEM TO CHILD ITEM '.var_export($child_item,1);

            } else {
/*
                $positional_diff = (-$diff)+1;
                if ($position>$positional_diff && $position<$source_iterator->count()+$positional_diff) {
                    $source_iterator->seek($position-$positional_diff);
                }
*/
                return;
            }

            $position++;
            $source_iterator->next(); 
        }
    }
    
    /**
     */
    protected function _initItem(&$item, array $default = array(), $add_children = true) 
    {
        if (empty($item)) {
            $item = new \ArrayIterator($default);
        }
        if (!is_object($item)) {
            if (is_array($item)) {
                $item = new \ArrayIterator($item);
            } else {
                $item = new \ArrayIterator($default);
            }
        }
        if ($add_children && !$item->offsetExists('children')) {
            $item->offsetSet('children', new \ArrayIterator);
        }
        return;
    }

    /**
     */
    protected function _doItems() 
    {
        $content = '';
        if (!empty($this->toc)) {
            foreach ($this->toc as $item_id=>$menu_item) {
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
            if (!empty($entry['text'])) {
                $item_content = $this->formater->buildTag('link', $entry['text'], array(
                    'href'=>'#'.$id,
                    'title'=>'Reach this section'
                ));
            }
            if (!empty($entry['children'])) {
                $children_content = '';
                foreach ($entry['children'] as $item_id=>$menu_item) {
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
