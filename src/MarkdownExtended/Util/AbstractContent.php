<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\API\Kernel;

/**
 * Use this object as a base to implement `\MarkdownExtended\API\ContentInterface`
 *
 * It treats a `$data` stack (and associated methods) with
 * the help of a `\MarkdownExtended\Util\IndexesAggregator`.
 */
abstract class AbstractContent
    implements ContentInterface
{
    /**
     * @var \MarkdownExtended\Util\IndexesAggregator
     */
    protected $indexes;

    /**
     * Construct a new basic content object
     *
     * @param null|\IteratorAggregate $indexes_aggregator
     */
    public function __construct(\IteratorAggregate $indexes_aggregator = null)
    {
        $this->setIndexAggregator(
            is_null($indexes_aggregator) ? new IndexesAggregator() : $indexes_aggregator
        );
    }

    /**
     * Defines object's data aggregator
     *
     * @param \MarkdownExtended\Util\IndexesAggregator $indexes
     *
     * @return $this
     */
    public function setIndexAggregator(IndexesAggregator $indexes)
    {
        $this->indexes = $indexes;
        $this->indexes
            ->addIndexRegistry('data')
            ->addIndexRegistry('metadata')
            ->addIndexRegistry('menu')
            ->addIndexRegistry('notes')
        ;
        return $this;
    }

    /**
     * Gets object's data aggregator
     *
     * @return \MarkdownExtended\Util\IndexesAggregator
     */
    public function getIndexAggregator()
    {
        return $this->indexes;
    }

    /**
     * Adds a value in a stack of the data aggregator
     *
     * @param string $type
     * @param mixed $value
     * @param null|mixed $index
     *
     * @return $this
     */
    public function addIndex($type, $value, $index = null)
    {
        $this->indexes->addIndex($type, $value, $index);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function setData($type, array $data)
    {
        $stack = $this->indexes->getIndexRegistry('data');
        $stack[$type] = $data;
        $this->indexes->setIndexRegistry('data', $stack);
        return $this;
    }

    /**
     * {@inheritDoc}
     * @return $this
     */
    public function addData($type, $value, $index = null)
    {
        $data = $this->getData($type);
        if (is_null($index)) {
            $data[] = $value;
        } else {
            $data[$index] = $value;
        }
        return $this->setData($type, $data);
    }

    /**
     * {@inheritDoc}
     */
    public function getData($type, $name = null)
    {
        $data = $this->indexes->getIndexRegistry('data');
        if (!empty($name)) {
            return (isset($data[$type]) && isset($data[$type][$name]) ? $data[$type][$name] : null);
        }
        return (isset($data[$type]) ? $data[$type] : array());
    }

    /**
     * {@inheritDoc}
     */
    public function getDataFormatted($type, array $options = null)
    {
        $data = $this->getData($type);
        if (empty($data)) {
            return '';
        }
        return Kernel::get('OutputFormatBag')
            ->getDataToString($type, $data, $this);
    }
}
