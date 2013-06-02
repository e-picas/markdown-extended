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

class Blockquote extends Filter
{
	
	/**
	 * Create blockquotes blocks
	 *
	 * @param string $text Text to parse
	 * @return string The text parsed
	 * @see _doBlockQuotes_callback()
	 */
	public function transform($text) 
	{
		return preg_replace_callback('/
			  (								# Wrap whole match in $1
				(?>
				  ^[ ]*>[ ]?		# ">" at the start of a line
					(?:\((.+?)\))?
					.+\n					# rest of the first line
				  (.+\n)*				# subsequent consecutive lines
				  \n*						# blanks
				)+
			  )
			/xm',
			array($this, '_callback'), $text);
	}

	/**
	 * Build each blockquote block
	 *
	 * @param array $matches A set of results of the `doBlockQuotes()` function
	 * @return string The text parsed
	 * @see html_block_gamut()
	 * @see _doBlockQuotes_callback2()
	 */
	protected function _callback($matches) 
	{
		$bq = $matches[1];
		$cite = $matches[2];
		// trim one level of quoting - trim whitespace-only lines
		$bq = preg_replace('/^[ ]*>[ ]?(\((.+?)\))?|^[ ]+$/m', '', $bq);
		$bq = parent::runGamut('html_block_gamut', $bq);		# recurse

		$bq = preg_replace('/^/m', "  ", $bq);
		// These leading spaces cause problem with <pre> content, 
		// so we need to fix that:
		$bq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx', array($this, '_callback2'), $bq);

		return "\n". parent::hashBlock("<blockquote"
			.( !empty($cite) ? " cite=\"$cite\"" : '' )
			.">\n$bq\n</blockquote>")."\n\n";
	}

	/**
	 * Deletes the last sapces, for <pre> blocks
	 *
	 * @param array $matches A set of results of the `_doBlockQuotes_callback()` function
	 * @return string The text parsed
	 */
	protected function _callback2($matches) 
	{
		$pre = $matches[1];
		$pre = preg_replace('/^  /m', '', $pre);
		return $pre;
	}

}

// Endfile