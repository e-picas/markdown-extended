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
use \MarkdownExtended\OutputFormat\DefaultHelper;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Manpages Markdown Extended output Helper
 * @package MarkdownExtended\OutputFormat
 */
class ManHelper
    extends DefaultHelper
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
        $headers_infos = array();

        // metadata
        if ($md_content->getMetadata()) {
            $special_metadata = MarkdownExtended::getConfig('special_metadata');
            foreach ($md_content->getMetadata() as $meta_name=>$meta_content) {
                if (!in_array($meta_name, $special_metadata)) {
                    if ($meta_name=='title') {
                        $headers_infos['name'] = $meta_content;
                    } elseif (in_array($meta_name, $formatter::$headers_meta_data)) {
                        $headers_infos[$meta_name] = $meta_content;
                    } else {
                        $content .= $formatter->buildTag('meta_data', $meta_content, array('name'=>$meta_name));
                    }
                }
            }
        }

        // page title
        if (!empty($headers_infos)) {
            if (empty($headers_infos['name']) && $md_content->getTitle()) {
                $headers_infos['name'] = $md_content->getTitle();
            }
            $content .= $formatter->buildTag('meta_title', null, $headers_infos);
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
     */
    public function getToc(MDE_API\ContentInterface $md_content, MDE_API\OutputFormatInterface $formatter)
    {
        return '';
    }

}

// Endfile
