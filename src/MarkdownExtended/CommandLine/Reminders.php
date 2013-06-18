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
    MarkdownExtended\CommandLine\AbstractConsole,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * Command line controller to rebuild the MarkdownExtended reminders HTML
 */
class Reminders extends AbstractConsole
{

    /**
     * @var \MarkdownExtended\MarkdownExtended
     */
    protected static $emd_instance;

    /**
     * @var array Collection of \MarkdownExtended\Content objects
     */
    protected $md_contents  =array();

    /**#@+
     * Command line options values
     */
    protected $output       =false;
    protected $config       =false;
    protected $docs_dir     =false;
    protected $format       ='HTML';
    /**#@-*/

    /**
     * Command line options
     */
    static $cli_options = array(
        'x'=>'verbose', 
        'q'=>'quiet', 
        'debug', 
        'h'=>'help', 
        'o:'=>'output:', 
        'c:'=>'config:', 
        'd:'=>'docsdir:', 
        'f:'=>'format:', 
    );

    /**
     * Constructor
     * Setup the input/output, verify that we are in CLI mode and that something is requested
     * @see self::runOptions()
     */
    public function __construct()
    {
        parent::__construct();
        $this->runOption_docsdir(__DIR__.'/../Resources/doc');
        $this->runOption_output(__DIR__.'/../../markdown_reminders.html');
        $this->runOption_config(MarkdownExtended::FULL_CONFIGFILE);
        $this->runOptions();
    }


// -------------------
// Options methods
// -------------------

    /**
     * Get the help string
     */
    public function runOption_help()
    {
        $class_name = MarkdownExtended::MDE_NAME;
        $class_version = MarkdownExtended::MDE_VERSION;
        $class_sources = MarkdownExtended::MDE_SOURCES;
        $help_str = <<<EOT
[ {$class_name} {$class_version} - Reminders builder ]

Rebuild the `markdown_reminders` HTML file based on parsing the `Resources/doc/` directory contents.

Usage:
    ~$ php path/to/build_reminders [OPTIONS ...]

Options:
    -h | --help                get this help information
    -x | --verbose             increase verbosity of the script
    -q | --quiet               do not write Markdown Parser or PHP error messages
    -o | --output    = FILE    specify a file to write generated content in (default is 'src/markdown_reminders.html')
    -c | --config    = FILE    configuration file to use for Markdown instance (INI format)
    -f | --format    = NAME    format of the output (default is HTML)

More infos at <{$class_sources}>.
EOT;
        $this->write($help_str);
        $this->endRun();
        exit(0);
    }

    /**
     * Run the multi docsdir
     */
    public function runOption_docsdir($path)
    {
        $this->docs_dir = $path;
        $this->info("Setting 'docs_dir' to '$this->docs_dir'");
    }

    /**
     * Run the output option
     * @param string $file The command line option argument
     */
    public function runOption_output($file)
    {
        $this->output = $file;
        $this->info("Setting 'output' to `$this->output`");
    }

    /**
     * Run the config file option
     * @param string $file The command line option argument
     */
    protected function runOption_config($file)
    {
        $this->config = $file;
        $this->info("Setting configuration file to `$this->config`");
    }

    /**
     * Run the format option
     * @param string $str The command line option argument
     */
    public function runOption_format($str)
    {
        $this->format = $str;
        $this->info("Setting 'format' to `".$this->format."`");
    }

// -------------------
// CLI methods
// -------------------

    /**
     * Run the whole script depending on options setted
     */
    public function run()
    {
        $this->info(PHP_EOL.">>>> let's go for the parsing ...".PHP_EOL, true, false);
        $dir = new \DirectoryIterator($this->docs_dir);
        $_emd = $this->getEmdInstance();
        foreach ($dir as $_file) {
            if ($dir->isFile() && !$dir->isDot() && $dir->getExtension()==='md') {
                try {
                    $md_content = new \MarkdownExtended\Content(null, $dir->getPathname());
                    $md_output = $_emd->get('Parser')
                        ->parse($md_content)
                        ->getContent();
                } catch (\MarkdownExtended\Exception\DomainException $e) {
                    $this->catched($e);
                } catch (\MarkdownExtended\Exception\RuntimeException $e) {
                    $this->catched($e);
                } catch (\MarkdownExtended\Exception\UnexpectedValueException $e) {
                    $this->catched($e);
                } catch (\MarkdownExtended\Exception\InvalidArgumentException $e) {
                    $this->catched($e);
                } catch (\MarkdownExtended\Exception\Exception $e) {
                    $this->catched($e);
                } catch (\Exception $e) {
                    $this->catched($e);
                }
                $this->md_contents[] = $md_output;
            }
        }

var_export($this->md_contents);

        $this->info(PHP_EOL.">>>> the parsing is complete.".PHP_EOL, true, false);
        $this->endRun(1);
    }

// -------------------
// Process
// -------------------

    /**
     * Use of the PHP Markdown Extended class as a singleton
     */
    protected function getEmdInstance(array $config = array())
    {
        if (false!==$this->config) {
            $config['config_file'] = $this->config;
        }
        if (!empty($this->format)) {
            $config['output_format'] = $this->format;
        }           
        return parent::getEmdInstance($config);
    }
    
    /**
     * Write a result for each processed file or string in a file
     * @param string $output
     * @param string $output_file
     */
    public function writeOutputFile($output)
    {
        $fsize=null;
        if (!empty($output) && !empty($this->output)) {
            $this->info("Writing parsed content in output file `$this->output`", false);
            if ($ok = @file_put_contents($this->output, $output)) {
                $fsize = MDE_Helper::getFileSize($this->output);
                $this->info("OK [file size: $fsize]");
            } else {
                $this->error("Can not write output file `$this->output` ! (try to run `sudo ...`)");
            }
        }
        return $fsize;
    }

    /**
     * Write a result for each processed file or string
     * @param string $output
     * @param bool $exit
     */
    public function writeOutput($output, $exit = false)
    {
        $clength=null;
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
     * @param string $title
     */
    public function writeInputTitle($title)
    {
        $this->write("==> $title <==");
    }

}

// Endfile
