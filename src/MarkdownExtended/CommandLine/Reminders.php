<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\CommandLine;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Config;
use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Command line controller to rebuild the MarkdownExtended reminders HTML
 *
 * @package MarkdownExtended\CommandLine
 */
class Reminders
    extends AbstractConsole
{

    /**
     * @var array Collection of \MarkdownExtended\ContentCollection object
     */
    protected $md_contents;

    /**#@+
     * Command line options values
     */
    protected $output       =false;
    protected $config       =false;
    protected $docs_dir     =false;
    protected $format       ='HTML';
    protected $template     ='markdown_reminders.php';
    /**#@-*/

    /**
     * Command line options
     */
    static $cli_options = array(
        'v'=>'verbose', 
        'q'=>'quiet', 
        'x'=>'debug', 
        'h'=>'help', 
        'o:'=>'output:', 
        'c:'=>'config:', 
        'd:'=>'docsdir:', 
        'f:'=>'format:', 
        't:'=>'template:', 
    );

    /**
     * Constructor
     * Setup the input/output, verify that we are in CLI mode and that something is requested
     * @see self::runOptions()
     */
    public function __construct()
    {
        parent::__construct();
        try {
            $this->md_contents = MDE_API::factory('ContentCollection', null, 'content_collection');
        } catch (MDE_Exception\InvalidArgumentException $e) {
            $this->caught($e);
        } catch (MDE_Exception\RuntimeException $e) {
            $this->caught($e);
        }
        $this->runOption_docsdir(__DIR__.'/../Resources/doc');
        $this->runOption_output(__DIR__.'/../../../markdown_reminders.html');
        $this->runOption_config(Config::FULL_CONFIGFILE);
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
    -v | --verbose             increase verbosity of the script
    -q | --quiet               do not write Markdown Parser or PHP error messages
    -o | --output    = FILE    specify a file to write generated content in (default is 'markdown_reminders.html')
    -c | --config    = FILE    configuration file to use for Markdown instance (INI format)
    -f | --format    = NAME    format of the output (default is HTML)
    -t | --template  = FILE    specify a file a template file to build content (default is 'markdown_reminders.php')

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
        $this->info("Setting 'format' to `$this->format`");
    }

    /**
     * Run the template option
     * @param string $file The command line option argument
     */
    public function runOption_template($file)
    {
        $this->template = $file;
        $this->info("Setting 'template' to `$this->template`");
    }

// -------------------
// CLI methods
// -------------------

    /**
     * Run the whole script depending on options set
     */
    public function run()
    {
        $this->info(PHP_EOL.">>>> let's go for the parsing ...".PHP_EOL, true, false);
        $dir = new \DirectoryIterator($this->docs_dir);
        $_emd = $this->getMdeInstance();
        foreach ($dir as $_file) {
            if ($dir->isFile() && !$dir->isDot() && $dir->getExtension()==='md') {
                try {
                    $this->info("parsing file ".$dir->getPathname());
                    $md_id = MDE_Helper::header2label(
                        str_replace('.md', '', basename($dir->getPathname()))
                    );
                    $md_content = MDE_API::factory('Content', array(
                        null, $dir->getPathname(), $md_id
                    ));
                    $parser = $_emd->get('Parser');
                    $md_output = $parser
                        ->parse($md_content)
                        ->getContent();
                } catch (MDE_Exception\DomainException $e) {
                    $this->caught($e);
                } catch (MDE_Exception\RuntimeException $e) {
                    $this->caught($e);
                } catch (MDE_Exception\UnexpectedValueException $e) {
                    $this->caught($e);
                } catch (MDE_Exception\InvalidArgumentException $e) {
                    $this->caught($e);
                } catch (MDE_Exception\Exception $e) {
                    $this->caught($e);
                } catch (\Exception $e) {
                    $this->caught($e);
                }
                $this->md_contents->add($md_content);
            }
        }
        $reminders = $_emd->getTemplater(array(array(
                'template'=>$this->template
            )))
            ->buildTemplate(array(
                'span_contents' => $this->md_contents->getArrayFilter(array($this, 'filterSpan')),
                'block_contents' => $this->md_contents->getArrayFilter(array($this, 'filterBlock')),
                'misc_contents' => $this->md_contents->getArrayFilter(array($this, 'filterMisc')),
                'mde_home' => MarkdownExtended::MDE_SOURCES,
                'mde_name' => MarkdownExtended::MDE_NAME,
                'mde_version' => MarkdownExtended::MDE_VERSION,
            ))
            ->getContent();
        if ($this->writeOutputFile($reminders, $this->output)) {
            $this->info(PHP_EOL.">>>> the parsing is complete.".PHP_EOL, true, false);
        } else {
            $this->error(
                sprintf("An error occurred while trying to write content in file '%s'", $this->output)
            );
        }
        $this->endRun(1);
    }

// -------------------
// Process
// -------------------

    /**
     * Use of the PHP Markdown Extended class as a singleton
     */
    protected function getMdeInstance(array $config = array())
    {
        if (false!==$this->config) {
            $config['config_file'] = $this->config;
        }
        if (!empty($this->format)) {
            $config['output_format'] = $this->format;
        }           
        return parent::getMdeInstance($config);
    }
    
    /**
     * Write a result for each processed file or string in a file
     * @param string $output
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

    public function filterSpan($item) 
    {
        $meta = $item->getMetadata();
        return (isset($meta['block']) && in_array(
            trim($meta['block']), array('Span', 'Span Elements')
        ));
    }
    
    public function filterBlock($item) 
    {
        $meta = $item->getMetadata();
        return (isset($meta['block']) && in_array(
            trim($meta['block']), array('Block', 'Block Elements')
        ));
    }

    public function filterMisc($item) 
    {
        $meta = $item->getMetadata();
        return (isset($meta['block']) && in_array(
            trim($meta['block']), array('Misc', 'Miscellaneous')
        ));
    }
    

}

// Endfile
