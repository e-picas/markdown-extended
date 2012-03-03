<?php
/**
 * PHP Extended Markdown
 * Copyright (c) 2004-2012 Pierre Cassat
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

/**
 *
 */
class Markdown_Filter_LinkDefinition extends Markdown_Filter
{
	
 /**
  * Mandatory method
  */
	public function transform($text) { return $text; }
	
	/**
	 * Strips link definitions from text, stores the URLs and titles in
	 * hash references.
	 *
	 * Link defs are in the form: ^[id]: url "optional title"
	 *
	 * @param string $text The text to be parsed
	 * @return string The text parsed
	 * @see _stripLinkDefinitions_callback()
	 * @todo Manage attributes (not working for now)
	 */
	public function strip($text) 
	{
		$less_than_tab = Markdown_Extended::getConfig('tab_width') - 1;
		return preg_replace_callback('{
							^[ ]{0,'.$less_than_tab.'}\[(.+)\][ ]?:	# id = $1
							  [ ]*
							  \n?				# maybe *one* newline
							  [ ]*
							(?:
							  <(.+?)>		# url = $2
							|
							  (\S+?)		# url = $3
							)
							  [ ]*
							  \n?				# maybe one newline
							  [ ]*
							(?:
								(?<=\s)		# lookbehind for whitespace
								["(]
								(.*?)			# title = $4
								[")]
								[ ]*
							)?	        # title is optional
							  [ ]*
							  \n?				# maybe one newline
							  [ ]*
							(?:				  # Attributes = $5
								(?<=\s)	  # lookbehind for whitespace
								(
									([ ]*\n)?
									((?:\S+?=\S+?)|(?:.+?=.+?)|(?:.+?=".*?")|(?:\S+?=".*?"))
								)
							  [ ]*
							)?	        # attributes are optional
							(\n+|\Z)
			}xm',
			array(&$this, '_strip_callback'), $text);
	}

	/**
	 * Add each link reference to `$urls` and `$titles` tables with index `$link_id`
	 *
	 * @param array $matches A set of results of the `stripLinkDefinitions()` function
	 * @return empty
	 */
	protected function _strip_callback($matches) 
	{
		$link_id = strtolower($matches[1]);
		$url = $matches[2] == '' ? $matches[3] : $matches[2];
		Markdown_Extended::addVar('urls', array($link_id=>$url));
		Markdown_Extended::addVar('titles', array($link_id=>$matches[4]));
		Markdown_Extended::addVar('attributes', array($link_id=>$matches[5]));
		return ''; // String that will replace the block
	}

}

// Endfile