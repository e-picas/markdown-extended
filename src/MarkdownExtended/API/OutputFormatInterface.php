<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

/**
 * Interface to implement for any output format object
 */
interface OutputFormatInterface
{
    /**
     * This may construct a valid string for concerned tag, content and attributes
     *
     * @param   string  $tag_name       The tag name to construct
     * @param   string  $content        Concerned content
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     *
     * @return  string
     */
    public function buildTag($tag_name, $content = null, array $attributes = []);

    /**
     * @param   string  $content        Concerned content
     * @param   string  $tag_name       The tag name to construct
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     *
     * @return  string
     */
    public function getTagString($content, $tag_name, array $attributes = []);

    /**
     * Gets the notes list as string
     *
     * @param   array $notes
     * @param   \MarkdownExtended\API\ContentInterface $content
     */
    public function getNotesToString(array $notes, ContentInterface $content);

    /**
     * Gets the metadata list as string
     *
     * @param   array $metadata
     * @param   \MarkdownExtended\API\ContentInterface $content
     */
    public function getMetadataToString(array $metadata, ContentInterface $content);
}
