<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

/**
 * PHP Markdown Extended OutputFormat helper interface
 *
 * @package MarkdownExtended\API
 * @api
 */
interface OutputFormatHelperInterface
{

    /**
     * This must return a full formatted string ready to write a well-formed document in the considered format
     *
     * @param   \MarkdownExtended\API\ContentInterface          $content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @return  string
     */
    public function getFullContent(ContentInterface $content, OutputFormatInterface $formatter);

    /**
     * This must return a full formatted table of contents in the considered format
     *
     * @param   \MarkdownExtended\API\ContentInterface          $content
     * @param   \MarkdownExtended\API\OutputFormatInterface     $formatter
     * @return  string
     */
    public function getToc(ContentInterface $content, OutputFormatInterface $formatter);

}

// Endfile
