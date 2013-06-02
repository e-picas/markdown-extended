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

class AutoLink extends Filter
{
	
	/**
	 * @param string $text Text to parse
	 * @return string The text parsed
	 * @see _doAutoLinks_url_callback()
	 * @see _doAutoLinks_email_callback()
	 */
	public function transform($text) 
	{
		$text = preg_replace_callback('{<((https?|ftp|dict):[^\'">\s]+)>}i', 
			array($this, '_url_callback'), $text);

		// Email addresses: <address@domain.foo>
		return preg_replace_callback('{
			<
			(?:mailto:)?
			(
				(?:
					[-!#$%&\'*+/=?^_`.{|}~\w\x80-\xFF]+
				|
					".*?"
				)
				\@
				(?:
					[-a-z0-9\x80-\xFF]+(\.[-a-z0-9\x80-\xFF]+)*\.[a-z]+
				|
					\[[\d.a-fA-F:]+\]	# IPv4 & IPv6
				)
			)
			>
			}xi',
			array($this, '_email_callback'), $text);
	}

	/**
	 * @param array $matches A set of results of the `doAutoLinks` function
	 * @return string The text parsed
	 * @see encodeAttribute()
	 * @see hashPart()
	 */
	protected function _url_callback($matches) 
	{
		$url = parent::runGamut('tool:EncodeAttribute', $matches[1]);
		$link = "<a href=\"$url\">$url</a>";
		return parent::hashPart($link);
	}

	/**
	 * @param array $matches A set of results of the `doAutoLinks` function
	 * @return string The text parsed
	 * @see encodeEmailAddress()
	 * @see hashPart()
	 */
	protected function _email_callback($matches) 
	{
		$address = $matches[1];
		$link = parent::runGamut('tool:EncodeEmailAddress', $address);
		return parent::hashPart($link);
	}

}

// Endfile