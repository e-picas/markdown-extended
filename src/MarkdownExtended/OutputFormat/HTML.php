<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
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
namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\OutputFormatInterface;
use \MarkdownExtended\API\OutputFormat\AbstractOutputFormat;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Format a content in full HTML
 */
class HTML 
    extends AbstractOutputFormatHTML
    implements OutputFormatInterface
{

    /**
     * @var array
     */
    protected $tags_map = array(
        'block' => array(
            'tag'=>'div',
        ),
        'paragraph' => array(
            'tag'=>'p',
        ),
        'bold' => array(
            'tag'=>'strong',
        ),
        'italic' => array(
            'tag'=>'em',
        ),
        'preformated' => array(
            'tag'=>'pre',
        ),
        'link' => array(
            'tag'=>'a',
        ),
        'abbreviation' => array(
            'tag'=>'abbr',
        ),
        'definition_list' => array(
            'tag'=>'dl',
        ),
        'definition_list_item_term' => array(
            'tag'=>'dt',
        ),
        'definition_list_item_definition' => array(
            'tag'=>'dd',
        ),
        'list' => array(
            'tag'=>'ul',
        ),
        'list_item' => array(
            'tag'=>'li',
        ),
        'unordered_list' => array(
            'tag'=>'ul',
        ),
        'unordered_list_item' => array(
            'tag'=>'li',
        ),
        'ordered_list' => array(
            'tag'=>'ol',
        ),
        'ordered_list_item' => array(
            'tag'=>'li',
        ),
        'table_caption' => array(
            'tag'=>'caption',
        ),
        'table_header' => array(
            'tag'=>'thead',
        ),
        'table_body' => array(
            'tag'=>'tbody',
        ),
        'table_footer' => array(
            'tag'=>'tfoot',
        ),
        'table_line' => array(
            'tag'=>'tr',
        ),
        'table_cell' => array(
            'tag'=>'td',
        ),
        'table_cell_head' => array(
            'tag'=>'th',
        ),
        'meta_title' => array(
            'tag'=>'title',
        ),
        'image' => array(
            'tag'=>'img',
            'closable'=>true,
        ),
        'new_line' => array(
            'tag'=>'br',
            'closable'=>true,
        ),
        'horizontal_rule' => array(
            'tag'=>'hr',
            'closable'=>true,
        ),
    );
        
    /**
     * @var string
     */
    protected $empty_element_suffix;

    /**
     * Get the configuration 'empty_element_suffix'
     */
    public function __construct()
    {
        $this->empty_element_suffix = MarkdownExtended::getConfig('html_empty_element_suffix');
    }

    /**
     * Builder of HTML tags :
     *     <TAG ATTR1="ATTR_VAL1" ... > TEXT </TAG>
     *
     * @param string $text The content of the tag
     * @param string $tag The tag name
     * @param array $attributes An array of attributes constructed by "variable=>value" pairs
     * @param bool $close Is it a closed tag ? (FALSE by default)
     *
     * @return string The built tag string
     */
    public function getTagString($text, $tag, array $attributes = array(), $close = false)
    {
        $attr='';
        if (!empty($attributes)) {
            foreach ($attributes as $variable=>$value) {
                if (!empty($value)) {
                    if (is_string($variable)) {
                        $attr .= " {$variable}=\"{$value}\"";
                    } else {
                        $attr .= ' '.trim($value);
                    }
                }
            }
        }
        if (true===$close) {
            return "<{$tag}{$attr}" . $this->empty_element_suffix;
        } else {
            return "<{$tag}{$attr}>{$text}</{$tag}>";
        }
    }

// -------------------
// Tag specific builder
// -------------------

    public function buildTitle($text, array $attributes = array())
    {
        if (isset($attributes['level'])) {
            $tag = 'h' . $attributes['level'];
            unset($attributes['level']);
        } else {
            $tag = 'h' . MarkdownExtended::getVar('baseheaderlevel');
        }       
        return $this->getTagString($text, $tag, $attributes);
    }
    
    public function buildMetaData($text = null, array $attributes = array())
    {
        if (empty($attributes['content']) && !empty($text)) {
            $attributes['content'] = $text;
        }
        if (!empty($attributes['name']) || !empty($attributes['http-equiv'])) {
            return $this->getTagString($text, 'meta', $attributes, true);
        }
        return $text;
    }

    public function buildComment($text = null, array $attributes = array())
    {
        return sprintf('<!-- %s -->', $text);
    }

    public function buildParagraph($text = null, array $attributes = array())
    {
        return "\n" . $this->getTagString($text, 'p', $attributes) . "\n";
    }

    public function buildLink($text = null, array $attributes = array())
    {
        if (isset($attributes['email'])) {
            unset($attributes['email']);
        }
        return $this->getTagString($text, 'a', $attributes);
    }

}

// Endfile
