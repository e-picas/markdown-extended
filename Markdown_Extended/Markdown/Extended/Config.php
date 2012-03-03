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
class Markdown_Extended_Config 
{

 /**
  *
  */
	protected $config_file;

 /**
  *
  */
	public function __construct( $cfg_file=null )
	{
		self::load($cfg_file, true);
	}

 /**
  *
  */
	public function load( $cfg_file=null, $silent=false )
	{
		self::setConfigFile( $cfg_file );
		if (!empty($this->config_file)) {
			if (file_exists($this->config_file)) {

				$mde_config = parse_ini_file( $this->config_file, true );

				if (isset($mde_config) && is_array($mde_config) && !empty($mde_config)) {
					foreach($mde_config as $_var=>$_val) {
						Markdown_Extended::setConfig( $_var, $_val );
					}

				} elseif ($silent!==true) {
					throw new DomainException(sprintf(
  		  		"Configuration file doesn't seem to have a well-formed INI array in <%s>!", $this->config_file
		  	  ));
				}

			} elseif ($silent!==true) {
				throw new UnexpectedValueException(sprintf(
  	  		"Defined configuration file doesn't exist, get <%s>!", $this->config_file
	  	  ));
			}

		} else {
			if ($silent!==true) {
				throw new UnexpectedValueException(
  	  		"Undefined configuration file!"
	  	  );
			}
		}
	}
	
 /**
  *
  */
	public function setConfigFile( $cfg_file=null )
	{
		if (!empty($cfg_file)) $this->config_file = $cfg_file;
	}
	
}

// Endfile