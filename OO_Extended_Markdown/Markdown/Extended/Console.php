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
class Markdown_Extended_Console 
{

	public $stdout; //stdout
	public $stdin;  //stdin

	protected $md_content='';
	protected $md_parsed_content='';

	protected $input         =array();
	protected $verbose       =false;
	protected $quiet         =false;
	protected $output        =false;
	protected $multi         =false;
	protected $filter_html   =false;
	protected $filter_styles =false;
	protected $nofilter      =false;
	protected $extract       =false;

	protected $options;
	static $cli_options = array(
		'v'=>'version', 
		'h'=>'help', 
		'x'=>'verbose', 
		'q'=>'quiet', 
		'o:'=>'output:', 
		'm'=>'multi', 
		'filter-html', 
		'filter-styles', 
		'nofilter:', 
		'extract::'
	);

	static $parsedfiles_counter=1;

	public function __construct()
	{
		$this->stdout = fopen('php://stdout', 'w');
		$this->stdin = fopen('php://stdin', 'w');
		if (php_sapi_name() != 'cli') exit('<!-- NOT IN CLI -->');
		self::getOptions();
		if (empty($this->options) && empty($this->input)) 
			self::error( "No argument found - nothing to do!" );
		self::runOptions();
	}

	protected function getOptions()
	{
		$this->options = getopt(
			join('', array_keys(self::$cli_options)),
			array_values(self::$cli_options)
		);

		$argv = $_SERVER['argv'];
		$last = array_pop($argv);
		while ($last && count($argv)>=1 && $last[0]!='-' && !in_array($last,$this->options)) {
			$this->input[] = $last;
			$last = array_pop($argv);
		}
	}

	protected function runOptions()
	{
		foreach($this->options as $_opt_n=>$_opt_v) 
		{
			$opt_torun=false;
			foreach(array($_opt_n, $_opt_n.':', $_opt_n.'::') as $_opt_item) {
				if (array_key_exists($_opt_item, self::$cli_options))
					$opt_torun = self::$cli_options[$_opt_item];
				elseif (in_array($_opt_item, self::$cli_options))
					$opt_torun = $_opt_n;
			}
			$_opt_method = 'runOption_'.str_replace(':', '', str_replace('-', '_', $opt_torun));
			if (method_exists($this, $_opt_method))
				$ok = $this->$_opt_method( $_opt_v );
			else
				self::info( "Unknown argument '$_opt_n'! (argument ignored)" );
		}

		if (!empty($this->input)) {
			if ($this->multi===true)
				self::info( "Input files are setted on `".join(', ', $this->input)."`" );
			else
				self::info( "Input file is setted on `{$this->input[0]}`" );
		}
	}

	public function write( $str, $new_line=true )
	{
    fwrite($this->stdout, $str.( $new_line===true ? PHP_EOL : '' ));
    fflush($this->stdout);
	}
	
	public function info( $str, $new_line=true )
	{
		if ($this->verbose===true) self::write( ". ".$str." ...", $new_line );
	}
	
	public function error( $str, $code=1 )
	{
		self::write( PHP_EOL.">> ".$str.PHP_EOL );
		self::write( "( run '--help' option to get information )" );
		self::endRun();
		exit($code);
	}
	
	private function endRun( $exit=false, $str=null )
	{
		if ($this->quiet===true) ini_restore('error_reporting'); 
		if (!empty($str)) self::write( ">> ".$str );
		if ($exit==true) exit(0);
	}

// -------------------
// CLI methods
// -------------------

	public function run()
	{
		if ($this->verbose===true)
			self::write( PHP_EOL.">>>> let's go for the parsing ...".PHP_EOL );
		if (!empty($this->input)) {
			if ($this->multi===true) {
				$myoutput = $this->output;
				foreach($this->input as $_input) {
					if (!empty($this->output))
						$this->output = self::buildOutputFilename( $myoutput );
					$_ok = self::runStoryOnOneFile($_input);
				}
				if ($this->verbose===true)
					self::write( "  -------------------------------------------" );
			} else {
					$_ok = self::runStoryOnOneFile($this->input[0]);
			}
		} else {
			self::error( "No input markdown file entered!" );
		}
		if ($this->verbose===true)
			self::write( PHP_EOL.">>>> the parsing is complete.".PHP_EOL );
		self::endRun(1);
	}

