<?php
/**
 * PHP Extended Markdown
 * Copyright (c) 2012 Pierre Cassat
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
 *
 * @package 	PHP_Extended_Markdown
 * @license   	BSD
 * @link      	https://github.com/PieroWbmstr/Extended_Markdown
 * @subpackage 	PHP_Extended_Markdown_OutputFormat
 */

/**
 */
class PHP_Extended_Markdown_OutputFormat_HTML
	implements PHP_Extended_Markdown_OutputFormat
{

	public function render()
	{
	}

	/**
	 * Builder of HTML tags :
	 *     <TAG ATTR1="ATTR_VAL1" ... > TEXT </TAG>
	 *
	 * @param string $text The content of the tag
	 * @param string $tag The tag name
	 * @param array $attributes An array of attributes constructed by "variable=>value" pairs
	 * @param bool $close Is it a closed tag ? (FALSE by default)
	 * @return string The built tag string
	 */
	public static function buildTag( $text, $tag, $attributes=array(), $close=false )
	{
		$attr='';
		if (!empty($attributes))
		{
			foreach($attributes as $variable=>$value)
			{
				if (!empty($value))
				{
					if (is_string($variable))
					{
						$attr .= ' '.$variable.'="'.$value.'"';
					}
					else
					{
						$attr .= ' '.trim($value);
					}
				}
			}
		}
		if (true===$close)
		{
			return '<'.$tag.$attr.' />';
		}
		else
		{
			return '<'.$tag.$attr.'>'.$text.'</'.$tag.'>';
		}
	}

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
	 * @param bool $return_array May we return an array like ( 'mailto:link' , 'link' ) for display ? (FALSE by default)
	 * @return string|array The encoded address, and the address to display if $return_array is TRUE
	 */
	public static function encodeEmailAddress( $addr, $return_array=false ) 
	{
		$addr = 'mailto:' . $addr;
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

		if (true===$return_array) {
			return array(
				implode('', $chars), // full 'mailto:...' encoded string
				implode('', array_slice($chars, 7)) // text without `mailto:`
			);
		} else {
			return implode('', $chars);
		}		
	}

	/**
	 * @param string $text The mailto link address
	 * @param array $attrs The mailto link attributes if so
	 * @return string The mailto link tag string (transformed 
	 */
	public static function buildMailto( $address, $attrs=array() )
	{
		list($encoded_address, $displayable_address) = self::encodeEmailAddress( $address, true );
		$attrs['href'] = $encoded_address;
		return self::buildLink( $displayable_address, $attrs );
	}

	/**
	 * @param string $text The abbreviation text
	 * @param array $attrs The abbreviation attributes if so
	 * @return string The abbreviation tag string
	 */
	public static function buildAbbreviation( $text, $attrs=array() ) 
	{
		return self::buildTag( $text, 'abbr', $attrs );
	}

	/**
	 * @param string $text The anchor text content
	 * @param array $attrs The anchor attributes if so
	 * @return string The anchor tag string
	 */
	public static function buildAnchor( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'a', $attrs );
	}
	
	/**
	 * @param string $text The link text content
	 * @param array $attrs The link attributes if so
	 * @return string The link tag string
	 */
	public static function buildLink( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'a', $attrs );
	}
	
	/**
	 * @param string $text The blockquote text content
	 * @param array $attrs The blockquote attributes if so
	 * @return string The blockquote tag string
	 */
	public static function buildBlockquote( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'blockquote', $attrs );
	}

	/**
	 * @param string $text The code block text content
	 * @param array $attrs The code block attributes if so
	 * @return string The code block tag string
	 */
	public static function buildCodeBlock( $text, $attrs=array() )
	{
		return self::buildTag( 
			self::buildTag( $text, 'code', $attrs ), 
			'pre' );
	}

	/**
	 * @param string $text The code span text content
	 * @param array $attrs The code span attributes if so
	 * @return string The code span tag string
	 */
	public static function buildCodeSpan( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'code', $attrs );
	}

	/**
	 * @param string $text The definition list text content
	 * @param array $attrs The definition list attributes if so
	 * @return string The definition list tag string
	 */
	public static function buildDefinitionList( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'dl', $attrs );
	}

	/**
	 * @param string $text The definition term text content
	 * @param array $attrs The definition term attributes if so
	 * @return string The definition term tag string
	 */
	public static function buildDefinitionTerm( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'dt', $attrs );
	}

	/**
	 * @param string $text The definition description text content
	 * @param array $attrs The definition description attributes if so
	 * @return string The definition description tag string
	 */
	public static function buildDefinitionDescription( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'dd', $attrs );
	}

	/**
	 * @param string $type The emphasis type : 'em', 'strong' or 'both'
	 * @param string $text The emphasis text
	 * @param array $attrs The emphasis attributes if so
	 * @return string The emphasis tag string
	 */
	public static function buildEmphasis( $type, $text, $attrs=array() )
	{
		if ($type=='both')
		{
			return self::buildTag( 
				self::buildTag( $text, 'strong' ), 
				'em', $attrs );
		} 
		else 
		{
			return self::buildTag( $text, $type, $attrs );
		}
	}

	/**
	 * @param string $text The header content (title)
	 * @param int $level The header level
	 * @param string $attrs The attributes array of the built header tag
	 * @return string The header tag string
	 */
	public static function buildHeader( $text, $level=1, $attrs=array() )
	{
		return self::buildTag( $text, 'h'.$level, $attrs );
	}

	/**
	 * @param array $attrs The image attributes
	 * @return string The image tag string
	 */
	public static function buildImage( $attrs=array() )
	{
		return self::buildTag( '', 'img', $attrs, true );
	}

	/**
	 * @param string $text The paragraph text content
	 * @param array $attrs The paragraph attributes if so
	 * @return string The paragraph tag string
	 */
	public static function buildParagraph( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'p', $attrs );
	}

	/**
	 * @param string $text The unordered list content
	 * @param array $attrs The unordered list attributes if so
	 * @return string The unordered list tag string
	 */
	public static function buildUnorderedList( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'ul', $attrs );
	}

	/**
	 * @param string $text The ordered list content
	 * @param array $attrs The ordered list attributes if so
	 * @return string The ordered list tag string
	 */
	public static function buildOrderedList( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'ol', $attrs );
	}

	/**
	 * @param string $text The list item content
	 * @param array $attrs The list item attributes if so
	 * @return string The list item tag string
	 */
	public static function buildListItem( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'li', $attrs );
	}

	/**
	 * @param string $text The sup text content
	 * @param array $attrs The sup attributes if so
	 * @return string The sup tag string
	 */
	public static function buildSup( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'sup', $attrs );
	}

	/**
	 * @param string $text The span text content
	 * @param array $attrs The span attributes if so
	 * @return string The span tag string
	 */
	public static function buildSpan( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'span', $attrs );
	}
	
	/**
	 * @param string $text The div text content
	 * @param array $attrs The div attributes if so
	 * @return string The div tag string
	 */
	public static function buildDiv( $text, $attrs=array() )
	{
		return self::buildTag( $text, 'div', $attrs );
	}

}

// Endfile