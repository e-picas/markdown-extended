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

use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Util\AbstractContent;
use \MarkdownExtended\Util\IndexesAggregator;
use \MarkdownExtended\Util\Menu\Menu;

/**
 * The default MarkdownExtended Content object
 */
class Content
    extends AbstractContent
    implements ContentInterface
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
     * Construct a new content object with a source and current parsing options
     *
     * @param null|string $source
     * @param null|array $options
     * @param null|\IteratorAggregate $indexes_aggregator
     */
    public function __construct($source = null, $options = null, \IteratorAggregate $indexes_aggregator = null)
    {
        parent::__construct(
            is_null($indexes_aggregator) ? new IndexesAggregator() : $indexes_aggregator
        );
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
        return array(
            'content'   => $this->getContent(),
            'charset'   => $this->getCharset(),
            'title'     => $this->getTitle(),
            'body'      => $this->getBody(),
            'notes'     => $this->getNotes(),
            'menu'      => $this->getMenu(),
            'metadata'  => $this->getMetadata()
        );
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
        foreach ($notes as $note_id=>$note) {
            $this->addNote($note, $note_id);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function addNote(array $note, $note_id)
    {
        $this->addIndex('notes', $note, $note_id);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getNotes()
    {
        return $this->indexes->getIndexRegistry('notes');
    }

    /**
     * {@inheritDoc}
     */
    public function getNotesFormatted(array $options = null)
    {
        return Kernel::get('OutputFormatBag')
            ->getNotesToString($this->getNotes(), $this);
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setMetadata(array $data)
    {
        foreach ($data as $var=>$val) {
            $this->addMetadata($val, $var);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function addMetadata($var, $val)
    {
        $this->addIndex('metadata', $val, $var);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadata($name = null)
    {
        $metadata = $this->indexes->getIndexRegistry('metadata');
        if (!is_null($name)) {
            return isset($metadata[$name]) ? $metadata[$name] : null;
        }
        return $metadata;
    }

    /**
     * {@inheritDoc}
     */
    public function getMetadataFormatted(array $options = null)
    {
        return Kernel::get('OutputFormatBag')
            ->getMetadataToString($this->getMetadata(), $this);
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function setMenu(array $items)
    {
        foreach ($items as $item) {
            $this->addMenuItem($item);
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public function addMenuItem(array $data)
    {
        $this->addIndex('menu', $data);
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getMenu()
    {
        $menu = Menu::create(
            $this->indexes->getIndexRegistry('menu')
        );
        return $menu->getItems();
    }

    /**
     * {@inheritDoc}
     */
    public function getMenuFormatted(array $options = null)
    {
        return Kernel::get('OutputFormatBag')
            ->getMenuToString($this->getMenu(), $this);
    }
}
