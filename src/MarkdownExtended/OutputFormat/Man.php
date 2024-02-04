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

use MarkdownExtended\API\OutputFormatInterface;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\Util\Helper;

/**
 * Format a content in UNIX Manpage format
 *
 * Use special meta data to complete the manpage headers:
 *
 * -   `name` or `man-name` (preferred): the name of the program/command
 * -   `section`: the man pages section number (default is "3" for libraries)
 * -   `version`: the program/command version number
 * -   `date`: the date of this version
 * -   `man`: the name of the manpage
 *
 * Usage:
 *
 *      ~$ ./bin/markdown-extended -o MANPAGENAME.man -f man path/to/original.md
 *
 * @link https://www.gnu.org/software/groff/
 * @link http://manpages.ubuntu.com/manpages/oneiric/man7/groff_man.7.html
 */
class Man extends AbstractOutputFormat implements OutputFormatInterface
{
    /**
     * The table of tags to use
     *
     * @var array
     */
    protected $tags_map = [
        'indent' => [
            'tag' => '.RS',
        ],
        'unindent' => [
            'tag' => '.RE',
        ],
        'title' => [
            'levels' => [
                1 => [
                    'tag' => '.SH',
                ],
                2 => [
                    'tag' => '.SS',
                ],
                3 => [
                    'tag' => '.TP',
                ],
            ],
        ],
        'paragraph' => [
            'tag' => '.PP',
        ],
        'bold' => [
            'tag' => '\fB',
            'closable' => true,
        ],
        'italic' => [
            'tag' => '\fI',
            'closable' => true,
        ],
        'preformatted' => [
            'tag' => '.EX',
            'end_tag' => '.EE',
            'indentable' => true,
        ],
        'code' => [
            'tag' => '`\fS',
            'end_tag' => '`',
            'closable' => true,
        ],
        'abbreviation' => [
            'tag' => '.SM',
        ],
        'definition_list_item_term' => [
            'tag' => '.TP',
            'prefix' => '<!--dt-->',
        ],
        'list' => [
            'tag' => 'ul',
            'indentable' => true,
        ],
        'unordered_list_item' => [
            'tag' => '.IP ',
        ],
        'ordered_list_item' => [
            'tag' => '.IP \(bu ',
        ],
        'new_line' => [
            'tag' => '.br',
        ],
        'link' => [
            'tag' => '.UR',
            'end_tag' => '.UE',
        ],
        'mailto_link' => [
            'tag' => '.MT',
            'end_tag' => '.ME',
        ],
    ];

    /**
     * List of classic manpages sections
     * @var array
     * @deprecated Titles are not filtered now
     */
    public static $sections = [
        'name', 'synopsis', 'syntax', 'availability', 'description', 'options', 'files', 'resources', 'environment', 'diagnosis', 'bugs', 'author', 'see also',
        'examples', 'standards', 'license', 'history', 'exit status', 'messages', 'copyright', 'reporting bugs', 'notes',
    ];

    /**
     * List of special metadata to build manpage headers
     * @var array
     */
    public static $headers_meta_data = [
        'man-name', 'name', 'version', 'date', 'section', 'man',
    ];

    /**
     * The tag closing string
     * @var string
     */
    protected $ending_tag;

    /**
     * The new line string
     * @var string
     */
    protected $new_line;

    /**
     * Flag to turn on when paragraphing is not allowed (will be replace by new line)
     * @var bool
     */
    protected $no_paragraphing = false;

    /**
     * Remind some commons
     */
    public function __construct()
    {
        $this->ending_tag = '\fP';
        $this->new_line = "\n";
    }

    /**
     * This will try to call a method `build{TagName}()` if it exists, then will try to use
     * the object `$tags_map` static to automatically find what to do, and then call the
     * default `getTagString()` method passing it the arguments.
     *
     * @param string $tag_name
     * @param string $content
     * @param array $attributes An array of attributes constructed like "variable=>value" pairs
     *
     * @return string
     */
    public function buildTag($tag_name, $content = null, array $attributes = [])
    {
        $_method = 'build'.Helper::toCamelCase($tag_name);
        if (method_exists($this, $_method)) {
            return call_user_func_array(
                [$this, $_method],
                [$content, $attributes]
            );
        } else {
            return call_user_func_array(
                [$this, 'getTagString'],
                [$content, $tag_name, $attributes]
            );
        }
    }

    /**
     * @param string $content
     * @param string $tag_name
     * @param array $attributes An array of attributes constructed like "variable=>value" pairs
     *
     * @return string
     */
    public function getTagString($content, $tag_name, array $attributes = [])
    {
        return $this->escapeString($content);
    }

    /**
     * Escape a string ready for manpage output
     *
     * @param   null    $text
     * @param   bool    $escape_slashes
     * @param   bool    $strip_blank_lines
     * @return null|string
     */
    public function escapeString($text = null, $escape_slashes = false, $strip_blank_lines = true)
    {
        $text = html_entity_decode($text);
        if ($escape_slashes) {
            $slash  = preg_quote(chr(92));
            //            $mask   = $slash.'(?![fB|fI|fS|fP])';
            $text   = preg_replace('/' . $slash . '/', $slash . $slash, $text);
        }
        if ($strip_blank_lines) {
            $text   = preg_replace("/^[\n]+/", "\n", $text);
        }
        if (!empty($text) && $text[0] == "'") {
            $text = '\\' . $text;
        }
        return $text;
        //        return $this->trimString($text);
    }

