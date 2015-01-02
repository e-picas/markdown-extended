<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Default Markdown Extended output Helper
 * @package MarkdownExtended\OutputFormat
 */
class DefaultHelper
    implements MDE_API\OutputFormatHelperInterface
{

    /**
     * Get a complete version of parsed content, including metadata, body and notes
     *
     * @param   \MarkdownExtended\API\ContentInterface          $md_content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @return  string
     */
    public function getFullContent(MDE_API\ContentInterface $md_content, MDE_API\OutputFormatInterface $formatter)
    {
        $content = '';

        // charset
        if ($md_content->getCharset()) {
            $content .= $formatter->buildTag('meta_data', $md_content->getCharset(), array('name'=>'charset'));
        }

        // metadata
        if ($md_content->getMetadata()) {
            $special_metadata = MarkdownExtended::getConfig('special_metadata');
            foreach ($md_content->getMetadata() as $meta_name=>$meta_content) {
                if (!in_array($meta_name, $special_metadata)) {
                    if ($meta_name=='title') {
                        $content .= $formatter->buildTag('meta_title', $meta_content);
                    } else {
                        $content .= $formatter->buildTag('meta_data', $meta_content, array('name'=>$meta_name));
                    }
                }
            }
        }

        // page title
        if ($md_content->getTitle()) {
            $content .= $formatter->buildTag('title', $md_content->getTitle());
        }

        // toc
        if ($md_content->getMenu()) {
            $content .= $this->getToc($md_content, $formatter);
        }

        // body
        if ($md_content->getBody()) {
            $content .= $md_content->getBody();
        }

        // notes
        if ($md_content->getNotes()) {
            $notes_content = '';
            foreach ($md_content->getNotes() as $id=>$note_content) {
                $notes_content .= $formatter->buildTag('ordered_list_item', $note_content['text'], $note_content);
            }
            $content .= $formatter->buildTag('ordered_list', $notes_content, array('type'=>'footnotes'));
        }

        return $content;
    }

    /**
     * Build a hierarchical menu
     *
     * @param   \MarkdownExtended\API\ContentInterface          $md_content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @return  string
     *
     * @todo rewrite it without HTML (!)
     */
    public function getToc(MDE_API\ContentInterface $md_content, MDE_API\OutputFormatInterface $formatter)
    {
        $menu = $md_content->getMenu();
        $content = '';
        if (!empty($menu)) {
            $depth = 0;
            $current_level = null;

            $toc_title  = MarkdownExtended::getConfig('toc_title');
            $toc_id     = MarkdownExtended::getConfig('toc_id');
            $content    .= $formatter->buildTag(
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
            $content .= $formatter->buildTag(
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
