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
 */

/**
 * PHP Extended Markdown OutputFormat interface
 */
interface PHP_Extended_Markdown_OutputFormat
{

	public function render();


// -----------------------------------
// ABBREVIATIONS
// -----------------------------------

	/**
	 * @param string $text The abbreviation text
	 * @param array $attrs The abbreviation attributes if so
	 * @return string The abbreviation tag string
	 */
	public static function buildAbbreviation( $text, $attrs=array() );

// -----------------------------------
// ANCHORS
// -----------------------------------

	/**
	 * @param string $text The anchor text content
	 * @param array $attrs The anchor attributes if so
	 * @return string The anchor tag string
	 */
	public static function buildAnchor( $text, $attrs=array() );

// -----------------------------------
// AUTOMATIC LINKS
// -----------------------------------

	/**
	 * @param string $text The link text content
	 * @param array $attrs The link attributes if so
	 * @return string The link tag string
	 */
	public static function buildLink( $text, $attrs=array() );

	/**
	 * @param string $text The mailto link address
	 * @param array $attrs The mailto link attributes if so
	 * @return string The mailto link tag string (transformed 
	 */
	public static function buildMailto( $address, $attrs=array() );

// -----------------------------------
// BLOCKQUOTES
// -----------------------------------

	/**
	 * @param string $text The blockquote text content
	 * @param array $attrs The blockquote attributes if so
	 * @return string The blockquote tag string
	 */
	public static function buildBlockquote( $text, $attrs=array() );

// -----------------------------------
// CODE BLOCKS
// -----------------------------------

	/**
	 * @param string $text The code block text content
	 * @param array $attrs The code block attributes if so
	 * @return string The code block tag string
	 */
	public static function buildCodeBlock( $text, $attrs=array() );

	/**
	 * @param string $text The code span text content
	 * @param array $attrs The code span attributes if so
	 * @return string The code span tag string
	 */
	public static function buildCodeSpan( $text, $attrs=array() );

// -----------------------------------
// DEFINITIONS LISTS
// -----------------------------------

	/**
	 * @param string $text The definition list text content
	 * @param array $attrs The definition list attributes if so
	 * @return string The definition list tag string
	 */
	public static function buildDefinitionList( $text, $attrs=array() );

	/**
	 * @param string $text The definition term text content
	 * @param array $attrs The definition term attributes if so
	 * @return string The definition term tag string
	 */
	public static function buildDefinitionTerm( $text, $attrs=array() );

	/**
	 * @param string $text The definition description text content
	 * @param array $attrs The definition description attributes if so
	 * @return string The definition description tag string
	 */
	public static function buildDefinitionDescription( $text, $attrs=array() );

// -----------------------------------
// EMPHASIS
// -----------------------------------

	/**
	 * @param string $type The emphasis type : 'em', 'strong' or 'both'
	 * @param string $text The emphasis text
	 * @param array $attrs The emphasis attributes if so
	 * @return string The emphasis tag string
	 */
	public static function buildEmphasis( $type, $text, $attrs=array() );

// -----------------------------------
// HEADERS
// -----------------------------------

	/**
	 * @param string $text The header content (title)
	 * @param int $level The header level
	 * @param string $attrs The attributes array of the built header tag
	 * @return string The header tag string
	 */
	public static function buildHeader( $text, $level=1, $attrs=array() );

// -----------------------------------
// IMAGES
// -----------------------------------

	/**
	 * @param array $attrs The image attributes
	 * @return string The image tag string
	 */
	public static function buildImage( $attrs=array() );

// -----------------------------------
// LISTS
// -----------------------------------

	/**
	 * @param string $text The unordered list content
	 * @param array $attrs The unordered list attributes if so
	 * @return string The unordered list tag string
	 */
	public static function buildUnorderedList( $text, $attrs=array() );

	/**
	 * @param string $text The ordered list content
	 * @param array $attrs The ordered list attributes if so
	 * @return string The ordered list tag string
	 */
	public static function buildOrderedList( $text, $attrs=array() );

	/**
	 * @param string $text The list item content
	 * @param array $attrs The list item attributes if so
	 * @return string The list item tag string
	 */
	public static function buildListItem( $text, $attrs=array() );

// -----------------------------------
// NOTES
// -----------------------------------

	/**
	 * @param string $text The sup text content
	 * @param array $attrs The sup attributes if so
	 * @return string The sup tag string
	 */
	public static function buildSup( $text, $attrs=array() );

	/**
	 * @param string $text The span text content
	 * @param array $attrs The span attributes if so
	 * @return string The span tag string
	 */
	public static function buildSpan( $text, $attrs=array() );

	/**
	 * @param string $text The div text content
	 * @param array $attrs The div attributes if so
	 * @return string The div tag string
	 */
	public static function buildDiv( $text, $attrs=array() );

// -----------------------------------
// PARAGRAPHS
// -----------------------------------

	/**
	 * @param string $text The paragraph text content
	 * @param array $attrs The paragraph attributes if so
	 * @return string The paragraph tag string
	 */
	public static function buildParagraph( $text, $attrs=array() );


}

// Endfile
