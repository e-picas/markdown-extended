<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Exception\FileSystemException;
use MarkdownExtended\Util\Registry;
use MarkdownExtended\Util\Helper;

/**
 * Optionable object
 */
class Optionable
{
    /**
     * Resets options to defaults
     *
     * @return $this
     *
     * @see \MarkdownExtended\MarkdownExtended::getDefaults()
     */
    public function resetOptions()
    {
        Kernel::getInstance()->set('config', new Registry(MarkdownExtended::getDefaults()));
        return $this;
    }

    /**
     * Defines custom options or configuration file
     *
     * @param string|array $options A set of options or a configuration file path to override defaults
     *
     * @return $this
     *
     * @throws \MarkdownExtended\Exception\FileSystemException if a configuration file can not be found
     */
    public function setOptions($options)
    {
        if (is_string($options)) {
            return $this->setOptions(['config_file' => $options]);
        }

        if (isset($options['config_file']) && !empty($options['config_file'])) {
            $path = $options['config_file'];
            unset($options['config_file']);

            if (!file_exists($path)) {
                $local_path = Kernel::getResourcePath($path, Kernel::RESOURCE_CONFIG);
                if (empty($local_path) || !file_exists($local_path)) {
                    throw new FileSystemException(
                        sprintf('Configuration file "%s" not found', $path)
                    );
                }
                $path = $local_path;
            }

            $path_options = Helper::loadConfigFile($path);
            unset($path_options['config_file']);
            $this->setOptions($path_options);
            $options['loaded_config_file'] = $path;
        }

        if (is_array($options) && !empty($options)) {
            foreach ($options as $var => $val) {
                Kernel::getInstance()->setConfig($var, $val);
            }
        }

        return $this;
    }

}
