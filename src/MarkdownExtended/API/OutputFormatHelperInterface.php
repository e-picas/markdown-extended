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
namespace MarkdownExtended\API;

/**
 * PHP Markdown Extended OutputFormat helper interface
 */
interface OutputFormatHelperInterface
{

    /**
     * This must return a full formated string ready to write a well-formed document in the considered format
     *
     * @param   \MarkdownExtended\API\ContentInterface          $content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formater
     * @return  string
     */
    public function getFullContent(\MarkdownExtended\API\ContentInterface $content, \MarkdownExtended\API\OutputFormatInterface $formater);

    /**
     * This must return a full formated table of contents in the considered format
     *
     * @param   \MarkdownExtended\API\ContentInterface          $content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formater
     * @return  string
     */
    public function getToc(\MarkdownExtended\API\ContentInterface $content, \MarkdownExtended\API\OutputFormatInterface $formater);

}

// Endfile
