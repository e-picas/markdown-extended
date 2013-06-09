<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended;

use MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 */
class Templater implements TemplaterInterface
{

    /**
     * @var array
     */
    protected $config;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var object MarkdownExtended\Content
     */
    protected $mde_content;

    /**
     * @var array
     */
    protected $done = array();

    /**
     * Construction of a new template object
     *
     * @param array $user_config
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
     * @return string
     */
    public function __toString()
    {
        if (!empty($this->mde_content)) {
            return $this->parse()->getContent();
        }
        return '';
    }

    /**
     * Load a content object
     *
     * @param object MarkdownExtended\Content
     *
     * @return self
     */
    public function load(Content $mde_content)
    {
        $this->mde_content = $mde_content;
        return $this;
    }

    /**
     * Get the current content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * Get a template content
     *
     * @return string
     *
     * @throws MarkdownExtended\Excpetion\Exception if template file not found
     */
    public function getTemplate()
    {
        $tpl_filename = $this->config['template'];
        $template_files = $this->config['template_file'];
        if ($tpl_filename===false) {
            return '';
        }

        if (empty($tpl_filename)) {
            $tpl_filename = $this->config['default_template'];
        }
        if (array_key_exists($tpl_filename, $template_files)) {
            $tpl_filename = $template_files[$tpl_filename];
        }

        $tpl_filename = MDE_Helper::find($tpl_filename, 'template');
        if (file_exists($tpl_filename)) {
            $tpl_content = file_get_contents($tpl_filename);
        } else {
            throw new MDE_Excpetion\Exception(
                sprintf('Template file "%s" not found!', $tpl_filename)
            );
        }
        return $tpl_content;
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
     * @param string $keyword
     *
     * @return string
     */
    protected function _buildKeywordMask($keyword)
    {
        return sprintf($this->config['keywords_mask'], $keyword);
    }

    /**
     * @param string $content
     * @param array $keywords
     * @param bool $force_insert
     *
     * @return string
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
                        $content_block = call_user_func(array($this->mde_content, $block_name_method));
                    }
                } else {
                    $content_block = ' ';
                }
                if (!empty($content_block)) {
                    $content = str_replace($matches[0], $content_block, $content);
                    if (!in_array($block_name, $this->done)) {
                        $this->done[] = $block_name;
                    }
                }
            }
        }
        return $content;
    }

}

// Endfile
