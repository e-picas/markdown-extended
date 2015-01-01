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
