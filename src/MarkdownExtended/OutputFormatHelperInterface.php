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
namespace MarkdownExtended;

use MarkdownExtended\Content,
    MarkdownExtended\OutputFormatInterface;

/**
 * PHP Markdown Extended OutputFormat helper interface
 */
interface OutputFormatHelperInterface
{

    /**
     * This must return a full formated string ready to write a well-formed document in the considered format
     *
     * @param object $content \MarkdownExtended\Content
     * @param object $formater \MarkdownExtended\OutputFormatInterface
     *
     * @return string
     */
    public function getFullContent(Content $content, OutputFormatInterface $formater);

    /**
     * This must return a full formated table of contents in the considered format
     *
     * @param object $content \MarkdownExtended\Content
     * @param object $formater \MarkdownExtended\OutputFormatInterface
     *
     * @return string
     */
    public function getToc(Content $content, OutputFormatInterface $formater);

}

// Endfile
