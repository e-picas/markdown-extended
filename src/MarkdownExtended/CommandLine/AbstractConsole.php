<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\CommandLine;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Command line controller/interface base
 *
 * This base command line class is designed to use options '-x' or '--verbose' to increase
 * script verbosity on STDOUT and '-q' or '--quiet' to decrease it. The idea is quiet simple:
 *
 * -   in "normal" rendering (no "verbose" neither than "quiet" mode), the result of the 
 *     processed content is rendered, with the file name header in case of multi-files input
 *     and command line script's errors are rendered
 * -   in "verbose" mode, some process information are shown, informing user about what
 *     happening, follow process execution and get some execution information such as some
 *     some string lengths ; the command line script errors are rendered
 * -   in "quiet" mode, nothing is written through SDTOUT except PHP process errors and output
 *     rendering of parsed content ; the command line script's errors are not rendered
 *
 * For all of these cases, PHP errors caught during Markdown Extended classes execution are
 * rendered and script execution may stop.
 *
 * @package MarkdownExtended\CommandLine
 */
abstract class AbstractConsole
{

    /**
     * @var     string  Current script's path
     */
    public $script_path;

    /**
     * @var     STDOUT
     */
    public $stdout;

    /**
     * @var     STDIN
     */
    public $stdin;

    /**
     * @var     STDERR
     */
    public $stderr;

    /**
     * @var     int Initial error reporting for restoration
     */
    public $error_reporting;

    /**
     * @var     \MarkdownExtended\MarkdownExtended
     */
    protected static $mde_instance;

    /**#@+
     * Command line options values
     */
    protected $input         = array();
    protected $verbose       = false;
    protected $quiet         = false;
    protected $debug         = false;
    /**#@-*/

    /**#@+
     * Command line options
     */
    protected $options;
    static $cli_options = array(
        'x'=>'verbose', 
        'q'=>'quiet', 
        'debug', 
    );
    /**#@-*/

    /**
     * Constructor
     *
     * Setup the input/output and verify that we are in CLI mode
     */
    public function __construct()
    {
        $this->error_reporting = error_reporting();
        $this->stdout   = defined('STDOUT') ? STDOUT : fopen('php://stdout', 'c+');
        $this->stdin    = defined('STDIN')  ? STDIN  : fopen('php://stdin', 'c+');
        $this->stderr   = defined('STDERR') ? STDERR : fopen('php://stderr', 'c+');
        if (strpos(php_sapi_name(),'cli')===false) {
            exit('<!-- NOT IN CLI -->');
        }
        if (isset($_SERVER['argv']) && is_array($_SERVER['argv']) && isset($_SERVER['argv'][0])) {
            $this->setScriptPath(realpath($_SERVER['argv'][0]));
        } else {
            $this->setScriptPath(getcwd());
        }
        $this->getOptions();
    }

// -------------------
// Writing methods
// -------------------

    /**
     * Write an info to CLI output
     *
     * @param   string  $str        The information to write
     * @param   bool    $new_line   May we pass a line after writing the info
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function write($str, $new_line = true, $stream = 'stdout')
    {
        if (!property_exists($this, $stream)) {
            $stream = 'stdout';
        }
        fwrite($this->{$stream}, $str . ($new_line===true ? PHP_EOL : ''));
        fflush($this->{$stream});
    }
    
    /**
     * Write an info in verbose mode
     *
     * @param   string  $str            The information to write
     * @param   bool    $new_line       May we pass a line after writing the info
     * @param   bool    $leading_dot    Add a leading dot or not
     * @return  void
     */
    public function info($str, $new_line = true, $leading_dot = true)
    {
        if (!empty($str) && $this->verbose===true) {
            $this->write( ($leading_dot ? '. ' : '') . $str, $new_line);
        }
    }
    
    /**
     * Write an separator line in verbose mode
     *
     * @return  void
     */
    public function separator()
    {
        if ($this->verbose===true) {
            $this->write("  -------------------------------------------  ");
        }
    }

