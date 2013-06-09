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
namespace MarkdownExtended\CommandLine;

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * Command line controller/interface base
 */
abstract class AbstractConsole 
{

    /**
     * @var STDOUT
     */
	public $stdout;

    /**
     * @var STDIN
     */
	public $stdin;

	/**#@+
	 * Command line options values
	 */
	protected $input         =array();
	protected $verbose       =false;
	protected $quiet         =false;
	/**#@-*/

	/**#@+
	 * Command line options
	 */
	protected $options;
	static $cli_options = array(
		'x'=>'verbose', 
		'q'=>'quiet', 
	);
	/**#@-*/

	/**
	 * Constructor
	 * Setup the input/output, verify that we are in CLI mode and that something is requested
	 * @see self::runOptions()
	 */
	public function __construct()
	{
		$this->stdout = fopen('php://stdout', 'w');
		$this->stdin = fopen('php://stdin', 'w');
		if (php_sapi_name() != 'cli') {
			exit('<!-- NOT IN CLI -->');
		}
		$this->getOptions();
	}

// -------------------
// Writing methods
// -------------------

	/**
	 * Write an info to CLI output
	 * @param string $str The information to write
	 * @param bool $new_line May we pass a line after writing the info
	 */
	public function write($str, $new_line = true)
	{
    	fwrite($this->stdout, $str.($new_line===true ? PHP_EOL : ''));
    	fflush($this->stdout);
	}
	
	/**
	 * Write an info in verbose mode
	 * @param string $str The information to write
	 * @param bool $new_line May we pass a line after writing the info
	 */
	public function info($str, $new_line = true, $leading_dot = true)
	{
		if (!empty($str) && $this->verbose===true) {
		    $this->write(($leading_dot ? '. ' : '').$str, $new_line);
		}
	}
	
	/**
	 * Write an separator line in verbose mode
	 */
	public function separator()
	{
		if ($this->verbose===true) {
		    $this->write("  -------------------------------------------");
		}
	}

	/**
	 * Write an error info and exit
	 * @param string $str The information to write
	 * @param int $code The error code used to exit the script
	 */
	public function error($str, $code = 1)
	{
		if ($this->quiet===true) {
			$this->write( $str );
		} else {
			$this->write(PHP_EOL.">> ".$str.PHP_EOL);
			$this->write("( run '--help' option to get information )");
		}
		if ($code>0) {
			$this->endRun();
			exit($code);
		}
	}
	
	/**
	 * Write an info and exit
	 * @param bool $exit May we have to exit the script after writing the info?
	 * @param string $str The information to write
	 */
	protected function endRun($exit = false, $str = null, $leading_signs = true)
	{
		if ($this->quiet===true) ini_restore('error_reporting'); 
		if (!empty($str)) $this->write(($leading_signs ? '>> ' : '').$str);
		if ($exit==true) exit(0);
	}

// -------------------
// Options
// -------------------

	/**
	 * Get the command line user options
	 */
	protected function getOptions()
	{
		$this->options = getopt(
			join('', array_keys($this::$cli_options)),
			array_values($this::$cli_options)
		);

		$argv = $_SERVER['argv'];
		$last = array_pop($argv);
		while ($last && count($argv)>=1 && $last[0]!='-' && !in_array($last,$this->options)) {
			$this->input[] = $last;
			$last = array_pop($argv);
		}
		$this->input = array_reverse($this->input);
	}

	/**
	 * Run the command line options of the request
	 */
	protected function runOptions()
	{
		foreach ($this->options as $_opt_n=>$_opt_v) {
			$opt_torun=false;
			foreach (array($_opt_n, $_opt_n.':', $_opt_n.'::') as $_opt_item) {
				if (array_key_exists($_opt_item, $this::$cli_options)) {
					$opt_torun = $this::$cli_options[$_opt_item];
				} elseif (in_array($_opt_item, $this::$cli_options)) {
					$opt_torun = $_opt_n;
				}
			}
			$_opt_method = 'runOption_'.str_replace(':', '', str_replace('-', '_', $opt_torun));
			if (method_exists($this, $_opt_method)) {
				$ok = call_user_func_array(
				    array($this, $_opt_method),
				    array($_opt_v)
				);
			} else {
				if (count($this->options)==1) {
					$this->error("Unknown argument '$_opt_n'!");
				} else {
					$this->info("Unknown argument '$_opt_n'! (argument ignored)");
				}
			}
		}
	}

	/**
	 * Run the verbose option
	 */
	public function runOption_verbose()
	{
		$this->verbose = true;
		$this->info("Enabling 'verbose' mode");
	}

	/**
	 * Run the quiet option
	 */
	public function runOption_quiet()
	{
		$this->quiet = true;
		error_reporting(0); 
		$this->info("Enabling 'quiet' mode, no PHP error will be written");
	}

// -------------------
// CLI methods
// -------------------

	/**
	 * Run the whole script depending on options setted
	 */
	abstract public function run();

}

// Endfile