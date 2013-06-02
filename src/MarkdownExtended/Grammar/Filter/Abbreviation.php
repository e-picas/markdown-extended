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

class Abbreviation extends Filter
{

	public function _setup()
	{
		MarkdownExtended::setVar('abbr_desciptions', array());
		MarkdownExtended::setVar('abbr_word_re', '');
		$abbr_word_re='';
		$abbr_desciptions = array();
		foreach (MarkdownExtended::getVar('predef_abbr') as $abbr_word => $abbr_desc) {
			if ($abbr_word_re) $abbr_word_re .= '|';
			$abbr_word_re .= preg_quote($abbr_word);
			$abbr_desciptions[$abbr_word] = trim($abbr_desc);
		}
		MarkdownExtended::setVar('abbr_word_re', $abbr_word_re);
		MarkdownExtended::setVar('abbr_desciptions', $abbr_desciptions);
	}

	public function _teardown()
	{
		MarkdownExtended::setVar('abbr_desciptions', array());
		MarkdownExtended::setVar('abbr_word_re', '');
	}

	/**
	 * Find defined abbreviations in text and wrap them in <abbr> elements.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _doAbbreviations_callback()
	 */
	public function transform($text) 
	{
		if (MarkdownExtended::getConfig('abbr_word_re')) {
			// cannot use the /x modifier because abbr_word_re may 
			// contain significant spaces:
			$text = preg_replace_callback('{'.
				'(?<![\w\x1A])'.
				'(?:'.MarkdownExtended::getConfig('abbr_word_re').')'.
				'(?![\w\x1A])'.
				'}', 
				array($this, '_callback'), $text);
		}
		return $text;
	}

	/**
	 * Process each abbreviation
	 *
	 * @param array $matches One set of results form the `doAbbreviations()` function
	 * @return string The abbreviation entry parsed
	 * @see hashPart()
	 * @see encodeAttribute()
	 */
	protected function _callback($matches) 
	{
		$abbr = $matches[0];
		$abbr_desciptions = MarkdownExtended::getConfig('abbr_desciptions');
		if (isset($abbr_desciptions[$abbr])) {
			$desc = $abbr_desciptions[$abbr];
			if (empty($desc)) {
				return parent::hashPart("<abbr>$abbr</abbr>");
			} else {
				$desc = parent::runGamut('tool:EncodeAttribute', $desc);
				return parent::hashPart("<abbr title=\"$desc\">$abbr</abbr>");
			}
		} else {
			return $matches[0];
		}
	}

	/**
	 * Strips abbreviations from text, stores titles in hash references.
	 *
	 * Link defs are in the form: [id]*: url "optional title"
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _stripAbbreviations_callback()
	 */
	public function strip($text) 
	{
		$less_than_tab = MarkdownExtended::getConfig('tab_width') - 1;
		return preg_replace_callback('{
				^[ ]{0,'.$less_than_tab.'}\*\[(.+?)\][ ]?:	# abbr_id = $1
				(.*)					# text = $2 (no blank lines allowed)	
			}xm',
			array($this, '_strip_callback'),
			$text);
	}

	/**
	 * Strips abbreviations from text, stores titles in hash references.
	 *
	 * @param array $matches Results from the `stripAbbreviations()` function
	 * @return string The text parsed
	 */
	protected function _strip_callback($matches) 
	{
		MarkdownExtended::addConfig('abbr_word_re', 
			(MarkdownExtended::getConfig('abbr_word_re') ? '|' : '' ).preg_quote($matches[1])
		);
		MarkdownExtended::addConfig('abbr_desciptions', array($matches[1] => trim($matches[2])));
		return ''; // String that will replace the block
	}
	
}

// Endfile