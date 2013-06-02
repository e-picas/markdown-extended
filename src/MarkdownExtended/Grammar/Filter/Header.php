<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2004-2013 Pierre Cassat
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

class Header extends Filter
{
	
	public $ids = array();
	
	/**
	 * Redefined to add id attribute support.
	 *
	 * Setext-style headers:
	 *	  Header 1  {#header1}
	 *	  ========
	 *  
	 *	  Header 2  {#header2}
	 *	  --------
	 *
	 * ATX-style headers:
	 *	# Header 1        {#header1}
	 *	## Header 2       {#header2}
	 *	## Header 2 with closing hashes ##  {#header3}
	 *	...
	 *	###### Header 6   {#header2}
	 *
	 * @param string $text Text to parse
	 * @return string Text with all headers parsed
	 * @see _doHeaders_callback_setext()
	 * @see _doHeaders_callback_atx()
	 */
	public function transform($text) 
	{
		// Setext-style headers:
		$text = preg_replace_callback(
			'{
				(^.+?)								            # $1: Header text
				(?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})?	# $2: Id attribute
				[ ]*\n(=+|-+)[ ]*\n+				      # $3: Header footer
			}mx',
			array($this, '_setext_callback'), $text);

		// atx-style headers:
		$text = preg_replace_callback('{
				^(\#{1,6})	                     # $1 = string of #\'s
				[ ]*
				(.+?)		                         # $2 = Header text
				[ ]*
				\#*			                         # optional closing #\'s (not counted)
				(?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})? # id attribute
				[ ]*
				\n+
			}xm',
			array($this, '_atx_callback'), $text);

		return $text;
	}

	/**
	 * Process setext-style headers:
	 *	  Header 1  {#header1}
	 *	  ========
	 *  
	 *	  Header 2  {#header2}
	 *	  --------
	 *
	 * @param array $matches The results from the `doHeaders()` function
	 * @return string Text with header parsed
	 * @see _doHeaders_attr()
	 * @see span_gamut()
	 * @see hashBlock()
	 */
	protected function _setext_callback($matches) 
	{
		if ($matches[3] == '-' && preg_match('{^- }', $matches[1]))
			return $matches[0];
		$level = $matches[3]{0} == '=' ? 1 : 2;
		$attr  = self::_attributes($id =& $matches[2]);
		$block = "<h$level$attr>".parent::runGamut('span_gamut', $matches[1])."</h$level>";
		return "\n" . parent::hashBlock($block) . "\n\n";
	}

	/**
	 * Process ATX-style headers:
	 *	# Header 1        {#header1}
	 *	## Header 2       {#header2}
	 *	## Header 2 with closing hashes ##  {#header3}
	 *	...
	 *	###### Header 6   {#header2}
	 *
	 * @param array $matches The results from the `doHeaders()` function
	 * @return string Text with header parsed
	 * @see _doHeaders_attr()
	 * @see span_gamut()
	 * @see hashBlock()
	 */
	protected function _atx_callback($matches) 
	{
		$level = strlen($matches[1]);
		if (!empty($matches[3]))
    		$attr  = self::_attributes($id =& $matches[3]);
		else
			$attr  = self::_attributes($id =& parent::runGamut('tool:Header2Label', $matches[2]));
		$block = "<h$level$attr>".parent::runGamut('span_gamut', $matches[2])."</h$level>";
		return "\n" . parent::hashBlock($block) . "\n\n";
	}

	/**
	 * Adding headers attributes if so 
	 *
	 * @param str $attr The attributes string
	 * @return string Text to add in the header tag
	 */
	protected function _attributes($attr) 
	{
		if (empty($attr)) return '';
		$id = $attr;
		if (in_array($id, $this->ids)) {
			$i=0;
			while (in_array($id, $this->ids)) {
				$i++;
				$id = (string)$attr.$i;
			}
		}
		$this->ids[] = $id;
		return " id=\"$id\"";
	}

}

// Endfile