<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended;

use MarkdownExtended\API\ContentInterface;
use MarkdownExtended\API\Kernel;

/**
 * The default MarkdownExtended Content object
 */
class Content implements ContentInterface
{
    /**
     * @var string
     */
    protected $source;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var array
     */
    protected $parsing_options;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $charset      = 'utf-8';

    /**
     * @var array
     */
    protected $notes        = [];

    /**
     * @var array
     */
    protected $metadata     = [];

    /**
     * Construct a new content object with a source and current parsing options
     *
     * @param null|string $source
     * @param null|array $options
     */
    public function __construct($source = null, $options = null)
    {
        if (!is_null($source)) {
            $this->setSource($source);
        }
        if (!is_null($options)) {
            $this->setParsingOptions($options);
        }
    }

    /**
     * Use the content as a string
     *
     * This basically returns `$this->getContent()`.
     *
     * @see self::getContent()
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getContent();
    }

    /**
     * Get the content as an array
     *
     * This returns an array with all content's blocks
     * but `parsing_options` and `source`.
     *
     * @return array
     */
    public function __toArray()
    {
        return [
            'content'   => $this->getContent(),
            'charset'   => $this->getCharset(),
            'title'     => $this->getTitle(),
            'body'      => $this->getBody(),
            'notes'     => $this->getNotes(),
            'metadata'  => $this->getMetadata(),
        ];
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setParsingOptions(array $options)
    {
        $this->parsing_options = $options;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getParsingOptions()
    {
        return $this->parsing_options;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setCharset($charset)
    {
        $this->charset = trim($charset);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setTitle($title)
    {
        $title = is_array($title) ? $title[0] : $title;
        $this->title = trim($title);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setBody($str)
    {
        $this->body = trim($str);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setNotes(array $notes)
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function addNote(array $note, $note_id)
    {
        $this->notes[$note_id] = $note;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * {@inheritDoc}
     */
    public function getNotesFormatted()
    {
        return Kernel::get('OutputFormatBag')
            ->getNotesToString($this->notes, $this);
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setMetadata(array $data)
    {
        $this->metadata = $data;
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function addMetadata($var, $val)
    {
        $this->metadata[$var] = $val;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata($name = null)
    {
        if (!is_null($name)) {
            return isset($this->metadata[$name]) ? $this->metadata[$name] : null;
        }
        return $this->metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFormatted()
    {
        return Kernel::get('OutputFormatBag')
            ->getMetadataToString($this->metadata, $this);
    }
}
