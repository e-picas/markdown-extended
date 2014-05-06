<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
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

use \MarkdownExtended\Registry;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Global configuration registry
 */
class Config
    extends Registry
{

    /**
     * @var     array
     */
    public static $defaults = array(
        // the default output format
        'output_format' => 'HTML',
        // Change to ">" for HTML output
        'html_empty_element_suffix' => ' />',
        // Define the width of a tab (4 spaces by default)
        'tab_width' => 4,
        // Table of hash values for escaped characters:
        'escape_chars' => "\`*_{}[]()>#+-.!:|",
        // Regex to match balanced [brackets].
        // Needed to insert a maximum bracked depth while converting to PHP.
        'nested_brackets_depth' => 6,
        // Regex to match balanced (parenthesis).
        // Needed to insert a maximum bracked depth while converting to PHP.
        'nested_url_parenthesis_depth' => 4,
        // Change to `true` to disallow markup or entities.
        'no_markup' => false,
        'no_entities' => false,
        // Special metadata used during parsing
        'special_metadata' => array('baseheaderlevel', 'quoteslanguage'),
        // Block inclusion tag
        'block_inclusion' => '<!-- @([^ @]+)@ -->',
        // Gamut classes aliases
        'gamut_aliases' => array(
            'filter' => '\MarkdownExtended\Grammar\Filter',
            'tool' => '\MarkdownExtended\Grammar\Tool'
        ),
        // Optional title attribute for links that do not have one
        'link_mask_title' => 'See online %%',
        'mailto_mask_title' => 'Contact %%',
        // Optional attribute to define for fenced code blocks with language type
        'fcb_language_attribute' => 'class',
        // Attribute's value construction for fenced code blocks with language type
        'fcb_attribute_value_mask' => 'language-%%',
        // Predefined urls, titles and abbreviations for reference links and images.
        'predef_urls' => array(),
        'predef_titles' => array(),
        'predef_attributes' => array(),
        'predef_abbr' => array(),
    );

    /**
     * @var     string
     */
    protected $config_file;

    /**
     * @var  array
     */
    protected static $cached_config_files = array();

    /**
     * Create a configuration object
     */
    public function __construct()
    {
        parent::__construct();
        $this->reset();
    }

    /**
     * Reset data on defaults
     *
     * @return  void
     */
    public function reset()
    {
        $this->data = self::$defaults;
    }

    /**
     * Init a new config with user options
     *
     * @param   null/string/array   $user_config
     * @return  void
     */
    public function init($user_config = null)
    {
        $config_file = MarkdownExtended::FULL_CONFIGFILE;
        if (!empty($user_config)) {
            if (is_string($user_config)) {
                $config_file = $user_config;
            } elseif (is_array($user_config)) {
                if (isset($user_config['config_file'])) {
                    $config_file = $user_config['config_file'];
                    unset($user_config['config_file']);
                }
            }
        }
        $this->reload($config_file, true);
        if (!empty($user_config) && is_array($user_config)) {
            $this->overload($user_config);
        }
    }

    /**
     * Load and parse a INI configuration file
     *
     * @param   string/array    $cfg_file
     * @param   bool            $silent
     * @return  void
     */
    public function load($cfg_file, $silent = false)
    {
        $this->setConfigFile($cfg_file);
        $mde_config = $this->_loadFile($this->config_file, $silent);
        if (!empty($mde_config)) {
            foreach ($mde_config as $_var=>$_val) {
                $this->set($_var, $_val);
            }
        }
    }

    /**
     * Over-load a configuration
     *
     * @param   string/array    $cfg_file
     * @param   bool            $forced
     * @param   bool            $silent
     * @return  void
     */
    public function reload($cfg_file = null, $forced = false, $silent = false)
    {
        $old_cfg_file = $this->getConfigFile();
        if (
            (!empty($cfg_file) && $old_cfg_file!==$cfg_file) ||
            $forced
        ) {
            $this->reset();
            $this->load($cfg_file, $silent);
        }
    }

    /**
     * Over-load a configuration
     *
     * @param   array   $config
     * @return  void
     */
    public function overload(array $config)
    {
        if (!empty($config)) {
            foreach ($config as $_opt_name=>$_opt_value) {
                $this->set($_opt_name, $_opt_value);
            }
        }
    }

    /**
     * Define the object config file
     *
     * @param   string  $cfg_file
     * @return  self
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if file doesn't exist
     */
    public function setConfigFile($cfg_file)
    {
        $cfg_file = MDE_Helper::find($cfg_file, 'config');
        if (file_exists($cfg_file)) {
            $this->config_file = $cfg_file;
        }  else {
            throw new MDE_Exception\UnexpectedValueException(sprintf(
                "Defined configuration file doesn't exist, get <%s>!", $cfg_file
            ));
        }
        return $this;
    }

    /**
     * Get the object config file
     *
     * @return  string
     */
    public function getConfigFile()
    {
        return $this->config_file;
    }

    /**
     * Really load and parse a INI configuration file
     *
     * @param   array   $cfg_file
     * @param   bool    $silent
     * @return  array
     * @throws  \MarkdownExtended\Exception\DomainException if file seems malformed
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if file doesn't exist
     */
    protected function _loadFile($cfg_file, $silent = false)
    {
        if (empty($cfg_file)) return array();
        if (!isset(self::$cached_config_files[$cfg_file])) {
            if (file_exists($cfg_file)) {
                $mde_config = parse_ini_file($cfg_file, true);
                if (isset($mde_config) && is_array($mde_config) && !empty($mde_config)) {
                    self::$cached_config_files[$cfg_file] = $mde_config;
                }  elseif ($silent!==true) {
                    throw new MDE_Exception\DomainException(sprintf(
                        "Configuration file doesn't seem to have a well-formed INI array in <%s>!", $cfg_file
                    ));
                }
            }  else {
                throw new MDE_Exception\UnexpectedValueException(sprintf(
                    "Defined configuration file doesn't exist, get <%s>!", $cfg_file
                ));
            }
        }
        return self::$cached_config_files[$cfg_file];
    }

}

// Endfile