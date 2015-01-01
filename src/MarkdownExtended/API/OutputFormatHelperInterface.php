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
