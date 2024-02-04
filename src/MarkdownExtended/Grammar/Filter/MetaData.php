<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\API\Kernel;

/**
 * Process Markdown meta data
 */
class MetaData extends Filter
{
    /**
     * The table of metadata
     *
     * @var     array
     */
    protected $metadata;

    /**
     * The table of special metadata
     *
     * @var     array
     */
    protected $special_metadata = [];

    /**
     * Flag to test if we are currently in a metadata match
     *
     * @var     int
     */
    protected static $inMetaData = -1;

    /**
     * Prepare object with configuration
     */
    public function _setup()
    {
        Kernel::setConfig('metadata', []);
        $this->metadata = [];
        $this->special_metadata = Kernel::getConfig('special_metadata');
        if (empty($this->special_metadata)) {
            $this->special_metadata = [];
        }
        self::$inMetaData = -1;
    }

    /**
     * @param   string  $text
     * @return  string
     */
    public function strip($text)
    {
        $lines = preg_split('/\n/', $text);
        $first_line = $lines[0];
        if (preg_match('/^([a-zA-Z0-9][0-9a-zA-Z _-]*?):\s*(.*)$/', $first_line)) {
            $text = '';
            self::$inMetaData = 1;
            foreach ($lines as $line) {
                if (self::$inMetaData === 0) {
                    $text .= $line."\n";
                } else {
                    $text .= self::transform($line);
                    if (preg_match('/^$/', $line)) {
                        self::$inMetaData = 0;
                    }
                }
            }
        }
        if (!empty($this->metadata)) {
            Kernel::setConfig('metadata', $this->metadata);
            foreach ($this->metadata as $var => $val) {
                Kernel::get(Kernel::TYPE_CONTENT)->addMetadata($var, $val);
            }
        }
        return $text;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($line)
    {
        $line = preg_replace_callback(
            '{^([a-zA-Z0-9][0-9a-zA-Z _-]*?):\s*(.*)$}i',
            [$this, '_callback'],
            $line
        );
        if (strlen($line)) {
            $line = preg_replace_callback(
                '/^\s*(.+)$/',
                [$this, '_callback_nextline'],
                $line
            );
        }
        if (strlen($line)) {
            $line .= "\n";
        }
        return $line;
    }

    /**
     * Callback applied to matches
     *
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $meta_key = strtolower(str_replace(' ', '', $matches[1]));
        $this->metadata[$meta_key] = trim($matches[2]);
        if (in_array($meta_key, $this->special_metadata)) {
            Kernel::setConfig($meta_key, $this->metadata[$meta_key]);
        }
        return '';
    }

    /**
     * Callback applied for next line matches
     *
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _callback_nextline($matches)
    {
        $meta_key = array_search(end($this->metadata), $this->metadata);
        $this->metadata[$meta_key] .= ' '.trim($matches[1]);
        return '';
    }

    /**
     * Build meta data strings
     *
     * @param string $text
     *
     * @return string
     */
    public function append($text)
    {
        $metadata = Kernel::getConfig('metadata');
        if (!empty($metadata)) {
            foreach ($metadata as $meta_name => $meta_value) {
                if (!empty($meta_name) && is_string($meta_name)) {
                    if (in_array($meta_name, $this->special_metadata)) {
                        Kernel::setConfig($meta_name, $meta_value);
                    } elseif ($meta_name == 'title') {
                        Kernel::get(Kernel::TYPE_CONTENT)
                            ->setTitle($meta_value);
                    }
                }
            }
        }
        return $text;
    }
}
