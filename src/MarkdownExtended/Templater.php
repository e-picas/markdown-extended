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

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\API\TemplateInterface;
use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\Exception\RuntimeException;
use \MarkdownExtended\Exception\UnexpectedValueException;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Util\Registry;
use \MarkdownExtended\Util\CacheRegistry;

class Templater
    implements TemplateInterface
{

    protected $config;
    protected $cache;

    public function __construct()
    {
        $this->config   = new Registry(Kernel::getConfig('template_options'));
        $this->cache    = new CacheRegistry;
    }

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

    public function getTemplate($template_path)
    {
        if (true === $template_path) {
            $template_path = Kernel::getConfig(
                'output_format_options.' . Kernel::getConfig('output_format') . '.default_template'
            );
        }

        if (!file_exists($template_path)) {
            $local_path = Kernel::getResourcePath($template_path, Kernel::RESOURCE_TEMPLATE);
            if (empty($local_path) || !file_exists($local_path)) {
                throw new UnexpectedValueException(
                    sprintf('Template "%s" not found', $template_path)
                );
            }
            $template_path = $local_path;
        }

        if (!$this->cache->isCached($template_path)) {
            if (!is_readable($template_path)) {
                throw new RuntimeException(
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

    public function getParams(ContentInterface $content)
    {
        $params = array();

        foreach ($this->config->get('keywords') as $var=>$word) {
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

        return $params;
    }

    protected function _buildKeywordMask($keyword)
    {
        return sprintf($this->config->get('keywords_mask'), $keyword);
    }

}

// Endfile