    /**
     * Special `trim()` to actually trim and "r-trim" last EOL
     *
     * @param null $text
     * @return string
     */
    public function trimString($text = null)
    {
        $text = trim($text);
        $text = rtrim($text, "\n");
        return $text;
    }

    // -------------------
    // Content's blocks builder
    // -------------------

    public function teardown($text)
    {
        $headers    = [];
        $content    = Kernel::get(Kernel::TYPE_CONTENT);

        foreach ($content->getMetadata() as $name => $value) {
            if ($name === 'title') {
                $headers['name'] = $value;
            } elseif (in_array($name, self::$headers_meta_data)) {
                $headers[$name] = $value;
            }
        }

        $title = $content->getTitle();
        if (empty($headers['name']) && $title) {
            $headers['name'] = $title;
        }

        $text = $this->buildTag('meta_title', null, $headers) . $text;
        return $text;
    }

    // -------------------
    // Tag specific builder
    // -------------------

    /**
     * The current level flag
     * @var int
     */
    protected $_current_title_level = 0;

    /**
     * The max level flag
     * @var int
     */
    protected $subtitle_max_level = 3;

    public function buildTitle($text, array $attributes = [])
    {
        $text = $this->escapeString($text);
        $level = isset($attributes['level']) ? $attributes['level'] : '1';
        $indent = '';
        if ($this->_current_title_level !== 0) {
            $lvl = $level;
            if ($lvl >= $this->_current_title_level) {
                while ($lvl >= $this->_current_title_level) {
                    $indent .= $this->unindent();
                    $lvl--;
                }
            } elseif ($lvl <= $this->_current_title_level) {
                while ($lvl <= $this->_current_title_level) {
                    $indent .= $this->indent();
                    $lvl++;
                }
            }
        }
        //        if ((int) $level <= $this->subtitle_max_level && in_array(strtolower($text), self::$sections)) {
        if ($level <= 2) {
            $this->_current_title_level = 0;
            return $indent . '.SH ' . strtoupper($text) . $this->new_line;
        } elseif ((int) $level <= $this->subtitle_max_level) {
            $this->_current_title_level = 0;
            return $indent . '.SS ' . $text . $this->new_line;
        } else {
            $domid = isset($attributes['id']) ? $attributes['id'] : $text;
            $this->_current_title_level = $level;
            return $indent . '.TP '
                . $domid . $this->new_line
                . $this->buildBold($text) . $this->new_line
                . $this->indent();
        }
    }

    public function indent()
    {
        return !$this->no_paragraphing ? '.RS' . $this->new_line : '';
    }

    public function unindent()
    {
        return !$this->no_paragraphing ?
            /*$this->new_line .*/ '.RE' . $this->new_line : '';
    }

    public function buildMetaData($text = null, array $attributes = [])
    {
        $text = $this->escapeString($text);
        if (!empty($attributes['name'])) {
            if (empty($attributes['content']) && !empty($text)) {
                $attributes['content'] = $text;
            }
            return '.\" ' . $attributes['name'] . ': ' . Helper::getSafeString($attributes['content']) /*. $this->new_line*/;
        }
        return '.\" ' . $text /*. $this->new_line*/;
    }

    public function buildMetaTitle($text = null, array $attributes = [])
    {
        return '.TH '
            . ' "' . (!empty($attributes['man-name']) ? $attributes['man-name'] : '') . '"'
            . ' "' . (!empty($attributes['section']) ? $attributes['section'] : '3') . '"'
            . ' "' . (!empty($attributes['date']) ? $attributes['date'] : '') . '"'
            . ' "' . (!empty($attributes['version']) ? 'Version '.str_replace(['version', 'Version'], '', $attributes['version']) : '') . '"'
            . ' "' . (!empty($attributes['man']) ? $attributes['man'] : '') . '"'
            . $this->new_line;
    }

    public function buildParagraph($text = null, array $attributes = [])
    {
        if ($this->no_paragraphing) {
            return $this->new_line
                . $this->trimString($this->escapeString($text))
                . $this->new_line;
        } else {
            return '.PP' . $this->new_line
                . $this->trimString($this->escapeString($text))
                . $this->new_line;
        }
    }

    public function buildBold($text = null, array $attributes = [])
    {
        return '\fB'
            . $this->trimString($this->escapeString($text))
            . $this->ending_tag;
    }

    public function buildItalic($text = null, array $attributes = [])
    {
        return '\fI'
            . $this->trimString($this->escapeString($text))
            . $this->ending_tag;
    }

