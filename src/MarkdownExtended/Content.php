<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended;

use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\API\Kernel;

class Content
    implements ContentInterface
{

    protected $source;
    protected $content;
    protected $parsing_options;
    protected $body;
    protected $title;
    protected $charset      = 'utf-8';
    protected $notes        = array();
    protected $metadata     = array();

    public function __construct($source = null, $options = null)
    {
        if (!is_null($source)) {
            $this->setSource($source);
        }
        if (!is_null($options)) {
            $this->setParsingOptions($options);
        }
    }

    public function __toString()
    {
        return $this->getContent();
    }

    public function __toArray()
    {
        return array(
            'content'   => $this->getContent(),
            'charset'   => $this->getCharset(),
            'title'     => $this->getTitle(),
            'body'      => $this->getBody(),
            'notes'     => $this->getNotes(),
            'metadata'  => $this->getMetadata()
        );
    }

    public function setSource($source)
    {
        $this->source = $source;
        return $this;
    }

    public function getSource()
    {
        return $this->source;
    }

    public function setParsingOptions(array $options)
    {
        $this->parsing_options = $options;
        return $this;
    }

    public function getParsingOptions()
    {
        return $this->parsing_options;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setCharset($charset)
    {
        $this->charset = trim($charset);
        return $this;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setTitle($title)
    {
        $this->title = trim($title);
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setBody($str)
    {
        $this->body = trim($str);
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setNotes(array $notes)
    {
        $this->notes = $notes;
        return $this;
    }

    public function addNote($note, $note_id)
    {
        $this->notes[$note_id] = $note;
        return $this;
    }

    public function getNotes()
    {
        return $this->notes;
    }

    public function getNotesFormatted()
    {
        return Kernel::get('OutputFormatBag')
            ->getNotesToString($this->notes, $this);
    }

    public function setMetadata(array $data)
    {
        $this->metadata = $data;
        return $this;
    }

    public function addMetadata($var, $val)
    {
        $this->metadata[$var] = $val;
        return $this;
    }

    public function getMetadata($name = null)
    {
        if (!is_null($name)) {
            return isset($this->metadata[$name]) ? $this->metadata[$name] : null;
        }
        return $this->metadata;
    }

    public function getMetadataFormatted()
    {
        return Kernel::get('OutputFormatBag')
            ->getMetadataToString($this->metadata, $this);
    }

}
