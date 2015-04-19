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
 * Interface to implement for content objects
 *
 * @api
 */
interface ContentInterface
{

    /**
     * Use the content as a string
     *
     * This may basically return `$this->getContent()`.
     * You can customize this method to adapt the usage of
     * a content object as a string.
     *
     * @return string
     */
    public function __toString();

    /**
     * Get the content as an array
     *
     * This may return an array with all content's blocks
     * interesting for user usage (basically all but `parsing_options`
     * and `source`).
     *
     * @return array
     */
    public function __toArray();

    /**
     * Sets the original source of the content
     *
     * @param string $source
     */
    public function setSource($source);

    /**
     * Gets the original source of the content
     *
     * @return string
     */
    public function getSource();

    /**
     * Sets the array of options used to parse the content
     *
     * @param array $options
     */
    public function setParsingOptions(array $options);

    /**
     * Gets the array of options used to parse the content
     *
     * @return array
     */
    public function getParsingOptions();

    /**
     * Sets the content's "final" content
     *
     * This may be a concatenation of some of content's blocks
     * depending on parsing options (i.e. with or without template).
     * The "$content" may be the string returned when using the
     * object like a string.
     *
     * @param string $content
     */
    public function setContent($content);

    /**
     * Gets the content's "final" content
     *
     * @return string
     */
    public function getContent();

    /**
     * Sets the content's character set
     *
     * @param string $charset
     */
    public function setCharset($charset);

    /**
     * Gets the content's character set
     *
     * @return string
     */
    public function getCharset();

    /**
     * Sets the content's title
     *
     * If no title is found, a good practice when parsing a
     * file content is to load the filename as title.
     *
     * @param string $title
     */
    public function setTitle($title);

    /**
     * Gets the content's title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Sets the content's body
     *
     * The "body" is the raw transformed content, without
     * addition of footnotes and metadata.
     *
     * @param string $str
     */
    public function setBody($str);

    /**
     * Gets the content's body
     *
     * @return string
     */
    public function getBody();

    /**
     * Sets the content's footnotes
     *
     * @see self::addNote()
     *
     * @param array $notes
     */
    public function setNotes(array $notes);

    /**
     * Adds a content's footnote
     *
     * A footnote definition is returned by the filter
     * as an array like:
     *
     *      array(
     *          count:          the note index in global notes' list
     *          type:           footnote / glossary / bibliography
     *          in-text-id:     ID of the note marker in the body
     *          note-id:        ID of the footnote definition in the body
     *          text:           content of the footnote definition
     *      )
     *
     * @param array $note
     * @param string $note_id
     */
    public function addNote(array $note, $note_id);

    /**
     * Gets the content's notes
     *
     * @return array
     */
    public function getNotes();

    /**
     * Gets the content's notes formatted in current output format
     *
     * @return string
     */
    public function getNotesFormatted();

    /**
     * Sets content's metadata
     *
     * @param array $data
     */
    public function setMetadata(array $data);

    /**
     * Adds a new content's metadata
     *
     * A metadata item is a simple array entry like:
     *
     *      meta_name => meta_value
     *
     * @param string $var
     * @param string $val
     */
    public function addMetadata($var, $val);

    /**
     * Gets one or all content's metadata
     *
     * @param null $name
     * @return null|string|array
     */
    public function getMetadata($name = null);

    /**
     * Gets content's metadata formatted in current output format
     *
     * @return string
     */
    public function getMetadataFormatted();

    /**
     * Gets content's table of contents
     *
     * @return null|array
     */
    public function getTableOfContents();

    /**
     * Gets content's table of contents formatted in current output format
     *
     * @return string
     */
    public function getTableOfContentsFormatted();
}
