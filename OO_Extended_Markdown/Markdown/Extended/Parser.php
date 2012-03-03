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
 * PHP Extended Markdown Parser Class
 */
class Markdown_Extended_Parser extends Markdown_Extended
{

	/**
	 * The global Markdown_Extended_Gamut instance
	 */
	protected $gamut;
	
	/**
	 * The global Markdown_Config instance
	 */
	protected $config;
	
	/**
	 * Predefined urls, titles and abbreviations for reference links and images.
	 */
	var $predef_urls = array();
	var $predef_titles = array();
	var $predef_attributes = array();
	var $predef_abbr = array();

// ----------------------------------
// CONSTRUCTORS
// ----------------------------------
	
	/**
	 * Constructor function. Initialize the parser object.
	 */
	public function __construct( $options=null ) 
	{
		// Init all dependencies
		Markdown_Extended::load( 'Markdown_Extended_Config' );
		Markdown_Extended::load( 'Markdown_Extended_Gamut' );
		Markdown_Extended::load( 'Markdown_Filter' );
		Markdown_Extended::load( 'Markdown_Tool' );
		$this->config = Markdown_Extended::get('Markdown_Extended_Config', MARKDOWN_CONFIGFILE);
		$this->gamut = Markdown_Extended::get('Markdown_Extended_Gamut', Markdown_Extended::getConfig('gamut_aliases'));

		// Init config
		Markdown_Extended::setConfig('nested_brackets_re', 
			str_repeat('(?>[^\[\]]+|\[', Markdown_Extended::getConfig('nested_brackets_depth')).
			str_repeat('\])*', Markdown_Extended::getConfig('nested_brackets_depth'))
		);	
		Markdown_Extended::setConfig('nested_url_parenthesis_re', 
			str_repeat('(?>[^()\s]+|\(', Markdown_Extended::getConfig('nested_url_parenthesis_depth')).
			str_repeat('(?>\)))*', Markdown_Extended::getConfig('nested_url_parenthesis_depth'))
		);		
		Markdown_Extended::setConfig('escape_chars_re', '['
			.preg_quote(Markdown_Extended::getConfig('escape_chars')).']');

		if (!empty($options))
		foreach($options as $_opt_name=>$_opt_value) {
			Markdown_Extended::setConfig($_opt_name, $_opt_value);
		}

		// Initial gamuts
		self::runGamuts('initial_gamut');
	}
	
	/**
	 * Setting up Extra-specific variables.
	 */
	protected function _setup() 
	{
		// Clear global hashes.
		Markdown_Extended::setVar('urls', $this->predef_urls);
		Markdown_Extended::setVar('titles', $this->predef_titles);
		Markdown_Extended::setVar('attributes', $this->predef_attributes);
		Markdown_Extended::setVar('predef_abbr', $this->predef_abbr);
		Markdown_Extended::setVar('html_hashes', array());

		// Launch all dependencies '_setup'
		$this->gamut->runGamutsMethod(self::getAllGamuts(), '_setup');
	}
	
	/**
	 * Clearing Extra-specific variables.
	 */
	protected function _teardown() 
	{
		// Clear global hashes.
		Markdown_Extended::setVar('urls', $this->predef_urls);
		Markdown_Extended::setVar('titles', $this->predef_titles);
		Markdown_Extended::setVar('attributes', $this->predef_attributes);
		Markdown_Extended::setVar('predef_abbr', $this->predef_abbr);
		Markdown_Extended::setVar('html_hashes', array());

		// Launch all dependencies '_teardown'
		$this->gamut->runGamutsMethod(self::getAllGamuts(), '_teardown');
	}
	
	public function getAllGamuts()
	{
		return array_merge(
			Markdown_Extended::getConfig('initial_gamut'),
			Markdown_Extended::getConfig('transform_gamut'),
			Markdown_Extended::getConfig('document_gamut'),
			Markdown_Extended::getConfig('span_gamut'),
			Markdown_Extended::getConfig('block_gamut')
		);
	}

// ----------------------------------
// USER INTERFACE
// ----------------------------------
	
	/**
	 * Main function. Performs some preprocessing on the input text
	 * and pass it through the document gamut.
	 *
	 * @param string $text The text to be parsed
	 * @return string The text parsed
	 * @see detab()
	 * @see hashHTMLBlocks()
	 * @see teardown()
	 * @see $document_gamut
	 */
	public function transform($text) 
	{
		self::_setup();
	
		// Run first transform gamut methods
		$text = self::runGamuts('transform_gamut', $text);

		// If 'special_gamut', run only this
		$special_gamut = Markdown_Extended::getConfig('special_gamut');
		if (!empty($special_gamut)) {

			$text = self::runGamuts('special_gamut', $text);
		} else {

			// Run document gamut methods
			$text = self::runGamuts('document_gamut', $text);
		}

		self::_teardown();
		return $text . "\n";
	}

	/**
	 * Call to Markdown_Extended_Gamut
	 */
	public function runGamuts( $gamuts, $text=null )
	{
		if (empty($gamuts)) return $text;

		if (is_string($gamuts)) {
			$gamuts = Markdown_Extended::getConfig( $gamuts );
			if (empty($gamuts) || !is_array($gamuts)) {
				throw new UnexpectedValueException(sprintf(
  	  		"Called gamut table can't be found, get <%s>!", $gamuts
	  	  ));
			}
		}

		if (!empty($gamuts) && is_array($gamuts))
			return $this->gamut->runGamuts($gamuts, $text);
		else
			return $text;
	}

}

// Endfile
