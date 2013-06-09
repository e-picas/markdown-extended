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
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 */
class AbstractOutputFormatHTML
{

    /**
     * Array of items constructed like:
     *
     *      interface tag name => array(
     *          'tag' => HTML tag name,
     *          'closable' => bool, // is the tag closed without content (i.e. "<br />")
     *      )
     *
     * @var array
     */
    protected $tags_map = array();

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
	    } elseif (isset($this->tags_map[$tag_name])) {
	        $new_tag_name = isset($this->tags_map[$tag_name]['tag']) ?
	            $this->tags_map[$tag_name]['tag'] : $tag_name;
	        $closable = isset($this->tags_map[$tag_name]['closable']) ?
	            $this->tags_map[$tag_name]['closable'] : false;
	        return call_user_func_array(
	            array($this, 'getTagString'),
	            array($content, $new_tag_name, $attributes, $closable)
	        );
	    } else {
	        return call_user_func_array(
	            array($this, 'getTagString'),
	            array($content, $tag_name, $attributes)
	        );
	    }
	}
	
}

// Endfile