    /**
     * Write an error info and exit
     *
     * @param   string  $str        The information to write
     * @param   int     $code       The error code used to exit the script
     * @param   bool    $forced     Force the 'help' info
     * @return  void
     */
    public function error($str, $code = 90, $forced = false)
    {
        if ($this->quiet!==true || $forced===true) {
            $this->write(">> " . $str, true, 'stderr');
            $this->runOption_usage($code);
        }
        if ($code>0) {
            $this->endRun();
            exit($code);
        }
    }
    
    /**
     * Write an info and exit
     *
     * @param   bool    $exit           May we have to exit the script after writing the info?
     * @param   string  $str            The information to write
     * @param   bool    $leading_signs  Add the leading '>>' sign or not
     * @return  void
     */
    protected function endRun($exit = false, $str = null, $leading_signs = true)
    {
        if ($this->quiet===true) {
            ini_restore('error_reporting');
        }
        if (!empty($str)) {
            $this->write(($leading_signs ? '>> ' : '').$str);
        }
        if ($exit===true) {
            exit(0);
        }
    }

    /**
     * Write a caught exception
     *
     * @param   \Exception  $e
     * @return  void
     */
    public function caught(\Exception $e)
    {
        $str = sprintf(
            'Caught "%s" [file %s - line %d]: "%s"',
            get_class($e),
            str_replace(realpath(__DIR__.'/../../../'), '', $e->getFile()),
            $e->getLine(), $e->getMessage()
        );
        if ($this->verbose===true || $this->debug===true) {
            $str .= PHP_EOL . PHP_EOL . $e->getTraceAsString();
        }
        return $this->error($str, $e->getCode(), true);
    }
    
    /**
     * Exec a command
     *
     * @param   string      $cmd
     * @param   bool        $fail_gracefully
     * @return  string|array
     * @throw   \MarkdownExtended\Exception\RuntimeException if the command fails or returns an error status
     */
    public function exec($cmd, $fail_gracefully = false)
    {
        try {
            exec($cmd, $output, $status);
            if ($status!==0 && $fail_gracefully!==true) {
                throw new MDE_Exception\RuntimeException(
                    sprintf('Error exit status while executing command : [%s]!', $cmd), $status
                );
            }
        } catch (MDE_Exception\RuntimeException $e) {
            $this->caught($e);
        }
        return is_array($output) && count($output)===1 ? $output[0] : $output;
    }
    
// -------------------
// Options
// -------------------

    /**
     * Get any output from previous command STDIN piped
     * see <http://stackoverflow.com/a/9711142/2512020>
     *
     * @return  string|null
     */
    protected function readSafeStdin()
    {
        $data   = '';
        $read   = array($this->stdin);
        $write  = array();
        $except = array();
        try {
            $result = stream_select($read, $write, $except, 0);
            if ($result !== false && $result > 0) {
                while (!feof($this->stdin)) {
                    $data .= fgets($this->stdin);
                }
            }
            @file_put_contents($this->stdin, '');
        } catch (\Exception $e) {
            $data = null;
        }
        return $data;
    }

    /**
     * Get the command line user options
     *
     * @return  void
     */
    protected function getOptions()
    {
        $this->options = getopt(
            join('', array_keys($this::$cli_options)),
            array_values($this::$cli_options)
        );

        $argv = $_SERVER['argv'];
        $last = array_pop($argv);

        $piped = $this->readSafeStdin();
        if (!empty($piped)) {
            $this->input[] = trim($piped, " \n");
        }

        while ($last && count($argv)>=1 && $last[0]!='-' && !in_array($last,$this->options)) {
            $this->input[] = $last;
            $last = array_pop($argv);
        }
        $this->input = array_reverse($this->input);
    }

