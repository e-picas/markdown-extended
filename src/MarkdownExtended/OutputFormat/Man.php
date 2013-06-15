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
namespace MarkdownExtended\OutputFormat;

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\OutputFormatInterface,
    MarkdownExtended\OutputFormat\AbstractOutputFormat,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

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
 *      ~$ ./bin/markdown_extended -o MANPAGENAME.man -f man path/to/original.md
 * 
 */
class Man 
    implements OutputFormatInterface
{

    /**
     * List of classic manpages sections
     * @static array
     */
    public static $sections = array(
        'name', 'synopsis', 'description', 'options', 'files', 'environment', 'diagnosis', 'bugs', 'author', 'see also', 
        'examples', 'standards', 'license', 'history', 'exit status', 'copyright', 'reporting bugs'
    );

    /**
     * List of special metadata to build manpage headers
     * @static array
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

    public function buildTitle($text, array $attributes = array())
    {
        $text = html_entity_decode($text);
        if (in_array(strtolower($text), self::$sections)) {
            return $this->new_line . '.SH ' . strtoupper($text) . $this->new_line;
        } else {
            return $this->new_line . '.B ' . strtoupper($text) . $this->new_line;
        }
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
        return '    ' . str_replace("\n", $this->buildTag('new_line') . '    ', $text);
    }

    public function buildCode($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return '`\fS' . $text . $this->ending_tag . '`';
    }

    public function buildAbbreviation($text = null, array $attributes = array())
    {
        $text = html_entity_decode($text);
        return $text . (!empty($attributes['title']) ? ' (' . $attributes['title'] . ')' : '');
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

}

// Endfile
