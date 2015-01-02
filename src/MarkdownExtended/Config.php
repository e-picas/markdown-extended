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

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Registry;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Global configuration registry
 * @package MarkdownExtended
 */
class Config
    extends Registry
{

    /**
     * Default full options INI file
     */
    const FULL_CONFIGFILE = 'markdown_config.full.ini';

    /**
     * Default simple options INI file (i.e. for input fields)
     */
    const SIMPLE_CONFIGFILE = 'markdown_config.simple.ini';

    /**
     * @var     array
     */
    public static $defaults = array(
        // the default API objects
        'content_class' => '\MarkdownExtended\Content',
        'content_collection_class' => '\MarkdownExtended\ContentCollection',
        'parser_class' => '\MarkdownExtended\Parser',
        'templater_class' => '\MarkdownExtended\Templater',
        'grammar\filter_class' => '\MarkdownExtended\Grammar\Filter',
        'grammar\tool_class' => '\MarkdownExtended\Grammar\Tool',
        // Gamut classes aliases
        'gamut_aliases' => array(
            'filter' => '\MarkdownExtended\Grammar\Filter',
            'tool' => '\MarkdownExtended\Grammar\Tool'
        ),
        // the default output format
        'output_format' => 'HTML',
        // Change to ">" for HTML output
        'html_empty_element_suffix' => ' />',
        // Define the width of a tab (4 spaces by default)
        'tab_width' => 4,
        // Table of hash values for escaped characters:
        'escape_chars' => "0`*_{}[]()<>#+-.!:|\\",
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
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if file doesn't exist
     * @throws  \MarkdownExtended\Exception\DomainException if file seems malformed
     */
    public function init($user_config = null)
    {
        $config_file = self::FULL_CONFIGFILE;
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
        try {
            $this->reload($config_file, true);
        } catch (MDE_Exception\UnexpectedValueException $e) {
            throw $e;
        } catch (MDE_Exception\DomainException $e) {
            throw $e;
        }
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
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if file doesn't exist
     * @throws  \MarkdownExtended\Exception\DomainException if file seems malformed
     */
    public function load($cfg_file, $silent = false)
    {
        try {
            $this->setConfigFile($cfg_file);
            $mde_config = $this->_loadFile($this->config_file, $silent);
        } catch (MDE_Exception\UnexpectedValueException $e) {
            throw $e;
        } catch (MDE_Exception\DomainException $e) {
            throw $e;
        }
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
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if file doesn't exist
     * @throws  \MarkdownExtended\Exception\DomainException if file seems malformed
     */
    public function reload($cfg_file = null, $forced = false, $silent = false)
    {
        $old_cfg_file = $this->getConfigFile();
        if (
            (!empty($cfg_file) && $old_cfg_file!==$cfg_file) ||
            $forced
        ) {
            $this->reset();
            try {
                $this->load($cfg_file, $silent);
            } catch (MDE_Exception\UnexpectedValueException $e) {
                throw $e;
            } catch (MDE_Exception\DomainException $e) {
                throw $e;
            }
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