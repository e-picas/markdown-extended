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

    /**
     * @param string $text
     * @return string
     */
    public function transform($text) 
    {
        $menu = MarkdownExtended::getContent()->getMenu();
        $this->formater = MarkdownExtended::get('OutputFormatBag');

        $toc = $this->_doItems($menu);
        $toc .= $this->formater->buildTag('title', 'Table of contents', array(
            'level'=>isset($attributes['title_level']) ? $attributes['title_level'] : '4',
            'id'=>isset($attributes['title_id']) ? $attributes['title_id'] : 'toc'
        ));
        $toc .= $this->formater->buildTag('unordered_list', $list_content, array(
            'class'=>isset($attributes['class']) ? $attributes['class'] : 'toc-menu',
        ));

var_export($menu);
var_export($toc);

exit('yo');

            MarkdownExtended::getContent()
                ->setMenuHtml($toc);

        return $text;
    }

    /**
     */
    protected function _doItems($entries) 
    {
        $list_content = '';
        if (!empty($entries)) {
            $list_content = $this->_recursiveItems($entries);
        }
        $options = array('class'=>'toc-menu');
        return $this->formater->buildTag('unordered_list', $list_content, $options);
    }

    /**
     */
    protected function _recursiveItems(&$entries, &$current_level = null, &$depth = 0) 
    {
        $list_content = '';

echo '<br />CURRENT LEVEL: '.$current_level;

        if (!empty($entries)) {
            foreach ($entries as $item_id=>$menu_item) {


                $diff = $menu_item['level']-(is_null($current_level) ? $menu_item['level'] : $current_level);


echo '<br />ITEM (diff='.$diff.'): '.var_export($menu_item,1);

                $depth += $diff;
                $item_content = $this->formater->buildTag('link', $menu_item['text'], array(
                    'href'=>'#'.$item_id,
                    'title'=>'Reach this section'
                ));
                $current_level = $menu_item['level'];
 
                if ($diff > 0) {
                    $subitems_content = $this->formater->buildTag(
                        'unordered_list',
                        $this->_recursiveItems($entries, $current_level, $depth)
                    );
                    $list_content .= $this->formater->buildTag('unordered_list_item', $subitems_content);                    
                } elseif ($diff < 0) {
                    $ancestor_subitems_content = !empty($list_content) ? $this->formater->buildTag('unordered_list', $list_content) : '';
                    $list_content = $this->formater->buildTag('unordered_list_item', $ancestor_subitems_content);
                    unset($entries[$item_id]);


echo '<br />BREAKING';

                    break;
                } else {
                    unset($entries[$item_id]);
                    $list_content .= $this->formater->buildTag('unordered_list_item', $item_content);
                }
            }
        }
        return $list_content;
    }
}

// Endfile
