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
namespace MarkdownExtended;

use \UnexpectedValueException;

/**
 * PHP Extended Markdown Parser Class
 */
class Parser
{

	/**
	 * Original content
	 * @var string
	 */
    protected $source_content;

	/**
	 * Final content
	 * @var string
	 */
    protected $parsed_content;

	/**
	 * Predefined urls, titles and abbreviations for reference links and images.
	 */
	public $predef_urls = array();
	public $predef_titles = array();
	public $predef_attributes = array();
	public $predef_abbr = array();

	/**
	 * Internal hashes used during transformation.
	 */
	protected $urls = array();
	protected $titles = array();
	protected $attributes = array();
	protected $ids = array();

    /**
     * @var array
     */
	protected $all_gamuts;

    /**
     * @static array
     */
    public static $definable = array(
        'predef_urls', 'predef_titles', 'predef_attributes', 'predef_abbr'
     );

// ----------------------------------
// CONSTRUCTORS
// ----------------------------------
	
	/**
	 * Constructor function: Initialize the parser object
	 *
	 * The `$config` arguments accept both a string (a config INI file path) or an array
	 * if you want to override config options ; in this case, you can set a config file path
	 * with the `config_file` index.
	 *
	 * @param array|string $config 
	 */
	public function __construct($config = null) 
	{
	    $config_file = MarkdownExtended::MARKDOWN_CONFIGFILE;
	    if (!empty($config)) {
	        if (is_string($config)) {
        	    $config_file = $config;
	        } elseif (is_array($config)) {
	            if (isset($config['config_file'])) {
            	    $config_file = $config['config_file'];
            	    unset($config['config_file']);
	            }
	        }
	    }

		// Init all dependencies
		MarkdownExtended::factory('\MarkdownExtended\ConfigFile', $config_file);
		MarkdownExtended::factory('\MarkdownExtended\Gamut', MarkdownExtended::getConfig('gamut_aliases'));
		MarkdownExtended::load('\MarkdownExtended\Grammar\Filter');
		MarkdownExtended::load('\MarkdownExtended\Grammar\Tool');

		// Init config
		MarkdownExtended::setConfig('nested_brackets_re', 
			str_repeat('(?>[^\[\]]+|\[', MarkdownExtended::getConfig('nested_brackets_depth')).
			str_repeat('\])*', MarkdownExtended::getConfig('nested_brackets_depth'))
		);	
		MarkdownExtended::setConfig('nested_url_parenthesis_re', 
			str_repeat('(?>[^()\s]+|\(', MarkdownExtended::getConfig('nested_url_parenthesis_depth')).
			str_repeat('(?>\)))*', MarkdownExtended::getConfig('nested_url_parenthesis_depth'))
		);		
		MarkdownExtended::setConfig('escape_chars_re', '['
			.preg_quote(MarkdownExtended::getConfig('escape_chars')).']');

		if (!empty($config) && is_array($config)) {
			foreach ($config as $_opt_name=>$_opt_value) {
			    if (in_array($_opt_name, self::$definable)) {
			        if (is_array($_opt_value)) {
    			        $this->{$_opt_name} = array_merge($this->{$_opt_name}, $_opt_value);
    			    }
			    } else {
    				MarkdownExtended::setConfig($_opt_name, $_opt_value);
    			}
			}
		}
		// Initial gamuts
		$this->runGamuts('initial_gamut');
	}

// ----------------------------------
// GAMUTS
// ----------------------------------
	
    /**
     * Get the global Gamut object
     */	
	public function getGamut()
	{
		return MarkdownExtended::get('\MarkdownExtended\Gamut');
	}
	
