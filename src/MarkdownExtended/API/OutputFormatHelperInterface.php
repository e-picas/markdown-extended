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
namespace MarkdownExtended\API;

use \MarkdownExtended\API as MDE_API;

/**
 * PHP Markdown Extended OutputFormat helper interface
 *
 * @package MarkdownExtended\API
 */
interface OutputFormatHelperInterface
{

    /**
     * This must return a full formated string ready to write a well-formed document in the considered format
     *
     * @param   \MarkdownExtended\API\ContentInterface          $content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @return  string
     */
    public function getFullContent(MDE_API\ContentInterface $content, MDE_API\OutputFormatInterface $formatter);

    /**
     * This must return a full formated table of contents in the considered format
     *
     * @param   \MarkdownExtended\API\ContentInterface          $content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @return  string
     */
    public function getToc(MDE_API\ContentInterface $content, MDE_API\OutputFormatInterface $formatter);

}

// Endfile
