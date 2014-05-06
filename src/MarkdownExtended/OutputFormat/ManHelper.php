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
namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\API\OutputFormatInterface;
use \MarkdownExtended\API\OutputFormatHelperInterface;
use \MarkdownExtended\OutputFormat\DefaultHelper;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Manpages Markdown Extended output Helper
 */
class ManHelper
    extends DefaultHelper
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
        $headers_infos = array();

        // metadata
        if ($md_content->getMetadata()) {
            $special_metadata = MarkdownExtended::getConfig('special_metadata');
            foreach ($md_content->getMetadata() as $meta_name=>$meta_content) {
                if (!in_array($meta_name, $special_metadata)) {
                    if ($meta_name=='title') {
                        $headers_infos['name'] = $meta_content;
                    } elseif (in_array($meta_name, $formater::$headers_meta_data)) {
                        $headers_infos[$meta_name] = $meta_content;
                    } else {
                        $content .= $formater->buildTag('meta_data', $meta_content, array('name'=>$meta_name));
                    }
                }
            }
        }

        // page title
        if (!empty($headers_infos)) {
            if (empty($headers_infos['name']) && $md_content->getTitle()) {
                $headers_infos['name'] = $md_content->getTitle();
            }
            $content .= $formater->buildTag('meta_title', null, $headers_infos);
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
     */
    public function getToc(ContentInterface $md_content, OutputFormatInterface $formater)
    {
        return '';
    }

}

// Endfile
