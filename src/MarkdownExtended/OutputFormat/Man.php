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
 * Format a content in UNIX Manpage format
 *
 * Use special meta data to complete the manpage headers:
 *
 * -   `name`: the name of the program/command
 * -   `section`: the man pages section number (default is "3" for libraries)
 * -   `version`: the program/command version number
 * -   `date`: the date of this version
 * -   `man`: the name of the manpage
 *
 * Usage:
 *
 *      ~$ ./bin/markdown-extended -o MANPAGENAME.man -f man path/to/original.md
 * 
 * @see http://manpages.ubuntu.com/manpages/oneiric/man7/groff_man.7.html
 */
class Man 
    implements OutputFormatInterface
{

    /**
     * List of classic manpages sections
     * @var array
     */
    public static $sections = array(
        'name', 'synopsis', 'syntax', 'availability', 'description', 'options', 'files', 'resources', 'environment', 'diagnosis', 'bugs', 'author', 'see also', 
        'examples', 'standards', 'license', 'history', 'exit status', 'messages', 'copyright', 'reporting bugs', 'notes'
    );

    /**
     * List of special metadata to build manpage headers
     * @var array
     */
    public static $headers_meta_data = array(
        'name', 'version', 'date', 'section', 'man'
    );

    /**
     * @var string
     */
    protected $ending_tag;

    /**
     * @var string
     */
    protected $new_line;

    /**
     * Remind some commons
     */
    public function __construct()
    {
        $this->ending_tag = '\fP';
        $this->new_line = "\n";
    }

    /**
     * This will try to call a method `builTagName()` if it exists, then will try to use
     * the object `$tags_map` static to automatically find what to do, and then call the 
     * default `getTagString()` method passing it the arguments.
     *
     * @param string $tag_name
     * @param string $content
     * @param array $attributes An array of attributes constructed like "variable=>value" pairs
     *
     * @return string
     */
    public function buildTag($tag_name, $content = null, array $attributes = array())
    {
        $_method = 'build'.MDE_Helper::toCamelCase($tag_name);
        if (method_exists($this, $_method)) {
            return call_user_func_array(
                array($this, $_method),
                array($content, $attributes)
            );
        } else {
            return call_user_func_array(
                array($this, 'getTagString'),
                array($content, $tag_name, $attributes)
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
    public function getTagString($content, $tag_name, array $attributes = array())
    {
        return html_entity_decode($content);
    }

// -------------------
// Tag specific builder
// -------------------

    protected $_current_title_level = 0;
    protected $subtitle_max_level = 3;

    public function buildTitle($text, array $attributes = array())
    {
        $text = html_entity_decode($text);
        $level = isset($attributes['level']) ? $attributes['level'] : '1';
        $indent = '';
        if ($this->_current_title_level!==0) {
            $lvl = $level;
            if ($lvl>=$this->_current_title_level) {
                while ($lvl>=$this->_current_title_level) {
                    $indent .= $this->unindent();
                    $lvl--;
                }
            } elseif ($lvl<=$this->_current_title_level) {
                while ($lvl<=$this->_current_title_level) {
                    $indent .= $this->indent();
                    $lvl++;
                }
            }
        }
        if ((int) $level <= $this->subtitle_max_level && in_array(strtolower($text), self::$sections)) {
            $this->_current_title_level = 0;
            return $this->new_line . $indent . '.SH ' . strtoupper($text) . $this->new_line;
        } elseif ((int) $level <= $this->subtitle_max_level) {
            $this->_current_title_level = 0;
            return $this->new_line . $indent . '.SS ' . $text . $this->new_line;
        } else {
//            $indent .= $this->indent();
            $id = isset($attributes['id']) ? $attributes['id'] : $text;
            $this->_current_title_level = $level;
            return $this->new_line . $indent . '.TP '
                . $id . $this->new_line
                . $this->buildBold($text) . $this->new_line
                . $this->indent();
        }
    }

    public function indent()
    {
        return '.RS' . $this->new_line;
    }
    
    public function unindent()
    {
        return '.RE' . $this->new_line;
    }
    
    public function buildMetaData($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        if (!empty($attributes['name'])) {
            if (empty($attributes['content']) && !empty($text)) {
                $attributes['content'] = $text;
            }
            return '.\" ' . $attributes['name'] . ': ' . $attributes['content'] . $this->new_line;
        }
        return '.\" ' . $text . $this->new_line;
    }

    public function buildMetaTitle($text = null, array $attributes = array())
    {
        return "\n" . '.TH '
            . ' "' . (!empty($attributes['name']) ? $attributes['name'] : '') . '"'
            . ' "' . (!empty($attributes['section']) ? $attributes['section'] : '3') . '"'
            . ' "' . (!empty($attributes['date']) ? $attributes['date'] : '') . '"'
            . ' "' . (!empty($attributes['version']) ? 'Version '.str_replace(array('version', 'Version'), '', $attributes['version']) : '') . '"'
            . ' "' . (!empty($attributes['man']) ? $attributes['man'] : '') . '"'
            . $this->new_line;
    }

    public function buildParagraph($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return $this->new_line . '.PP' . $this->new_line . $text . $this->new_line;
    }

    public function buildBold($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return '\fB' . trim($text) . $this->ending_tag;
    }

    public function buildItalic($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return '\fI' . trim($text) . $this->ending_tag;
    }

    public function buildPreformated($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return 
            $this->indent()
            . $this->new_line . '.EX' . $this->new_line
            . str_replace("\n", $this->buildTag('new_line') . '    ', $text)
            . $this->new_line . '.EE' . $this->new_line
            . $this->unindent()
            ;
    }

    public function buildCode($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return '`\fS' . $text . $this->ending_tag . '`';
    }

    public function buildAbbreviation($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return $text . (!empty($attributes['title']) ? ' ('
            . $this->new_line . '.SM '. $attributes['title']
            . $this->new_line . ')' 
            : '');
    }

    public function buildDefinitionListItemTerm($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return '.TP' . $this->new_line . trim($text);
    }

    public function buildDefinitionListItemDefinition($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return $this->new_line . trim($text);
    }

    public function buildNewLine($text = null, array $attributes = array())
    {
        return $this->new_line . '.br' . $this->new_line;
    }

    public function buildHorizontalRule($text = null, array $attributes = array())
    {
        return '';
    }

    public function buildComment($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return '.\"' . $text;
    }

    public function buildListItem($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return $this->new_line . '.IP \(bu ' . $this->new_line . $text . $this->new_line;
    }

    public function buildUnorderedListItem($text = null, array $attributes = array())
    {
        return $this->buildListItem($text, $attributes);
    }

    protected $ordered_list_counter = 1;

    public function buildOrderedListItem($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        $str = $this->new_line . '.IP ' . $this->ordered_list_counter 
            . ' ' . $this->new_line . $text . $this->new_line;
        $this->ordered_list_counter++;
        return $str;
    }

    public function buildList($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return $this->new_line . $this->indent()
            . $text
            . $this->new_line . $this->unindent();
    }

    public function buildUnorderedList($text = null, array $attributes = array())
    {
        return $this->buildList($text, $attributes);
    }

    public function buildOrderedList($text = null, array $attributes = array())
    {
        $this->ordered_list_counter = 1;
        return $this->buildList($text, $attributes);
    }

    public function buildLink($text = null, array $attributes = array())
    {
        if (isset($attributes['email'])) {
            $href = $text = $attributes['email'];
        } else {
            $href = isset($attributes['href']) ? $attributes['href'] : $text;
        }
        return $text!==$href ? 
            $text . ' <' . $href . '>' : '<' . $text . '>';
    }

}

// Endfile