    /**
     * Run the command line options of the request
     *
     * @return  void
     */
    protected function runOptions()
    {
        foreach ($this->options as $_opt_n=>$_opt_v) {
            $opt_torun = false;
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
                    $this->error("Unknown option '$_opt_n'!");
                } else {
                    $this->info("Unknown option '$_opt_n'! (argument ignored)");
                }
            }
        }
    }

    /**
     * Run the verbose option
     *
     * @return  void
     */
    public function runOption_verbose()
    {
        $this->verbose = true;
        $this->quiet = false;
        error_reporting($this->error_reporting);
    }

    /**
     * Run the quiet option
     *
     * @return  void
     */
    public function runOption_quiet()
    {
        $this->verbose = false;
        $this->quiet = true;
        error_reporting(0); 
    }

    /**
     * Run the debug option
     *
     * @return  void
     */
    public function runOption_debug()
    {
        $this->debug = true;
        error_reporting(E_ALL); 
    }

    /**
     * Run the usage option
     *
     * @param   int     $exit_status
     * @return  void
     */
    public function runOption_usage($exit_status = 0)
    {
        $this->write("Use option '--help' to get information.");
        $this->endRun();
        exit($exit_status);
    }

// -------------------
// Process methods
// -------------------

    /**
     * Set the current script full path
     *
     * @param $path
     * @return $this
     */
    public function setScriptPath($path)
    {
        $this->script_path = $path;
        return $this;
    }

    /**
     * Use of the PHP Markdown Extended class as a singleton
     *
     * @param array $config
     * @return \MarkdownExtended\MarkdownExtended instance
     */
    protected function getMdeInstance(array $config = array())
    {
        if (empty(self::$mde_instance)) {
            $this->info("Creating a MarkdownExtended instance with options ["
                .str_replace("\n", '', var_export($config,1))
                ."]");
            self::$mde_instance = MarkdownExtended::create();
        }
        try {
            self::$mde_instance->get('Parser', array($config), MDE_API::FAIL_WITH_ERROR);
        } catch (MDE_Exception\InvalidArgumentException $e) {
            $this->caught($e);
        } catch (MDE_Exception\RuntimeException $e) {
            $this->caught($e);
        }
        return self::$mde_instance;
    }
    
    /**
     * Writes an output safely for STDOUT (string or arrays)
     *
     * @param   mixed   $content
     * @param   int     $indent     internal indentation flag
     * @return  string
     */
    protected function _renderOutput($content, $indent = 0)
    {
        $text = '';
        if (is_string($content) || is_numeric($content)) {
            $text .= $content;
        } elseif (is_array($content)) {
            $max_length = 0;
            foreach ($content as $var=>$val) {
                if (strlen($var)>$max_length) $max_length = strlen($var);
            }
            foreach ($content as $var=>$val) {
                $text .= PHP_EOL
                    . ($indent>0 ? str_repeat('    ', $indent) : '')
                    . str_pad($var, $max_length, ' ') . ' : '
                    . $this->_renderOutput($val, ($indent+1));
            }
        }
        return ($indent===0 ? trim($text, PHP_EOL) : $text);
    }

// -------------------
// CLI methods
// -------------------

    /**
     * Run the whole script depending on options set
     */
    abstract public function run();

// ----------------------
// Utilities
// ----------------------

    /**
     * Write a result for each processed file or string in a file
     *
     * @param   string  $output
     * @param   string  $output_file
     * @return  int
     */
    public function writeOutputFile($output, $output_file)
    {
        $fsize = null;
        if (!empty($output) && !empty($output_file)) {
            $this->info("Writing parsed content in output file `$output_file`", false);
            if ($ok = @file_put_contents($output_file, $output)) {
                $fsize = MDE_Helper::getFileSize($output_file);
                $this->info("OK [file size: $fsize]");
            } else {
                $this->error("Can not write output file `$output_file` ! (try to run `sudo ...`)");
            }
        }
        return $fsize;
    }

    /**
     * Write a result for each processed file or string
     *
     * @param   string  $output
     * @return  int
     */
    public function writeOutput($output)
    {
        $clength = null;
        if (!empty($output)) {
            $clength = strlen($output);
            $this->info("Rendering parsed content [strlen: $clength]");
            $this->separator();
            $this->write($output);
        }
        return $clength;
    }

    /**
     * Write a title for each processed file or string
     *
     * @param   string  $title
     * @return  void
     */
    public function writeInputTitle($title)
    {
        $this->write("==> $title <==");
    }

}

// Endfile
