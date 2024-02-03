<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\OutputFormat;

use MarkdownExtended\API\Kernel;
use MarkdownExtended\API\OutputFormatInterface;
use MarkdownExtended\Grammar\Lexer;
use MarkdownExtended\Grammar\GamutLoader;
use MarkdownExtended\Util\Helper;
use MarkdownExtended\Grammar\Filter\Note;

/**
 * Format a content in full HTML
 */
class Html extends AbstractOutputFormat implements OutputFormatInterface
{
    /**
     * @var array
     */
    protected $tags_map = [
        'block' => [
            'tag' => 'div',
        ],
        'paragraph' => [
            'tag' => 'p',
        ],
        'bold' => [
            'tag' => 'strong',
        ],
        'italic' => [
            'tag' => 'em',
        ],
        'preformatted' => [
            'tag' => 'pre',
        ],
        'link' => [
            'tag' => 'a',
        ],
        'abbreviation' => [
            'tag' => 'abbr',
        ],
        'definition_list' => [
            'tag' => 'dl',
        ],
        'definition_list_item_term' => [
            'tag' => 'dt',
            'prefix' => '<!--dt-->',
        ],
        'definition_list_item_definition' => [
            'tag' => 'dd',
        ],
        'list' => [
            'tag' => 'ul',
        ],
        'list_item' => [
            'tag' => 'li',
        ],
        'unordered_list' => [
            'tag' => 'ul',
        ],
        'unordered_list_item' => [
            'tag' => 'li',
        ],
        'ordered_list' => [
            'tag' => 'ol',
        ],
        'ordered_list_item' => [
            'tag' => 'li',
        ],
        'table_caption' => [
            'tag' => 'caption',
        ],
        'table_header' => [
            'tag' => 'thead',
        ],
        'table_body' => [
            'tag' => 'tbody',
        ],
        'table_footer' => [
            'tag' => 'tfoot',
        ],
        'table_line' => [
            'tag' => 'tr',
        ],
        'table_cell' => [
            'tag' => 'td',
        ],
        'table_cell_head' => [
            'tag' => 'th',
        ],
        'meta_title' => [
            'tag' => 'title',
        ],
        'image' => [
            'tag' => 'img',
            'closable' => true,
        ],
        'new_line' => [
            'tag' => 'br',
            'closable' => true,
        ],
        'horizontal_rule' => [
            'tag' => 'hr',
            'closable' => true,
        ],
    ];

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
    public function getTagString($text, $tag, array $attributes = [], $close = false)
    {
        $attr = '';
        $prefix = '';
        if (!empty($attributes)) {
            if (isset($attributes['mde-prefix'])) {
                $prefix = $attributes['mde-prefix'];
                unset($attributes['mde-prefix']);
            }
            foreach ($attributes as $variable => $value) {
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
        if (true === $close) {
            return $prefix."<{$tag}{$attr}" . $this->empty_element_suffix;
        } else {
            return $prefix."<{$tag}{$attr}>{$text}</{$tag}>";
        }
    }

    // -------------------
    // Tag specific builder
    // -------------------

    public function buildTitle($text, array $attributes = [])
    {
        if (isset($attributes['level'])) {
            $tag = 'h' . $attributes['level'];
            unset($attributes['level']);
        } else {
            $tag = 'h' . Kernel::get('baseheaderlevel');
        }
        return $this->getTagString($text, $tag, $attributes);
    }

    public function buildMetaData($text = null, array $attributes = [])
    {
        if (empty($attributes['content']) && !empty($text)) {
            $attributes['content'] = $text;
        }
        if (!empty($attributes['name']) || !empty($attributes['http-equiv'])) {
            return $this->getTagString($text, 'meta', $attributes, true);
        }
        return $text;
    }

    public function buildComment($text = null, array $attributes = [])
    {
        return sprintf('<!-- %s -->', $text);
    }

    public function buildParagraph($text = null, array $attributes = [])
    {
        return "\n" . $this->getTagString($text, 'p', $attributes) . "\n";
    }

    public function buildLink($text = null, array $attributes = [])
    {
        $this->_validateLinkAttributes($attributes, $text);
        return $this->getTagString($text, 'a', $attributes);
    }

    public function buildPreformatted($text = null, array $attributes = [])
    {
        if (isset($attributes['language'])) {
            $attribute = $this->getConfig('codeblock_language_attribute');
            $attributes[$attribute] = Helper::fillPlaceholders(
                $this->getConfig('codeblock_attribute_mask'),
                $attributes['language']
            );
            unset($attributes['language']);
        }
        return "\n" . $this->getTagString($text, 'pre', $attributes) . "\n";
    }

    public function buildMaths($text = null, array $attributes = [], $type = 'div')
    {
        $math_type  = $this->getConfig('math_type');
        if ($math_type == "mathjax") {
            $text = $this->getTagString('['.$text.']', 'span', [
                    'class' => "MathJax_Preview",
                ])
                .$this->getTagString($text, 'script', [
                    'type' => "math/tex".($type == 'div' ? "; mode=display" : ''),
                ]);
        } else {
            $text = $this->getTagString($text, $type, [
                    'class' => "math",
                ]);
        }
        return $text;
    }

    public function buildMathsBlock($text = null, array $attributes = [])
    {
        return $this->buildMaths($text, $attributes, 'div');
    }

    public function buildMathsSpan($text = null, array $attributes = [])
    {
        return $this->buildMaths($text, $attributes, 'span');
    }

    public function buildFootnoteStandardItem($text = null, array $attributes = [], $note_type = Note::FOOTNOTE_DEFAULT)
    {
        $type_info = Note::getTypeInfo($note_type);

        if ($this->getConfig($type_info['prefix'] . '_backlink_class')) {
            $attributes['class'] =
                Helper::fillPlaceholders(
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $this->getConfig($type_info['prefix'] . '_backlink_class')),
                    $attributes['counter']
                );
        }
        if ($this->getConfig($type_info['prefix'] . '_backlink_title_mask')) {
            $attributes['title'] =
                Helper::fillPlaceholders(
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $this->getConfig($type_info['prefix'] . '_backlink_title_mask')),
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

    public function buildFootnoteGlossaryItem($text = null, array $attributes = [])
    {
        return $this->buildFootnoteStandardItem($text, $attributes, Note::FOOTNOTE_GLOSSARY);
    }

    public function buildFootnoteBibliographyItem($text = null, array $attributes = [])
    {
        return $this->buildFootnoteStandardItem($text, $attributes, Note::FOOTNOTE_BIBLIOGRAPHY);
    }

    public function buildFootnoteStandardLink($text = null, array $attributes = [], $note_type = Note::FOOTNOTE_DEFAULT)
    {
        $type_info = Note::getTypeInfo($note_type);

        if ($this->getConfig($type_info['prefix'] . '_link_class')) {
            $attributes['class'] =
                Helper::fillPlaceholders(
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $this->getConfig($type_info['prefix'] . '_link_class')),
                    $text
                );
        }
        if ($this->getConfig($type_info['prefix'] . '_link_title_mask')) {
            $attributes['title'] =
                Helper::fillPlaceholders(
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $this->getConfig($type_info['prefix'] . '_link_title_mask')),
                    $text
                );
        }

        $backlink_id = $attributes['backlink_id'];
        unset($attributes['backlink_id']);
        unset($attributes['counter']);
        $link = Kernel::get('OutputFormatBag')
            ->buildTag('link', $text, $attributes);

        return Kernel::get('OutputFormatBag')
            ->buildTag('sup', $link, ['id' => $backlink_id]);
    }

    public function buildFootnoteGlossaryLink($text = null, array $attributes = [])
    {
        return $this->buildFootnoteStandardLink($text, $attributes, Note::FOOTNOTE_GLOSSARY);
    }

    public function buildFootnoteBibliographyLink($text = null, array $attributes = [])
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

            if ($first_char === '#' && $this->getConfig('anchor_title_mask')) {
                $attributes['title'] = Helper::fillPlaceholders(
                    $this->getConfig('anchor_title_mask'),
                    $attributes['href']
                );
            } elseif ($this->getConfig('link_title_mask')) {
                $attributes['title'] = Helper::fillPlaceholders(
                    $this->getConfig('link_title_mask'),
                    !empty($attributes['href']) ? $attributes['href'] : ''
                );
            }
        }
    }
}
