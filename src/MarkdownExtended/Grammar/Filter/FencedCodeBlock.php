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
namespace MarkdownExtended\Grammar\Filter;

use \MarkdownExtended\MarkdownExtended,
    \MarkdownExtended\Grammar\Filter;

class FencedCodeBlock extends Filter
{

	/**
	 * Adding the fenced code block syntax to regular Markdown:
	 *
	 *     ~~~
	 *     Code block
	 *     ~~~
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _doFencedCodeBlocks_callback()
	 */
	public function transform($text) 
	{
		$less_than_tab = MarkdownExtended::getConfig('tab_width');
		
		return preg_replace_callback('{
				(?:\n|\A)           # 1: Opening marker
				(
					~{3,}             # Marker: three tilde or more.
				)
				(\w+)?              # 2: Language
				[ ]* \n             # Whitespace and newline following marker.
				(                   # 3: Content
					(?>
						(?!\1 [ ]* \n)	# Not a closing marker.
						.*\n+
					)+
				)
				\1 [ ]* \n          # Closing marker
			}xm',
			array($this, '_callback'), $text);
	}

	/**
	 * Process the fenced code blocks
	 *
	 * @param array $matches Results form the `doFencedCodeBlocks()` function
	 * @return string The text parsed
	 * @see _doFencedCodeBlocks_newlines()
	 * @see hashBlock()
	 */
	protected function _callback($matches) 
	{
		$codeblock = $matches[3];
		$language  = $matches[2];
		$codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);
		$codeblock = preg_replace_callback('/^\n+/', array($this, '_newlines'), $codeblock);
		$codeblock = "<pre><code"
			.( !empty($language) ? " class=\"language-$language\"" : '' )
			.">$codeblock</code></pre>";
		return "\n\n".parent::hashBlock($codeblock)."\n\n";
	}

	/**
	 * Process the fenced code blocks new lines
	 *
	 * @param array $matches Results form the `doFencedCodeBlocks()` function (passed from the `_doFencedCodeBlocks_callback()` function)
	 * @return string The block parsed
	 */
	protected function _newlines($matches) 
	{
		return str_repeat( "<br".MarkdownExtended::getConfig('empty_element_suffix'), strlen($matches[0]) );
	}


}

// Endfile