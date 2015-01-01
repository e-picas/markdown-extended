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

use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Class Templater
 * @package MarkdownExtended
 */
class Templater
    implements MDE_API\TemplaterInterface
{

    /**
     * @var     array
     */
    protected $config;

    /**
     * @var     string
     */
    protected $content;

    /**
     * @var     \MarkdownExtended\API\ContentInterface
     */
    protected $mde_content;

    /**
     * @var     \MarkdownExtended\API\CollectionInterface
     */
    protected $mde_content_collection;

    /**
     * @var     array
     */
    protected $done = array();

    /**
     * Construction of a new template object
     *
     * @param   array   $user_config
     */
    public function __construct(array $user_config = array())
    {
        $defaults = MarkdownExtended::getConfig('templater');
        if (empty($defaults) || !is_array($defaults)) {
            $defaults = array();
        }
        $this->config = array_merge($defaults, $user_config);
    }

    /**
     * Echoing the template object will force parsing and write result
     *
     * @return  string
     */
    public function __toString()
    {
        if (!empty($this->content)) {
            return $this->getContent();
        }
        if (!empty($this->mde_content)) {
            return $this->parse()->getContent();
        }
        if (!empty($this->mde_content_collection)) {
            return $this->parseCollection()->getContent();
        }
        return '';
    }

    /**
     * Load a content object
     *
     * @param   \MarkdownExtended\API\ContentInterface
     * @return  self
     */
    public function load(MDE_API\ContentInterface $mde_content)
    {
        $this->mde_content = $mde_content;
        return $this;
    }

    /**
     * Load a contents collection object
     *
     * @param   \MarkdownExtended\API\CollectionInterface
     * @return  self
     */
    public function loadCollection(MDE_API\CollectionInterface $mde_content_collection)
    {
        $this->mde_content_collection = $mde_content_collection;
        return $this;
    }

    /**
     * Get the current content
     *
     * @return  string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get a template content
     *
     * @return  string
     * @throws  \MarkdownExtended\Exception\Exception if template file not found
     */
    public function getTemplate()
    {
        $tpl_filename = $this->_findTemplate();
        if (!empty($tpl_filename) && file_exists($tpl_filename)) {
            $tpl_content = file_get_contents($tpl_filename);
        } else {
            throw new MDE_Exception\Exception(
                sprintf('Template file "%s" not found!', $tpl_filename)
            );
        }
        return $tpl_content;
    }

    /**
     * Build a template content passing it arguments
     *
     * @param   array   $params
     * @return  self
     * @throws  \MarkdownExtended\Exception\Exception if template file not found
     */
    public function buildTemplate(array $params)
    {
        $tpl_filename = $this->_findTemplate();
        if (!empty($tpl_filename) && file_exists($tpl_filename)) {
            if (!empty($params)) {
                extract($params, EXTR_OVERWRITE);
            }
            ob_start();
            include $tpl_filename;
            $this->content = ob_get_contents();
            ob_end_clean();
        } else {
            throw new MDE_Exception\Exception(
                sprintf('Template file "%s" not found!', $tpl_filename)
            );
        }
        return $this;
    }

    /**
     * Parse a content to complete it and pass it in a template if necessary
     *
     * @return self
     */
    public function parse()
    {
        $this
            ->parseContent()
            ->parseTemplate()
            ->autoInsert();
        return $this;
    }

    /**
     * Parse a contents collection to complete it and pass it in a template if necessary
     *
     * @return self
     */
    public function parseCollection()
    {
        $this->content = null;
        $this->mde_content = null;
        foreach ($this->mde_content_collection as $_content) {
            $this->content = null;
            $this->load($_content);
            $this->parseContent();
            $_content->setBody($this->content);
        }
        $this->content = null;
        $this->mde_content = null;
        $this->buildTemplate(array(
            'contents'=>$this->mde_content_collection
        ));
        return $this;
    }

    /**
     * Parse a in-body tags
     *
     * @return self
     */
    public function parseContent()
    {
        $content = empty($this->content) ? $this->mde_content->getBody() : $this->content;
        if (!empty($content)) {
            $this->content = $this->_doParse($content, $this->config['keywords']);
        }
        return $this;
    }

    /**
     * Parse a template content
     *
     * @return self
     */
    public function parseTemplate()
    {
        $template = $this->getTemplate();
        $content = empty($this->content) ? $this->mde_content->getBody() : $this->content;
        if (!empty($content) && !empty($template)) {
            $this->content = $this->_doParse($template, $this->config['keywords']);
        }
        return $this;
    }

    /**
     * Insert tags from 'auto_insert' config entry
     *
     * @return self
     */
    public function autoInsert()
    {
        $content = empty($this->content) ? $this->mde_content->getBody() : $this->content;
        if (!empty($content)) {
            $auto_insert = $this->config['auto_insert'];
            $keywords = array();
            foreach ($auto_insert as $block_name) {
                if (array_key_exists($block_name, $this->config['keywords'])) {
                    $keywords[$block_name] = $this->config['keywords'][$block_name];
                }
            }
            $this->content = $this->_doParse($content, $keywords);
        }
        return $this;
    }

// -----------------------
// Internals
// -----------------------

    /**
     * @param   string  $keyword
     * @return  string
     */
    protected function _buildKeywordMask($keyword)
    {
        return sprintf($this->config['keywords_mask'], $keyword);
    }

    /**
     * @param   string  $content
     * @param   array   $keywords
     * @param   bool    $force_insert
     * @return  string
     */
    protected function _doParse($content, array $keywords, $force_insert = false)
    {
        foreach ($keywords as $block_name=>$keyword) {
            $to_replace = null;
            $simple_regex = MDE_Helper::buildRegex($this->_buildKeywordMask($keyword));
            $para_regex = MDE_Helper::buildRegex(
                MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('paragraph', $this->_buildKeywordMask($keyword))
                );
            // first the para version
            if (preg_match($para_regex, $content, $matches)) {
                $to_replace = $matches[0];
            } elseif (preg_match($simple_regex, $content, $matches)) {
                $to_replace = $matches[0];
            }
            if (!empty($to_replace)) {
                if (!in_array($block_name, $this->done)) {
                    if ($block_name==='body') {
                        $content_block = empty($this->content) ? $this->mde_content->getBody() : $this->content;
                    } else {
                        $block_name_method = 'get'.MDE_Helper::toCamelCase($block_name);
                        $block_name_method_toString = $block_name_method.'ToString';
                        try {
                            $content_block = call_user_func(array($this->mde_content, $block_name_method_toString));
                        } catch (\Exception $e) {}
                        if (is_null($content_block)) {
                            try {
                                $content_block = call_user_func(array($this->mde_content, $block_name_method));
                            } catch (\Exception $e) {}
                        }
                    }
                } else {
                    $content_block = ' ';
                }
                if (!is_null($content_block)) {
                    $content = str_replace($matches[0], $content_block, $content);
                    if (!in_array($block_name, $this->done)) {
                        $this->done[] = $block_name;
                    }
                }
            }
        }
        return $content;
    }

    /**
     * Find a template file
     *
     * @return  null/string
     * @throws  \MarkdownExtended\Exception\Exception if template file not found
     */
    protected function _findTemplate()
    {
        $tpl_filename       = isset($this->config['template']) ? $this->config['template'] : null;
        $user_template      = isset($this->config['user_template']) ? $this->config['user_template'] : false;
        $template_files     = isset($this->config['template_file']) ? $this->config['template_file'] : array();
        $template_default   = isset($this->config['default_template']) ? $this->config['default_template'] : null;
        if ($tpl_filename===false) {
            return null;
        }
        if (!empty($user_template)) {
            $tpl_filename = $user_template;
        } else {
            if (empty($tpl_filename)) {
                $tpl_filename = $template_default;
            }
            if (array_key_exists($tpl_filename, $template_files)) {
                $tpl_filename = $template_files[$tpl_filename];
            }
        }

        if (!empty($tpl_filename)) {
            if (!file_exists($tpl_filename)) {
                $tpl_filename = MDE_Helper::find($tpl_filename, 'template');
            }

            if (!file_exists($tpl_filename)) {
                throw new MDE_Exception\Exception(
                    sprintf('Template file "%s" not found!', $tpl_filename)
                );
            }
            return $tpl_filename;
        }
        return null;
    }

}

// Endfile
