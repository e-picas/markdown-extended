<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MarkdownExtended;

use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;
use \MarkdownExtended\API as MDE_API;

/**
 * PHP Markdown Extended Parser Class
 * @package MarkdownExtended
 */
class Parser
    implements MDE_API\ParserInterface
{

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
     * @param   array/string    $config
     * @throws  \MarkdownExtended\Exception\InvalidArgumentException if a class load fails
     * @throws  \MarkdownExtended\Exception\RuntimeException if an object creation sent an error
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException it the creation of an object throws an exception
     */
    public function __construct($config = null) 
    {
        // Init all dependencies
        try {
            MarkdownExtended::get('Config')->init($config);
            MarkdownExtended::factory('Grammar\Gamut', array(MarkdownExtended::getConfig('gamut_aliases')));
            MarkdownExtended::load('Grammar\Filter');
            MarkdownExtended::load('Grammar\Tool');
            MarkdownExtended::get('OutputFormatBag')->load(MarkdownExtended::getConfig('output_format'));
        } catch (MDE_Exception\InvalidArgumentException $e) {
            throw $e;
        } catch (MDE_Exception\UnexpectedValueException $e) {
            throw $e;
        } catch (MDE_Exception\RuntimeException $e) {
            throw $e;
        }

        // Init config
        MarkdownExtended::setConfig('nested_brackets_re', 
            str_repeat('(?>[^\[\]]+|\[', MarkdownExtended::getConfig('nested_brackets_depth')).
            str_repeat('\])*', MarkdownExtended::getConfig('nested_brackets_depth'))
        );  
        MarkdownExtended::setConfig('nested_url_parenthesis_re', 
            str_repeat('(?>[^()\s]+|\(', MarkdownExtended::getConfig('nested_url_parenthesis_depth')).
            str_repeat('(?>\)))*', MarkdownExtended::getConfig('nested_url_parenthesis_depth'))
        );      
        MarkdownExtended::setConfig('escape_chars_re', 
            '['
            .preg_quote(MarkdownExtended::getConfig('escape_chars'))
            .']');
        MarkdownExtended::setConfig('less_than_tab',
            (MarkdownExtended::getConfig('tab_width') - 1));

        // Initial gamuts
        $this->runGamuts('initial_gamut');
    }

// ----------------------------------
// PARSER
// ----------------------------------
    
    /**
     * Main function. Performs some pre-processing on the input text
     * and pass it through the document gamut.
     *
     * @param   \MarkdownExtended\API\ContentInterface   $content
     * @param   bool    $secondary
     * @return  \MarkdownExtended\MarkdownExtended
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if a gamut run fails
     * @see     self::_setup()
     * @see     self::_teardown()
     */
    public function parse(MDE_API\ContentInterface $content, $secondary = false)
    {
        MarkdownExtended::addProcessedContent($content, $secondary);
        $this->_setup();
        $text = $content->getSource();

        try {
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
        } catch (MDE_Exception\UnexpectedValueException $e) {
            throw $e;
        }

        $content->setBody($text . "\n");
        $this->_teardown();
        
        return MarkdownExtended::getInstance();
    }

// ----------------------------------
// GAMUTS
// ----------------------------------
    
    /**
     * Call to MarkdownExtended\Grammar\Gamut for an array of gamuts
     *
     * @param   array   $gamuts
     * @param   string  $text
     * @return  string
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if gamuts table not found
     */
    public function runGamuts($gamuts, $text = null)
    {
        if (empty($gamuts)) return $text;

        if (is_string($gamuts)) {
            $gamuts = MarkdownExtended::getConfig($gamuts);
            if (empty($gamuts) || !is_array($gamuts)) {
                throw new MDE_Exception\UnexpectedValueException(sprintf(
                    "Called gamut table can't be found, get <%s>!", $gamuts
                ));
            }
        }

        if (!empty($gamuts) && is_array($gamuts)) {
            return MarkdownExtended::get('Grammar\Gamut')
                ->runGamuts($gamuts, $text);
        }
        return $text;
    }

    /**
     * @return  array
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

    /**
     * Setting up Extra-specific variables.
     */
    protected function _setup() 
    {
        // Clear global hashes.
        MarkdownExtended::setVar('cross_references', array());
        MarkdownExtended::setVar('urls', MarkdownExtended::getConfig('predef_urls'));
        MarkdownExtended::setVar('titles', MarkdownExtended::getConfig('predef_titles'));
        MarkdownExtended::setVar('attributes', MarkdownExtended::getConfig('predef_attributes'));
        MarkdownExtended::setVar('predef_abbr', MarkdownExtended::getConfig('predef_abbr'));
        MarkdownExtended::setVar('html_hashes', array());

        // Launch all dependencies '_setup'
        MarkdownExtended::get('Grammar\Gamut')
            ->runGamutsMethod($this->getAllGamuts(), '_setup');
    }
    
    /**
     * Clearing Extra-specific variables.
     */
    protected function _teardown() 
    {
        // Clear global hashes.
        MarkdownExtended::setVar('urls', MarkdownExtended::getConfig('predef_urls'));
        MarkdownExtended::setVar('titles', MarkdownExtended::getConfig('predef_titles'));
        MarkdownExtended::setVar('attributes', MarkdownExtended::getConfig('predef_attributes'));
        MarkdownExtended::setVar('predef_abbr', MarkdownExtended::getConfig('predef_abbr'));
        MarkdownExtended::setVar('html_hashes', array());

        // Launch all dependencies '_teardown'
        MarkdownExtended::get('Grammar\Gamut')
            ->runGamutsMethod($this->getAllGamuts(), '_teardown');
    }
    
}

// Endfile
