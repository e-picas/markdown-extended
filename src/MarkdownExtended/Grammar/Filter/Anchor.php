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

class Anchor extends Filter
{
	
	public function _setup()
	{
		MarkdownExtended::setVar('in_anchor', false);
	}
	
	/**
	 * Turn Markdown link shortcuts into XHTML <a> tags.
	 *
	 * @param string $text Text to parse
	 * @return string The text parsed
	 * @see _doAnchors_reference_callback()
	 * @see _doAnchors_inline_callback()
	 * @see _doAnchors_reference_callback()
	 */
	public function transform($text) 
	{
		if (MarkdownExtended::getVar('in_anchor')==true) return $text;
		MarkdownExtended::setVar('in_anchor', true);
		
		// First, handle reference-style links: [link text] [id]
		$text = preg_replace_callback('{
			(					                        # wrap whole match in $1
			  \[
				('.MarkdownExtended::getConfig('nested_brackets_re').')	# link text = $2
			  \]

			  [ ]?				                    # one optional space
			  (?:\n[ ]*)?		                  # one optional newline followed by spaces

			  \[
				(.*?)		                        # id = $3
			  \]
			)
			}xs',
			array($this, '_reference_callback'), $text);

		// Next, inline-style links: [link text](url "optional title")
		$text = preg_replace_callback('{
			(				                                    # wrap whole match in $1
			  \[
				('.MarkdownExtended::getConfig('nested_brackets_re').')	          # link text = $2
			  \]
			  \(			                                  # literal paren
				[ \n]*
				(?:
					<(.+?)>	                                # href = $3
				|
					('.MarkdownExtended::getConfig('nested_url_parenthesis_re').')	# href = $4
				)
				[ \n]*
				(			                                    # $5
				  ([\'"])	                                # quote char = $6
				  (.*?)		                                # Title = $7
				  \6		                                  # matching quote
				  [ \n]*	                                # ignore any spaces/tabs between closing quote and )
				)?			                                  # title is optional
			  \)
			)
			}xs',
			array($this, '_inline_callback'), $text);

		// Last, handle reference-style shortcuts: [link text]
		// These must come last in case you've also got [link text][1]
		// or [link text](/foo)
		$text = preg_replace_callback('{
			(					      # wrap whole match in $1
			  \[
				([^\[\]]+)		# link text = $2; can\'t contain [ or ]
			  \]
			)
			}xs',
			array($this, '_reference_callback'), $text);

		MarkdownExtended::setVar('in_anchor', false);
		return $text;
	}

	/**
	 * @param array $matches A set of results of the `doAnchors` function
	 * @return string The text parsed
	 * @see encodeAttribute()
	 * @see span_gamut()
	 * @see hashPart()
	 */
	protected function _reference_callback($matches) 
	{
		$whole_match =  $matches[1];
		$link_text   =  $matches[2];
		$link_id     =& $matches[3];

		if ($link_id == "") {
			// for shortcut links like [this][] or [this].
			$link_id = $link_text;
		}
		
		// lower-case and turn embedded newlines into spaces
		$link_id = strtolower($link_id);
		$link_id = preg_replace('{[ ]?\n}', ' ', $link_id);

		$urls = MarkdownExtended::getVar('urls');
		$titles = MarkdownExtended::getVar('titles');
		$attributes = MarkdownExtended::getVar('attributes');
		if (isset($urls[$link_id])) {
			$url = $urls[$link_id];
			$url = parent::runGamut('tool:EncodeAttribute', $url);
			$result = "<a href=\"$url\"";
			if ( isset( $titles[$link_id] ) ) {
				$title = $titles[$link_id];
				$title = parent::runGamut('tool:EncodeAttribute', $title);
				$result .=  " title=\"$title\"";
			}
			if (isset($attributes[$link_id])) {
				$result .= parent::runGamut('tool:RebuildAttribute', $attributes[$link_id] );
			}
			$link_text = parent::runGamut('span_gamut', $link_text);
			$result .= ">$link_text</a>";
			$result = parent::hashPart($result);
		}
		else {
			$result = $whole_match;
		}
		return $result;
	}

	/**
	 * @param array $matches A set of results of the `doAnchors` function
	 * @return string The text parsed
	 * @see encodeAttribute()
	 * @see span_gamut()
	 * @see hashPart()
	 */
	protected function _inline_callback($matches) 
	{
		$whole_match	=  $matches[1];
		$link_text		=  parent::runGamut('span_gamut', $matches[2]);
		$url		    =  $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];
		$url = parent::runGamut('tool:EncodeAttribute', $url);
		$result = "<a href=\"$url\"";
		if (isset($title)) {
			$title = parent::runGamut('tool:EncodeAttribute', $title);
			$result .=  " title=\"$title\"";
		}
		$link_text = parent::runGamut('span_gamut', $link_text);
		$result .= ">$link_text</a>";
		return parent::hashPart($result);
	}

}

// Endfile