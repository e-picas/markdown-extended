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
use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * HTML output Helper
 * @package MarkdownExtended\OutputFormat
 */
class HTMLHelper
    implements MDE_API\OutputFormatHelperInterface
{

    /**
     * Get a complete version of parsed content, including metadata, body and notes
     *
     * @param   \MarkdownExtended\API\ContentInterface          $md_content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @param   bool    $full_html Defines if the metadata must be returned in a `<head>` block
     * @param   bool    $include_toc Include a table-of-content (default is `false`)
     * @param   string  $html_tag Defines the HTML header tag
     * @return  string
     */
    public function getFullContent(MDE_API\ContentInterface $md_content, MDE_API\OutputFormatInterface $formatter, $full_html = true, $include_toc = false, $html_tag = '<!DOCTYPE html>')
    {
        $content = '';
        $title_done = false;

        if ($full_html) {
            $content .= "$html_tag\n<head>\n";
            if ($md_content->getCharset()) {
                $content .= "<meta charset=\"{$md_content->getCharset()}\" />\n";
            }
        }

        // metadata
        if ($md_content->getMetadata()) {
            $special_metadata = MarkdownExtended::getConfig('special_metadata');
            foreach ($md_content->getMetadata() as $meta_name=>$meta_content) {
                if (!in_array($meta_name, $special_metadata)) {
                    if ($meta_name=='title') {
                        $content .= $formatter->buildTag('meta_title', $meta_content);
                        $title_done = true;
                    } else {
                        $content .= $formatter->buildTag('meta_data', null, array(
                            'name'=>$meta_name,
                            'content'=>$meta_content
                        )) . "\n";
                    }
                }
            }
            // last update
            if ($md_content->getLastUpdate()) {
                $content .= $formatter->buildTag('meta_data', null, array(
                    'http-equiv'=>'last-modified',
                    'content'=>
                        gmdate('D, d M Y H:i:s \G\M\T', $md_content->getLastUpdate()->getTimestamp())
                )) . "\n";
            }
        }

        // force title if so
        if ($full_html && !$title_done && $md_content->getTitle()) {
            $content .= $formatter->buildTag('meta_title', $md_content->getTitle()) . "\n";
        }

        if ($full_html) $content .= "</head><body>\n";

        // toc
        if ($md_content->getMenu()) {
            $content .= self::getToc($md_content, $formatter);
        }

        // body
        if ($md_content->getBody()) {
            $content .= $md_content->getBody();
        }

        // notes
        if ($md_content->getNotes()) {
            $notes_content = '';
            foreach ($md_content->getNotes() as $id=>$note_content) {
                $notes_content .= $formatter->buildTag('ordered_list_item', $note_content['text'], array(
                    'id' => !empty($note_content['note-id']) ? $note_content['note-id'] : $id
                )) . "\n";
            }
            $notes_content = $formatter->buildTag('ordered_list', $notes_content) . "\n";
            $content .= $formatter->buildTag('block', $notes_content, array('class'=>'footnotes')) . "\n";
        }

        // last update
        if ($md_content->getLastUpdate()) {
            $content .= $formatter->buildTag('paragraph', 'Last updated at '.$md_content->getLastUpdate()->format('r')) . "\n";
        }

        if ($full_html) $content .= "</body>\n</html>\n";

        return $content;
    }

    /**
     * Build a hierarchical menu
     *
     * @param   \MarkdownExtended\API\ContentInterface          $md_content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @param   null/array   $attributes
     * @return  string
     */
    public function getToc(MDE_API\ContentInterface $md_content, MDE_API\OutputFormatInterface $formatter, array $attributes = null)
    {
        $menu = $md_content->getMenu();
        $content = $list_content = '';
        if (!empty($menu)) {
            $depth = 0;
            $current_level = null;
            foreach ($menu as $item_id=>$menu_item) {
                $diff = $menu_item['level']-(is_null($current_level) ? $menu_item['level'] : $current_level);
                if ($diff > 0) {
                    $list_content .= str_repeat('<ul><li>', $diff);
                } elseif ($diff < 0) {
                    $list_content .= str_repeat('</li></ul></li>', -$diff);
                    $list_content .= '<li>';
                } else {
                    if (!is_null($current_level)) $list_content .= '</li>';
                    $list_content .= '<li>';
                }
                $depth += $diff;
                $list_content .= $formatter->buildTag('link', $menu_item['text'], array(
                    'href'=>'#'.$item_id,
                    'title'=>'Reach this section'
                ));
                $current_level = $menu_item['level'];
            }
            if ($depth!=0) {
                $list_content .= str_repeat('</ul></li>', $depth);
            }

            $content .= $formatter->buildTag(
                'title',
                (isset($attributes['title_string']) ? $attributes['title_string'] : 'Table of contents'),
                array(
                    'level'=>isset($attributes['title_level']) ? $attributes['title_level'] : '4',
                    'id'=>isset($attributes['title_id']) ? $attributes['title_id'] : 'toc'
                ));
            $content .= $formatter->buildTag(
                'unordered_list',
                $list_content,
                array(
                    'class'=>isset($attributes['class']) ? $attributes['class'] : 'toc-menu',
                ));
        }
        return $content;
    }

}

// Endfile