	public function runOption_help()
	{
//  --filter-html           filter out raw HTML (except styles)
//  --filter-styles         filter out HTML styles
		$help_str = <<<EOT
[ Markdown Extended CLI ]

Usage:
  ~$ php path/to/markdown.php [OPTION ...] [INPUT FILE(S) OR STRING(S)]

Options:
  -v | --version          get Markdown version information
  -h | --help             get this help information
  -x | --verbose          increase verbosity of Markdown
  -q | --quiet            do not write PHP error messages
  -o | --output=FILE      specify a file to write generated content
  -m | --multi            multi-files input
  --nofilter=A,B          specify a list of filters that will be ignored during Markdown parsing
  --extract[=meta]        extract some (specific if sepcified) metadata from the Markdown input

Converts text(s) in specified file(s) (or stdin) from markdown syntax.
By default, result is written through stdin in HTML format.

EOT;
		if (version_compare(PHP_VERSION, '5.1.0', '<')) {
			$phpvers = phpversion();
			$help_str .= <<<EOT

IMPORTANT NOTE: 
  Your system is running PHP $phpvers ; command line options with argument separated by
  an equal sign `=` is not supported ; please DO NOT USE EQUAL SIGN for options, use space :
    ~$ php path/to/markdown.php -o my_output_file.html input_markdown.md

EOT;
		}
		self::write( $help_str );
		self::endRun();
		exit(0);
	}

	public function runOption_version()
	{
		self::write( Markdown_Extended::info() );
		self::endRun();
		exit(0);
	}

	public function runOption_verbose()
	{
		$this->verbose = true;
		self::info( "Enabling 'verbose' mode" );
	}

	public function runOption_quiet()
	{
		$this->quiet = true;
		error_reporting(0); 
		self::info( "Enabling 'quiet' mode, no PHP error will be written" );
	}

	public function runOption_multi()
	{
		$this->multi = true;
		self::info( "Enabling 'multi' input mode" );
	}

	public function runOption_output( $file )
	{
		$this->output = $file;
		self::info( "Setting 'output' on `$this->output`, parsed content will be written in file(s)" );
	}

	public function runOption_filter_html()
	{
		$this->filter_html = true;
		self::info( "Enabling HTML filter, all HTML will be parsed" );
	}

	public function runOption_filter_styles()
	{
		$this->filter_styles = true;
		self::info( "Enabling HTML styles filter, will try to parse styles" );
	}

	public function runOption_extract( $meta=true )
	{
		$this->extract = is_bool($meta) ? true : $meta;
		if ($this->extract===true)
			self::info( "Enabling 'extract' mode, only metadata will be parsed" );
		else
			self::info( "Setting 'extract' on `$this->extract`, only this metadata will be parsed" );
	}

	public function runOption_nofilter( $str )
	{
		$this->nofilter = explode(',', $str);
		self::info( "Setting 'nofilter' on `".join(', ', $this->nofilter)."`, these will be ignored during parsing" );
	}

// -------------------
// Process
// -------------------

	public function runStoryOnOneFile( $input )
	{
		if ($this->extract!==false) {
			$infos = self::runOneFile( $input, null, $this->extract );
			self::endRun(0, "Infos extracted from input `$input`:".$infos);
			return $infos;
		} elseif (!empty($this->output)) {
			$fsize = self::runOneFile( $input, $this->output );
			self::endRun(0, "OK - File `$this->output` ($fsize) written with parsed content from file `$input`");
			return $fsize;
		} else {
			$clength = self::runOneFile( $this->input[0] );
			return $clength;
		}
	}

