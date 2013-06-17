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
namespace MarkdownExtended\OutputFormat;

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\Content,
    MarkdownExtended\OutputFormatInterface,
    MarkdownExtended\OutputFormatHelperInterface,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * Default Markdown Extended output Helper
 */
class DefaultHelper implements OutputFormatHelperInterface
{

    /**
     * Get a complete version of parsed content, including metadata, body and notes
     *
     * @param object $content \MarkdownExtended\Content
     * @param object $formater \MarkdownExtended\OutputFormatInterface
     *
     * @return string
     */
    public function getFullContent(Content $md_content, OutputFormatInterface $formater)
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
     * @param object $content \MarkdownExtended\Content
     * @param object $formater \MarkdownExtended\OutputFormatInterface
     *
     * @return string
     *
     * @todo rewrite it without HTML (!)
     */
    public function getToc(Content $md_content, OutputFormatInterface $formater)
    {
        $menu = $md_content->getMenu();
        $content = '';
        if (!empty($menu)) {
            $depth = 0;
            $current_level = null;
            $content .= '<h4 id="toc">Table of contents</h4>';
            $content .= '<ul class="toc-menu">';
            foreach ($menu as $item_id=>$menu_item) {
                $diff = $menu_item['level']-(is_null($current_level) ? $menu_item['level'] : $current_level);
                if ($diff > 0) {
                    $content .= str_repeat('<ul><li>', $diff);
                } elseif ($diff < 0) {
                    $content .= str_repeat('</li></ul></li>', -$diff);
                    $content .= '<li>';
                } else {
                    if (!is_null($current_level)) $content .= '</li>';
                    $content .= '<li>';
                }
                $depth += $diff;
                $content .= '<a href="#'.$item_id.'">'.$menu_item['text'].'</a>';
                $current_level = $menu_item['level'];
            }
            if ($depth!=0) {
                $content .= str_repeat('</ul></li>', $depth);
            }
            $content .= '</ul>';
        }
        return $content;
    }

}

// Endfile
