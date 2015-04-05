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
 * Interface to implement for `MarkdownExtended\Content` objects
 *
 * @package MarkdownExtended\API
 * @api
 */
interface ContentInterface
{

    public function __toString();

    public function __toArray();

    public function setSource($source);

    public function getSource();

    public function setParsingOptions(array $options);

    public function getParsingOptions();

    public function setContent($content);

    public function getContent();

    public function setCharset($charset);

    public function getCharset();

    public function setTitle($title);

    public function getTitle();

    public function setBody($str);

    public function getBody();

    public function setNotes(array $notes);

    public function addNote($note, $note_id);

    public function getNotes();

    public function getNotesFormatted();

    public function setMetadata(array $data);

    public function addMetadata($var, $val);

    public function getMetadata($name = null);

    public function getMetadataFormatted();

}

// Endfile