    public function buildPreformatted($text = null, array $attributes = [])
    {
        $lines = explode("\n", $text);
        $text = '';
        foreach ($lines as $i => $line) {
            $text .= $this->escapeString($line, true);
            if ($i < count($lines) - 1) {
                $text .= $this->new_line . $this->buildTag('new_line');
            }
        }
        if ($this->no_paragraphing) {
            return
                $this->indent()
                /*. $this->new_line*/ . $this->buildTag('new_line')
                . $text
                . $this->new_line . $this->buildTag('new_line')
                . $this->unindent()
            ;
        } else {
            return
                $this->indent()
                . $this->new_line . '.EX' . $this->new_line
                . $text
                . $this->new_line . '.EE' . $this->new_line
                . $this->unindent()
            ;
        }
    }

    public function buildCode($text = null, array $attributes = [])
    {
        return '`\fS'
            . $this->escapeString($text, true)
            . $this->ending_tag . '`';
    }

    public function buildAbbreviation($text = null, array $attributes = [])
    {
        return $this->escapeString($text)
            . (!empty($attributes['title']) ?
                ' ('  . $this->new_line
                . '.SM '. $attributes['title']
                . $this->new_line . ')'
            : '');
    }

    public function buildDefinitionList($text = null, array $attributes = [])
    {
        $this->no_paragraphing = false;
        return $this->trimString($text) . $this->new_line;
    }

    public function buildDefinitionListItemTerm($text = null, array $attributes = [])
    {
        $this->no_paragraphing = true;
        $text = html_entity_decode($text);
        return '<!--dt-->.TP' . $this->new_line
            . $this->trimString($text) . $this->new_line;
    }

    public function buildDefinitionListItemDefinition($text = null, array $attributes = [])
    {
        $this->no_paragraphing = true;
        $text = $this->escapeString($text);
        return $this->new_line . $this->trimString($text);
    }

    public function buildNewLine($text = null, array $attributes = [])
    {
        return '.br' . $this->new_line;
    }

    public function buildHorizontalRule($text = null, array $attributes = [])
    {
        return $this->new_line . '--------------------' . $this->new_line;
    }

    public function buildComment($text = null, array $attributes = [])
    {
        return '.\"' . $this->escapeString($text);
    }

    public function buildListItem($text = null, array $attributes = [])
    {
        $this->no_paragraphing = true;
        return '.IP \(bu ' . $this->new_line
            . $this->escapeString($text)
            . $this->new_line;
    }

    public function buildUnorderedListItem($text = null, array $attributes = [])
    {
        return $this->buildListItem($text, $attributes);
    }

    /**
     * Internal counter for list items
     * @var int
     */
    protected $ordered_list_counter = 1;

    public function buildOrderedListItem($text = null, array $attributes = [])
    {
        $this->no_paragraphing = true;
        $str = '.IP ' . $this->ordered_list_counter . '.'
            . $this->new_line
            . $this->escapeString($text)
            . $this->new_line;
        $this->ordered_list_counter++;
        return $str;
    }

    public function buildList($text = null, array $attributes = [])
    {
        $this->no_paragraphing = false;
        return $this->indent()
            . $this->escapeString($text)
            . $this->unindent();
    }

    public function buildUnorderedList($text = null, array $attributes = [])
    {
        return $this->buildList($text, $attributes);
    }

    public function buildOrderedList($text = null, array $attributes = [])
    {
        $this->ordered_list_counter = 1;
        return $this->buildList($text, $attributes);
    }

    public function buildLink($text = null, array $attributes = [])
    {
        if (isset($attributes['email'])) {
            $open_tag   = '.MT';
            $close_tag  = '.ME';
            $href       = $attributes['email'];
        } else {
            $open_tag   = '.UR';
            $close_tag  = '.UE';
            $href       = isset($attributes['href']) ? $attributes['href'] : $text;
        }
        return ($text !== $href ? $text . ' <' : '<')
//            . $this->new_line . $open_tag . ' ' . $href . $this->new_line
            . $href
//            . $this->new_line . $close_tag
            . ($text !== $href ? '>' : '>')
//            . $this->new_line
        ;
    }

    public function buildBlockquote($text = null, array $attributes = [])
    {
        return
            $this->indent()
            . $this->new_line . '"' . $this->new_line
            . $this->escapeString($this->trimString($text))
            . $this->new_line . '"' . $this->new_line
            . $this->unindent()
        ;
    }

    public function buildTableCaption($text = null, array $attributes = [])
    {
        return $this->new_line . $this->buildBold($text) . $this->new_line;
    }

    public function buildTableBody($text = null, array $attributes = [])
    {
        return $this->escapeString($text);
    }

    public function buildTableHeader($text = null, array $attributes = [])
    {
        return $this->buildTableBody($text, $attributes);
    }

    public function buildTableFooter($text = null, array $attributes = [])
    {
        return $this->buildTableBody($text, $attributes);
    }

    public function buildTableLine($text = null, array $attributes = [])
    {
        return '| ' . $this->escapeString($text);
    }

    public function buildTableCell($text = null, array $attributes = [])
    {
        return $text . ' | ';
    }

    public function buildTableCellHead($text = null, array $attributes = [])
    {
        return $this->buildBold($text) . ' | ';
    }
}
