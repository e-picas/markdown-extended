<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\API\OutputFormatInterface;
use \MarkdownExtended\API\OutputFormatHelperInterface;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Default Markdown Extended output Helper
 * @package MarkdownExtended\OutputFormat
 */
class DefaultHelper
    implements OutputFormatHelperInterface
{

    /**
     * Get a complete version of parsed content, including metadata, body and notes
     *
     * @param   \MarkdownExtended\API\ContentInterface          $md_content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formater
     * @return  string
     */
    public function getFullContent(ContentInterface $md_content, OutputFormatInterface $formater)
    {
        $content = '';

        // charset
        if ($md_content->getCharset()) {
            $content .= $formater->buildTag('meta_data', $md_content->getCharset(), array('name'=>'charset'));
        }

        // metadata
        if ($md_content->getMetadata()) {
            $special_metadata = MarkdownExtended::getConfig('special_metadata');
            foreach ($md_content->getMetadata() as $meta_name=>$meta_content) {
                if (!in_array($meta_name, $special_metadata)) {
                    if ($meta_name=='title') {
                        $content .= $formater->buildTag('meta_title', $meta_content);
                    } else {
                        $content .= $formater->buildTag('meta_data', $meta_content, array('name'=>$meta_name));
                    }
                }
            }
        }

        // page title
        if ($md_content->getTitle()) {
            $content .= $formater->buildTag('title', $md_content->getTitle());
        }

        // toc
        if ($md_content->getMenu()) {
            $content .= $this->getToc($md_content, $formater);
        }

        // body
        if ($md_content->getBody()) {
            $content .= $md_content->getBody();
        }

        // notes
        if ($md_content->getNotes()) {
            $notes_content = '';
            foreach ($md_content->getNotes() as $id=>$note_content) {
                $notes_content .= $formater->buildTag('ordered_list_item', $note_content['text'], $note_content);
            }
            $content .= $formater->buildTag('ordered_list', $notes_content, array('type'=>'footnotes'));
        }

        return $content;
    }

    /**
     * Build a hierarchical menu
     *
     * @param   \MarkdownExtended\API\ContentInterface          $md_content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formater
     * @return  string
     *
     * @todo rewrite it without HTML (!)
     */
    public function getToc(ContentInterface $md_content, OutputFormatInterface $formater)
    {
        $menu = $md_content->getMenu();
        $content = '';
        if (!empty($menu)) {
            $depth = 0;
            $current_level = null;

            $toc_title  = MarkdownExtended::getConfig('toc_title');
            $toc_id     = MarkdownExtended::getConfig('toc_id');
            $content    .= $formater->buildTag(
                'title',
                (!empty($toc_title) ? $toc_title : 'Table of contents'),
                array(
                    'id'=>(!empty($toc_id) ? $toc_id : 'toc')
                )
            );

            $menu_content = '';
            foreach ($menu as $item_id=>$menu_item) {
                $diff = $menu_item['level']-(is_null($current_level) ? $menu_item['level'] : $current_level);
                if ($diff > 0) {
                    $menu_content .= str_repeat('<ul><li>', $diff);
                } elseif ($diff < 0) {
                    $menu_content .= str_repeat('</li></ul></li>', -$diff);
                    $menu_content .= '<li>';
                } else {
                    if (!is_null($current_level)) $content .= '</li>';
                    $menu_content .= '<li>';
                }
                $depth += $diff;
                $menu_content .= '<a href="#'.$item_id.'">'.$menu_item['text'].'</a>';
                $current_level = $menu_item['level'];
            }
            if ($depth!=0) {
                $menu_content .= str_repeat('</ul></li>', $depth);
            }
            $content .= 'YO'.$formater->buildTag(
                'unordered_list',
                $menu_content,
                array(
                    'class'=>(!empty($toc_list_class) ? $toc_list_class : 'toc-menu')
                )
            );
        }
        return $content;
    }

}

// Endfile
