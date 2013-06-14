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
 * Format a content in full HTML
 */
class Man 
    implements OutputFormatInterface
{

    public static $sections = array(
        'name', 'synopsys', 'description', 'options', 'files', 'environement', 'diagnosis', 'bugs', 'author', 'see also', 
        'examples', 'standards', 'license', 'history'
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
	    return $content;
	}

// -------------------
// Tag specific builder
// -------------------

	public function buildTitle($text, array $attributes = array())
	{
	    if (in_array(strtolower($text), self::$sections)) {
			return $this->new_line . '.SH ' . strtoupper($text) . $this->new_line;
	    } else {
			return $this->new_line . '.B ' . strtoupper($text) . $this->new_line;
	    }
	}
	
	public function buildMetaData($text = null, array $attributes = array())
	{
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
	    return '.TH ' . $text . $this->new_line;
	}

	public function buildBlock($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildParagraph($text = null, array $attributes = array())
	{
		return $this->new_line . '.PP' . $this->new_line . $text . $this->new_line;
	}

	public function buildBold($text = null, array $attributes = array())
	{
		return '\fB' . $text . $this->ending_tag;
	}

	public function buildItalic($text = null, array $attributes = array())
	{
		return '\fI' . $text . $this->ending_tag;
	}

	public function buildPreformated($text = null, array $attributes = array())
	{
		return '`' . $text . '`';
	}

	public function buildLink($text = null, array $attributes = array())
	{
		return '<' . $text . '>';
	}

	public function buildAbbreviation($text = null, array $attributes = array())
	{
		return $text . (!empty($attributes['title']) ? ' (' . $attributes['title'] . ')' : '');
	}

	public function buildDefinitionList($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildDefinitionListItemTerm($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildDefinitionListItemDefinition($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildList($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildListItem($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildUnorderedList($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildUnorderedListItem($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildOrderedList($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildOrderedListItem($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableCaption($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableHeader($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableBody($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableFooter($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableLine($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableCell($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildTableCellHead($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildImage($text = null, array $attributes = array())
	{
	    return $text;
	}

	public function buildNewLine($text = null, array $attributes = array())
	{
		return '.br' . $this->new_line;
	}

	public function buildHorizontalRule($text = null, array $attributes = array())
	{
	    return '';
	}

}

// Endfile