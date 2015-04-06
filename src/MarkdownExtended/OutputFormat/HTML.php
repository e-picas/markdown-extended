<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\API\OutputFormatInterface;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Grammar\Filter\Note;

/**
 * Format a content in full HTML
 * @package MarkdownExtended\OutputFormat
 */
class HTML
    extends AbstractOutputFormat
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
        'preformatted' => array(
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
            'prefix' => '<!--dt-->',
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

    protected $config;

    /**
     * Get the configuration 'empty_element_suffix'
     */
    public function __construct()
    {
        $this->config = Kernel::getConfig('output_format_options.html');
        $this->empty_element_suffix = $this->getConfig('html_empty_element_suffix');
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
        $prefix = '';
        if (!empty($attributes)) {
            if (isset($attributes['mde-prefix'])) {
                $prefix = $attributes['mde-prefix'];
                unset($attributes['mde-prefix']);
            }
            foreach ($attributes as $variable=>$value) {
                $value = Helper::getSafeString($value);
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
            return $prefix."<{$tag}{$attr}" . $this->empty_element_suffix;
        } else {
            return $prefix."<{$tag}{$attr}>{$text}</{$tag}>";
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
            $tag = 'h' . Kernel::get('baseheaderlevel');
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
        $this->_validateLinkAttributes($attributes, $text);
        return $this->getTagString($text, 'a', $attributes);
    }

    public function buildPreformatted($text = null, array $attributes = array())
    {
        if (isset($attributes['language'])) {
            $attribute = $this->getConfig('codeblock_language_attribute');
            $attributes[$attribute] = Helper::fillPlaceholders(
                $this->getConfig('codeblock_attribute_mask'), $attributes['language']
            );
            unset($attributes['language']);
        }
        return "\n" . $this->getTagString($text, 'pre', $attributes) . "\n";
    }

    public function buildMaths($text = null, array $attributes = array(), $type = 'div')
    {
        $math_type  = $this->getConfig('math_type');
        if ($math_type == "mathjax") {
            $text = $this->getTagString('['.$text.']', 'span', array(
                    'class'=>"MathJax_Preview",
                ))
                .$this->getTagString($text, 'script', array(
                    'type'=>"math/tex".($type=='div' ? "; mode=display" : ''),
                ));
        } else {
            $text = $this->getTagString($text, $type, array(
                    'class'=>"math",
                ));
        }
        return $text;
    }

    public function buildMathsBlock($text = null, array $attributes = array())
    {
        return $this->buildMaths($text, $attributes, 'div');
    }

    public function buildMathsSpan($text = null, array $attributes = array())
    {
        return $this->buildMaths($text, $attributes, 'span');
    }

    public function buildFootnoteStandardItem($text = null, array $attributes = array(), $note_type = Note::FOOTNOTE_DEFAULT)
    {
        $type_info = Note::getTypeInfo($note_type);

        if ($this->getConfig($type_info['prefix'] . '_backlink_class')) {
            $attributes['class'] =
                Helper::fillPlaceholders(
                    parent::runGamut('tools:EncodeAttribute', $this->getConfig($type_info['prefix'] . '_backlink_class')),
                    $attributes['counter']
                );
        }
        if ($this->getConfig($type_info['prefix'] . '_backlink_title_mask')) {
            $attributes['title'] =
                Helper::fillPlaceholders(
                    parent::runGamut('tools:EncodeAttribute', $this->getConfig($type_info['prefix'] . '_backlink_title_mask')),
                    $attributes['counter']
                );
        }

        unset($attributes['counter']);
        $backlink = Kernel::get('OutputFormatBag')
            ->buildTag('link', '&#8617;', $attributes);
        $text = trim($text);
        if (preg_match('{</p>$}', $text)) {
            $text = substr($text, 0, -4) . '&#160;' . $backlink . substr($text, -4);
        } else {
            $text .= "\n\n" . Kernel::get('OutputFormatBag')
                    ->buildTag('paragraph', $backlink);
        }

        return $text;
    }

    public function buildFootnoteGlossaryItem($text = null, array $attributes = array())
    {
        return $this->buildFootnoteStandardItem($text, $attributes, Note::FOOTNOTE_GLOSSARY);
    }

    public function buildFootnoteBibliographyItem($text = null, array $attributes = array())
    {
        return $this->buildFootnoteStandardItem($text, $attributes, Note::FOOTNOTE_BIBLIOGRAPHY);
    }

    public function buildFootnoteStandardLink($text = null, array $attributes = array(), $note_type = Note::FOOTNOTE_DEFAULT)
    {
        $type_info = Note::getTypeInfo($note_type);

        if ($this->getConfig($type_info['prefix'] . '_link_class')) {
            $attributes['class'] =
                Helper::fillPlaceholders(
                    parent::runGamut('tools:EncodeAttribute', $this->getConfig($type_info['prefix'] . '_link_class')),
                    $text);
        }
        if ($this->getConfig($type_info['prefix'] . '_link_title_mask')) {
            $attributes['title'] =
                Helper::fillPlaceholders(
                    parent::runGamut('tools:EncodeAttribute', $this->getConfig($type_info['prefix'] . '_link_title_mask')),
                    $text);
        }

        $backlink_id = $attributes['backlink_id'];
        unset($attributes['backlink_id']);
        unset($attributes['counter']);
        $link = Kernel::get('OutputFormatBag')
            ->buildTag('link', $text, $attributes);

        return Kernel::get('OutputFormatBag')
            ->buildTag('sup', $link, array('id' => $backlink_id));
    }

    public function buildFootnoteGlossaryLink($text = null, array $attributes = array())
    {
        return $this->buildFootnoteStandardLink($text, $attributes, Note::FOOTNOTE_GLOSSARY);
    }

    public function buildFootnoteBibliographyLink($text = null, array $attributes = array())
    {
        return $this->buildFootnoteStandardLink($text, $attributes, Note::FOOTNOTE_BIBLIOGRAPHY);
    }

    protected function getConfig($name, $default = null)
    {
        return isset($this->config[$name]) ? $this->config[$name] : $default;
    }

    /**
     * Be sure to have a full attributes set (add a title if needed)
     *
     * @param   array   $attributes     Passed by reference
     */
    protected function _validateLinkAttributes(array &$attributes, &$text)
    {
        if (isset($attributes['email'])) {
            list($address_link, $address_text) = Helper::encodeEmailAddress($attributes['email']);
            if (!isset($attributes['href']) || empty($attributes['href'])) {
                $attributes['href'] = $address_link;
            }
            if ($this->getConfig('mailto_title_mask') && empty($attributes['title'])) {
                $attributes['title'] = Helper::fillPlaceholders(
                    $this->getConfig('mailto_title_mask'),
                    $address_text
                );
            }
            unset($attributes['email']);
            $text = $address_text;
        }

        if (empty($attributes['title']) && isset($attributes['href'])) {
            $first_char = substr($attributes['href'], 0, 1);

            if ($first_char==='#' && $this->getConfig('anchor_title_mask')) {
                $attributes['title'] = Helper::fillPlaceholders(
                    $this->getConfig('anchor_title_mask'), $attributes['href']);

            } elseif ($this->getConfig('link_title_mask')) {
                $attributes['title'] = Helper::fillPlaceholders(
                    $this->getConfig('link_title_mask'),
                    !empty($attributes['href']) ? $attributes['href'] : ''
                );

            }
        }
    }

}

// Endfile
