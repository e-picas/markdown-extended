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
class Markdown_Filter_Image extends Markdown_Filter
{

	/**
	 * Turn Markdown image shortcuts into <img> tags.
	 *
	 * @param string $text Text to parse
	 * @return string The text parsed
	 * @see _doImages_reference_callback()
	 * @see _doImages_inline_callback()
	 */
	public function transform($text) 
	{
		// First, handle reference-style labeled images: ![alt text][id]
		$text = preg_replace_callback('{
			(				                            # wrap whole match in $1
			  !\[
				('.Markdown_Extended::getConfig('nested_brackets_re').')		# alt text = $2
			  \]

			  [ ]?				                      # one optional space
			  (?:\n[ ]*)?		                    # one optional newline followed by spaces

			  \[
				(.*?)		                          # id = $3
			  \]

			)
			}xs', 
			array(&$this, '_reference_callback'), $text);

		// Next, handle inline images:  ![alt text](url "optional title")
		// Don't forget: encode * and _
		$text = preg_replace_callback('{
			(				                                  # wrap whole match in $1
			  !\[
				('.Markdown_Extended::getConfig('nested_brackets_re').')		      # alt text = $2
			  \]
			  \s?			                                # One optional whitespace character
			  \(			                                # literal paren
				[ \n]*
				(?:
					<(\S*)>	# src url = $3
				|
					('.Markdown_Extended::getConfig('nested_url_parenthesis_re').')	# src url = $4
				)
				[ \n]*
				(			                                  # $5
				  ([\'"])	                              # quote char = $6
				  (.*?)		                              # title = $7
				  \6		                                # matching quote
				  [ \n]*
				)?			                                # title is optional
			  \)
			)
			}xs',
			array(&$this, '_inline_callback'), $text);

		return $text;
	}

	/**
	 * @param array $matches A set of results of the `deImages` function
	 * @return string The text parsed
	 * @see encodeAttribute()
	 * @see hashPart()
	 */
	protected function _reference_callback($matches) 
	{
		$whole_match = $matches[1];
		$alt_text    = $matches[2];
		$link_id     = strtolower($matches[3]);

		if ($link_id == "") {
			$link_id = strtolower($alt_text); // for shortcut links like ![this][].
		}

		$urls = Markdown_Extended::getVar('urls');
		$titles = Markdown_Extended::getVar('titles');
		$attributes = Markdown_Extended::getVar('attributes');
		$alt_text = parent::runGamut('tool:EncodeAttribute', $alt_text);
		if (isset($urls[$link_id])) {
			$url = parent::runGamut('tool:EncodeAttribute', $urls[$link_id]);
			$result = "<img src=\"$url\" alt=\"$alt_text\"";
			if (isset($titles[$link_id])) {
				$title = $titles[$link_id];
				$title = parent::runGamut('tool:EncodeAttribute', $title);
				$result .=  " title=\"$title\"";
			}
			if (isset($attributes[$link_id])) {
				$result .= parent::runGamut('tool:RebuildAttribute', $attributes[$link_id] );
			}
			$result .= Markdown_Extended::getConfig('empty_element_suffix');
			$result = parent::hashPart($result);
		}
		else {
			// If there's no such link ID, leave intact:
			$result = $whole_match;
		}

		return $result;
	}

	/**
	 * @param array $matches A set of results of the `doImages` function
	 * @return string The text parsed
	 * @see encodeAttribute()
	 * @see hashPart()
	 */
	protected function _inline_callback($matches) 
	{
		$whole_match	= $matches[1];
		$alt_text		  = $matches[2];
		$url			    = $matches[3] == '' ? $matches[4] : $matches[3];
		$title			  =& $matches[7];

		$alt_text = parent::runGamut('tool:EncodeAttribute', $alt_text);
		$url = parent::runGamut('tool:EncodeAttribute', $url);
		$result = "<img src=\"$url\" alt=\"$alt_text\"";
		if (isset($title)) {
			$title = parent::runGamut('tool:EncodeAttribute', $title);
			$result .=  " title=\"$title\""; # $title already quoted
		}
		$result .= Markdown_Extended::getConfig('empty_element_suffix');

		return parent::hashPart($result);
	}

}

// Endfile