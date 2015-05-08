<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\API\TemplateInterface;
use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\Exception\FileSystemException;
use \MarkdownExtended\Exception\UnexpectedValueException;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Util\Registry;
use \MarkdownExtended\Util\CacheRegistry;

/**
 * The default template object of MarkdownExtended
 */
class Templater
    implements TemplateInterface
{

    /**
     * @var \MarkdownExtended\Util\Registry
     */
    protected $config;

    /**
     * @var \MarkdownExtended\Util\CacheRegistry
     */
    protected $cache;

    /**
     * Initializes object's registries
     */
    public function __construct()
    {
        $this->config   = new Registry(Kernel::getConfig('template_options'));
        $this->cache    = new CacheRegistry;
    }

    /**
     * {@inheritdoc}
     * @return mixed|string
     */
    public function parse(ContentInterface $content, $template_path = null)
    {
        $tpl_content    = $this->getTemplate($template_path);
        $params         = $this->getParams($content);

        foreach ($params as $name=>$callback) {
            $tpl_content = preg_replace_callback(
                Helper::buildRegex($name), $callback, $tpl_content
            );
        }

        return $tpl_content;
    }

    /**
     * Gets a template file content
     *
     * @param string $template_path
     *
     * @return mixed|string
     *
     * @throws \MarkdownExtended\Exception\FileSystemException if the template can not be found or is not readable
     */
    public function getTemplate($template_path)
    {
        if (true === $template_path) {
            $template_path = Kernel::getConfig(
                'output_format_options.' . Kernel::getConfig('output_format') . '.default_template'
            );
            if (empty($template_path)) {
                return Kernel::getConfig('template_options.inline_template');
            }
        }

        if (!file_exists($template_path)) {
            $local_path = Kernel::getResourcePath($template_path, Kernel::RESOURCE_TEMPLATE);
            if (empty($local_path) || !file_exists($local_path)) {
                throw new FileSystemException(
                    sprintf('Template "%s" not found', $template_path)
                );
            }
            $template_path = $local_path;
        }

        if (!$this->cache->isCached($template_path)) {
            if (!is_readable($template_path)) {
                throw new FileSystemException(
                    sprintf('Template "%s" is not readable', $template_path)
                );
            }
            $tpl_content = Helper::readFile($template_path);
            $this->cache->setCache($template_path, $tpl_content);
        } else {
            $tpl_content = $this->cache->getCache($template_path);
        }

        return $tpl_content;
    }

    /**
     * Gets the array of parameters to pass in the template based on a content object
     *
     * @param \MarkdownExtended\API\ContentInterface $content
     *
     * @return array
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if a keyword can not be found in the content object
     */
    public function getParams(ContentInterface $content)
    {
        $params     = array();
        $keywords   = $this->config->get('keywords');

        // all options keywords
        foreach ($keywords as $var=>$word) {
            $mask   = $this->_buildKeywordMask($word);
            $method = 'get' . Helper::toCamelCase($var);
            if (!method_exists($content, $method)) {
                throw new UnexpectedValueException(
                    sprintf('Template keyword "%s" not found in content', $var)
                );
            }
            $method_tostring = $method . 'Formatted';
            $params[$mask] = function () use ($content, $method, $method_tostring) {
                return call_user_func(
                    array($content, method_exists($content, $method_tostring) ? $method_tostring : $method)
                );
            };
        }

        // all metadata: META:name
        $meta = $content->getMetadata();
        if (!empty($meta) && isset($keywords['metadata'])) {
            foreach ($meta as $name=>$value) {
                $mask = $this->_buildKeywordMask($keywords['metadata'].':'.$name);
                $params[$mask] = function () use ($value) {
                    return Helper::getSafeString($value);
                };
            }
        }

        return $params;
    }

    /**
     * Gets a keyword tag from configuration
     *
     * @param string $keyword
     *
     * @return string
     */
    protected function _buildKeywordMask($keyword)
    {
        return sprintf($this->config->get('keywords_mask'), $keyword);
    }
}
