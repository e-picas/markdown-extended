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
 * HTML output Helper
 */
class HTMLHelper implements OutputFormatHelperInterface
{

    /**
     * Get a complete version of parsed content, including metadata, body and notes
     *
     * @param object $content \MarkdownExtended\Content
     * @param object $formater \MarkdownExtended\OutputFormatInterface
     * @param bool $full_html Defines if the meatdata must be returned in a `<head>` block
     * @param bool $include_toc Include a table-of-content (default is `false`)
     * @param string $html_tag Defines the HTML header tag
     *
     * @return string
     */
    public function getFullContent(Content $md_content, OutputFormatInterface $formater, $full_html = true, $include_toc = false, $html_tag = '<!DOCTYPE html>')
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
                        $content .= $formater->buildTag('meta_title', $meta_content);
                        $title_done = true;
                    } else {
                        $content .= $formater->buildTag('meta_data', null, array(
                            'name'=>$meta_name,
                            'content'=>$meta_content
                        )) . "\n";
                    }
                }
            }
        }

        // force title if so
        if (!$title_done && $md_content->getTitle()) {
            $content .= $formater->buildTag('meta_title', $md_content->getTitle()) . "\n";
        }

        if ($full_html) $content .= "</head><body>\n";

        // toc
        if ($md_content->getMenu()) {
            $content .= self::getToc($md_content, $formater);
        }

        // body
        if ($md_content->getBody()) {
            $content .= $md_content->getBody();
        }

        // notes
        if ($md_content->getNotes()) {
            $notes_content = '';
            foreach ($md_content->getNotes() as $id=>$note_content) {
                $notes_content .= $formater->buildTag('ordered_list_item', $note_content['text'], array(
                    'id' => !empty($note_content['note-id']) ? $note_content['note-id'] : $id
                )) . "\n";
            }
            $notes_content = $formater->buildTag('ordered_list', $notes_content) . "\n";
            $content .= $formater->buildTag('block', $notes_content, array('class'=>'footnotes')) . "\n";
        }

        if ($full_html) $content .= "</body>\n</html>\n";

        return $content;
    }

    /**
     * Build a hierarchical menu
     *
     * @param object $content \MarkdownExtended\Content
     * @param object $formater \MarkdownExtended\OutputFormatInterface
     *
     * @return string
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
