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
namespace MarkdownExtended\Grammar\Tool;

use \MarkdownExtended\MarkdownExtended,
    \MarkdownExtended\Grammar\Tool;

class EncodeEmailAddress extends Tool
{

	/**
	 *	Input: an email address, e.g. "foo@example.com"
	 *
	 *	Output: the email address as a mailto link, with each character
	 *		of the address encoded as either a decimal or hex entity, in
	 *		the hopes of foiling most address harvesting spam bots. E.g.:
	 *
	 *	  <p><a href="&#109;&#x61;&#105;&#x6c;&#116;&#x6f;&#58;&#x66;o&#111;
	 *        &#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;&#101;&#46;&#x63;&#111;
	 *        &#x6d;">&#x66;o&#111;&#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;
	 *        &#101;&#46;&#x63;&#111;&#x6d;</a></p>
	 *
	 *	Based by a filter by Matthew Wickline, posted to BBEdit-Talk.
	 *   With some optimizations by Milian Wolff.
	 *
	 * @param string $addr The email address to encode
	 * @return string The encoded address
	 */
	public function run($addr) 
	{
		$addr = "mailto:" . $addr;
		$chars = preg_split('/(?<!^)(?!$)/', $addr);
		$seed = (int)abs(crc32($addr) / strlen($addr)); // Deterministic seed.
		
		foreach ($chars as $key => $char) {
			$ord = ord($char);
			// Ignore non-ascii chars.
			if ($ord < 128) {
				$r = ($seed * (1 + $key)) % 100; // Pseudo-random function.
				// roughly 10% raw, 45% hex, 45% dec
				// '@' *must* be encoded. I insist.
				if ($r > 90 && $char != '@') /* do nothing */;
				else if ($r < 45) $chars[$key] = '&#x'.dechex($ord).';';
				else              $chars[$key] = '&#'.$ord.';';
			}
		}
		
		$addr = implode('', $chars);
		$text = implode('', array_slice($chars, 7)); // text without `mailto:`
		$addr = "<a href=\"$addr\">$text</a>";

		return $addr;
	}

}

// Endfile