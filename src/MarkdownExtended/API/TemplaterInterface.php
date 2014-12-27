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

/**
 * Interface TemplaterInterface
 *
 * @package MarkdownExtended\API
 */
interface TemplaterInterface
{

    /**
     * Get the template file path
     *
     * @return mixed
     */
    public function getTemplate();

    /**
     * Get the template content with loaded Markdown parsed content parts inserted
     *
     * @return mixed
     */
    public function __toString();

}

// Endfile
