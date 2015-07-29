<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Exception\UnexpectedValueException;

/**
 * A simple Content objects collection
 */
class ContentCollection
    extends \ArrayIterator
{
    /**
     * Constructs a new collection being sure that data are all `\MarkdownExtended\API\ContentInterface`
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct();
        foreach ($data as $item) {
            $this->append($item);
        }
    }

    /**
     * Appends a new collection entry
     *
     * @param \MarkdownExtended\API\ContentInterface $content
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException it the argument does not implement `\MarkdownExtended\API\ContentInterface`
     */
    public function append($content)
    {
        if (!is_object($content) || !Kernel::valid($content, Kernel::TYPE_CONTENT)) {
            throw new UnexpectedValueException(
                sprintf('Method "%s" expects a "%s" parameter object, got "%s"',
                    __METHOD__, Kernel::CONTENT_INTERFACE,
                    is_object($content) ? get_class($content) : gettype($content)
                )
            );
        }
        parent::append($content);
    }

    /**
     * Sets a collection entry
     *
     * @param string $index
     * @param string $content
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException it the argument does not implement `\MarkdownExtended\API\ContentInterface`
     */
    public function offsetSet($index, $content)
    {
        if (!is_object($content) || !Kernel::valid($content, Kernel::TYPE_CONTENT)) {
            throw new UnexpectedValueException(
                sprintf('Method "%s" expects the second parameter to implement "%s", got "%s"',
                    __METHOD__, Kernel::CONTENT_INTERFACE,
                    is_object($content) ? get_class($content) : gettype($content)
                )
            );
        }
        parent::offsetSet($index, $content);
    }
}