	public function runOneFile( $input, $output=null, $extract=null )
	{
		$return=null;
		if (!empty($input)) {
			$num = self::$parsedfiles_counter;
			if ($this->verbose===true)
				self::write( "  -------------------------------------------" );
			self::info( "[$num] >> parsing file `$input`" );
			if ($md_content = self::getInput( $input )) {
				if (!is_null($extract)) {
					$return = self::extractContent( $md_content, $extract );
				} else {
					if ($md_parsed_content = self::parseContent( $md_content )) {
						if (!empty($output)) {
							$return = self::writeOutputFile( $md_parsed_content, $output );
						} else {
							$return = self::writeOutput( $md_parsed_content );
						}
					}
				}
			}
			self::$parsedfiles_counter++;
		}
		return $return;
	}

	public function getInput( $input )
	{
		$md_content=null;
		if (!empty($input)) {
			if (@file_exists($input)) {
				self::info( "Reading input file `$input`", false );
				if ($md_content = @file_get_contents( $input )) {
					$this->md_content .= $md_content;
					self::info( "OK [strlen: ".strlen($md_content)."]" );
				} else {
					self::error( "Could not open input file `$input`!" );
				}
			} else {
				self::error( "Entered input markdown file `$input` not found!" );
			}
		}
		return $md_content;
	}

	public function parseContent( $md_content )
	{
		$md_output=null;
		if (!empty($md_content)) {
			self::info( "Parsing Mardkown content", false );
			if ($md_output = Markdown($md_content, array('skip_filters'=>$this->nofilter))) {
				$this->md_parsed_content .= $md_output;
				self::info("OK [strlen: ".strlen($md_output)."]");
			} else {
				self::error( "An error occured while trying to parse Markdown content ! (try to run `cd dir/to/markdown.php ...`)" );
			}
		}
		return $md_output;
	}

	public function extractContent( $md_content, $extract )
	{
		$md_output=null;
		if (!empty($md_content)) {
			self::info( "Extracting Mardkown metadata", false );
			if ($ok = Markdown($md_content, array('special_gamut'=>array(
				'filter:MetaData:strip'=>1
			)))) {
				$metadata = Markdown_Extended::getVar('metadata');
				self::info("OK [entries: ".count($metadata)."]");
				$md_output = '';
				foreach($metadata as $_metan=>$_metav) {
					if (is_string($extract)) {
						if ($extract==$_metan) $md_output = $_metav;
					} else {
						$md_output .= PHP_EOL.$_metan.' : '.$_metav;
					}
				}
			} else {
				self::error( "An error occured while trying to extract data form Markdown content ! (try to run `cd dir/to/markdown.php ...`)" );
			}
		}
		return $md_output;
	}

	public function writeOutputFile( $output, $output_file )
	{
		$fsize=null;
		if (!empty($output) && !empty($output_file)) {
			self::info( "Writing parsed content in output file `$output_file`", false );
			if ($ok = @file_put_contents( $output_file, $output )) {
				$fsize = self::getFileSize( $output_file );
				self::info( "OK [file size: $fsize]" );
			} else {
				self::error( "Can not write output file `$output_file` ! (try to run `sudo ...`)" );
			}
		}
		return $fsize;
	}

	public function writeOutput( $output, $exit=false )
	{
		$clength=null;
		if (!empty($output)) {
			$clength = strlen($output);
			self::info( "Rendering parsed content [strlen: $clength]" );
			if ($this->verbose===true)
				self::write( "  -------------------------------------------" );
			self::write( $output );
		}
		return $clength;
	}

	protected static function getFileSize( $file )
	{
		$size = @filesize($file);
		if (empty($size)) return null;
    if ($size < 1024) {
      return $size .' B';
    } elseif ($size < 1048576) {
      return round($size / 1024, 2) .' KiB';
    } elseif ($size < 1073741824) {
      return round($size / 1048576, 2) . ' MiB';
    } else {
      return round($size / 1073741824, 2) . ' GiB';
    }
  }

	protected function buildOutputFilename( $filename )
	{
		$ext = strrchr($filename, '.');
		$_f = str_replace($ext, '', $filename);
		return $_f.'_'.self::$parsedfiles_counter.$ext;
	}

}

// Endfile