	/**
	 * Setting up Extra-specific variables.
	 */
	protected function _setup() 
	{
        $this->source_content = null;
        $this->parsed_content = null;

		// Clear global hashes.
		MarkdownExtended::setVar('cross_references', array());
		MarkdownExtended::setVar('urls', $this->predef_urls);
		MarkdownExtended::setVar('titles', $this->predef_titles);
		MarkdownExtended::setVar('attributes', $this->predef_attributes);
		MarkdownExtended::setVar('predef_abbr', $this->predef_abbr);
		MarkdownExtended::setVar('html_hashes', array());

		// Launch all dependencies '_setup'
		$this->getGamut()->runGamutsMethod($this->getAllGamuts(), '_setup');
	}
	
	/**
	 * Clearing Extra-specific variables.
	 */
	protected function _teardown() 
	{
		// Clear global hashes.
		MarkdownExtended::setVar('urls', $this->predef_urls);
		MarkdownExtended::setVar('titles', $this->predef_titles);
		MarkdownExtended::setVar('attributes', $this->predef_attributes);
		MarkdownExtended::setVar('predef_abbr', $this->predef_abbr);
		MarkdownExtended::setVar('html_hashes', array());

		// Launch all dependencies '_teardown'
		$this->getGamut()->runGamutsMethod($this->getAllGamuts(), '_teardown');
	}
	
	/**
	 * @return array
	 */
	public function getAllGamuts()
	{
	    if (empty($this->all_gamuts)) {
            $this->all_gamuts = array();
            $full_gamuts = array_merge(
                MarkdownExtended::getConfig('initial_gamut'),
                MarkdownExtended::getConfig('transform_gamut'),
                MarkdownExtended::getConfig('document_gamut'),
                MarkdownExtended::getConfig('span_gamut'),
                MarkdownExtended::getConfig('block_gamut')
            );
            foreach ($full_gamuts as $name=>$p) {
                @list($type, $class, $method) = explode(':', $name);
                if (!empty($class)) {
                    $newgamut_name = $type.':'.$class;
                    if (!array_key_exists($newgamut_name, $this->all_gamuts)) {
                        $this->all_gamuts[$newgamut_name] = $p;
                    }
                }
            }
        }
        return $this->all_gamuts;
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
		$this->_setup();
        $this->source_content = $text;

		// Run first transform gamut methods
		$text = $this->runGamuts('transform_gamut', $text);

		// If 'special_gamut', run only this
		$special_gamut = MarkdownExtended::getConfig('special_gamut');
		if (!empty($special_gamut)) {
			$text = $this->runGamuts('special_gamut', $text);
		} else {
		// Else run document gamut methods
			$text = $this->runGamuts('document_gamut', $text);
		}

        $this->parsed_content = $text . "\n";
/*
$data = array(
//    'original'=>$this->source_content,
//    'parsed'=>$this->parsed_content,
    'menu'=>MarkdownExtended::getVar('menu'),
    'urls'=>MarkdownExtended::getVar('urls'),
	'metadata'=>MarkdownExtended::getVar('metadata'),
	'footnotes'=>MarkdownExtended::getVar('footnotes'),
	'glossaries'=>MarkdownExtended::getVar('glossaries'),
	'citations'=>MarkdownExtended::getVar('citations'),
);

echo '<pre>';
var_export($data);
echo '</pre>';
//exit('yo');
*/
//        MarkdownExtended::addProcessedContent($this);
		$this->_teardown();
		return $this->parsed_content;
	}

	/**
	 * Call to MarkdownExtended_Gamut
	 */
	public function runGamuts($gamuts, $text = null)
	{
		if (empty($gamuts)) return $text;

		if (is_string($gamuts)) {
			$gamuts = MarkdownExtended::getConfig($gamuts);
			if (empty($gamuts) || !is_array($gamuts)) {
				throw new UnexpectedValueException(sprintf(
  	  				"Called gamut table can't be found, get <%s>!", $gamuts
	  	  		));
	  	  	}
		}

		if (!empty($gamuts) && is_array($gamuts)) {
			return $this->getGamut()->runGamuts($gamuts, $text);
		}
		return $text;
	}

}

// Endfile
