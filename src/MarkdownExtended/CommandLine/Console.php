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
namespace MarkdownExtended\CommandLine;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\CommandLine\AbstractConsole;
use \MarkdownExtended\API as MDE_API;
use \MarkdownExtended\Helper as MDE_Helper;
use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Command line controller/interface for MarkdownExtended
 *
 * @package MarkdownExtended\CommandLine
 */
class Console
    extends AbstractConsole
{

    /**
     * @var string
     */
    protected $md_content='';

    /**
     * @var string
     */
    protected $md_parsed_content='';

    /**#@+
     * Command line options values
     */
    protected $output        =false;
    protected $multi         =false;
    protected $config        =false;
    protected $filter_html   =false;
    protected $filter_styles =false;
    protected $nofilter      =false;
    protected $extract       =false;
    protected $format        ='HTML';
    protected $template      =false;
    /**#@-*/

    /**
     * Command line options
     */
    static $cli_options = array(
        'v'=>'verbose', 
        'q'=>'quiet', 
        'x'=>'debug', 
        'V'=>'version', 
        'h'=>'help', 
        'o:'=>'output:', 
        'm'=>'multi', 
        'c:'=>'config:', 
        'f:'=>'format:', 
        'g:'=>'gamuts::', 
        'n:'=>'nofilter:', 
        'e::'=>'extract::',
        't::'=>'template::',
        'man',
//      'filter-html', 
//      'filter-styles', 
        // aliases
        's'=>'simple',
        'b'=>'body',
    );

    /**
     * @var array
     */
    public static $extract_presets = array(
        'body'=>array(
            'getter'=>'getBody',
            'gamuts'=>null
        ),
        'meta'=>array(
            'getter'=>'getMetadata',
            'gamuts'=>array('filter:MetaData:strip'=>1)
        ),
        'notes'=>array(
            'getter'=>'getNotes',
            'gamuts'=>null
        ),
        'footnotes'=>array(
            'getter'=>'getFootnotes',
            'gamuts'=>null
        ),
        'glossary'=>array(
            'getter'=>'getGlossaries',
            'gamuts'=>null
        ),
        'citations'=>array(
            'getter'=>'getCitations',
            'gamuts'=>null
        ),
        'urls'=>array(
            'getter'=>'getUrls',
            'gamuts'=>null
        ),
        'menu'=>array(
            'getter'=>'getMenu',
            'gamuts'=>null
        ),
    );
    
    /**
     * Internal counter
     */
    static $parsedfiles_counter=1;

    /**
     * Constructor
     *
     * Setup the input/output, verify that we are in CLI mode and that something is requested
     *
     * @see self::runOptions()
     */
    public function __construct()
    {
        parent::__construct();
        if (empty($this->options) && empty($this->input)) {
            $this->error("No argument found - nothing to do!");
        }
        $this->runOption_config(MarkdownExtended::FULL_CONFIGFILE);
        $this->runOptions();
    }

// -------------------
// Options methods
// -------------------

    /**
     * Get the help string
     *
     * @return  void
     */
    public function runOption_help()
    {
        $class_name = MarkdownExtended::MDE_NAME;
        $class_version = MarkdownExtended::MDE_VERSION;
        $class_sources = MarkdownExtended::MDE_SOURCES;
        $help_str = <<<EOT
[ {$class_name} {$class_version} - CLI interface ]

Converts markdown-extended syntax text(s) source(s) from specified file(s) (or STDIN).
The rendering can be the full parsed content or just a part of this content.
By default, result is written through STDOUT in HTML format.

To transform a file content, write its path as script argument. To process a list of input
files, just write file paths as arguments, separated by space.

To transform a string read from STDIN, write it as last argument between double-quotes or EOF.
You can also use the output of a previous command using the pipe notation.

Usage:
    markdown-extended  [OPTIONS ...]  [INPUT FILE(S) OR STRING(S)]

    echo "*Markdown* __content__" | markdown-extended  [OPTIONS ...]

Options:
    --version (-V)             get script's version information
    --help (-h)                get this help information
    --verbose (-v)             increase script's verbosity
    --quiet (-q)               decrease script's verbosity (do not write Markdown Parser or PHP error messages)
    --multi (-m)               multi-files input (automatic if multiple file names found)
    --output (-o)    = FILE    specify a file (or a file mask) to write generated content in
    --config (-c)    = FILE    configuration file to use (INI format)
    --format (-f)    = NAME    format of the output (default is HTML)
    --extract (-e)  [= META]   extract some data (the meta data array by default) from the input
    --template (-t) [= FILE]   load the content in a template file (configuration template by default)
    --gamuts (-g)   [= NAME]   get the list of gamuts (or just one if specified) processed on input
    --nofilter (-n)  = A,B     specify a list of filters that will be ignored during parsing
    --debug (-x)               special flag for dev

Aliases:
    --body (-b)                get only the body part from parsed content (alias of '-e=body')
    --simple (-s)              use the simple pre-defined configuration file ; preset for input fields

For a full manual, try `man ./path/to/markdown-extended.man` if the file exists ;
if it doesn't, you can try option `--man` of this script to generate it if possible.

More information at <{$class_sources}>.
EOT;
        $this->write($help_str);
        $this->endRun();
        exit(0);
    }

    /**
     * Run the version option
     *
     * @return  void
     */
    public function runOption_version()
    {
        $info = MDE_Helper::smallInfo(false, $this->quiet);
        $git_ok = $this->exec("which git");
        $git_dir = getcwd() . '/.git';
        if (!empty($git_ok) && file_exists($git_dir) && is_dir($git_dir)) {
            $remote = $this->exec("git config --get remote.origin.url");
            if (!empty($remote) && (
                strstr($remote, MarkdownExtended::MDE_SOURCES) ||
                strstr($remote, str_replace('http', 'https', MarkdownExtended::MDE_SOURCES))
            )) {
                $versions = $this->exec("git rev-parse --abbrev-ref HEAD && git rev-parse HEAD && git log -1 --format='%ci' --date=short | cut -s -f 1 -d ' '");
                if (!empty($versions)) {
                    $info .= PHP_EOL.implode(' ', $versions);
                }
            }
        }
        $this->write($info);
        $this->endRun();
        exit(0);
    }

    /**
     * Run the manual option
     *
     * @return  void
     */
    public function runOption_man()
    {
        $info = '';
        $man_ok = $this->exec("which man");
        $man_path = getcwd() . '/bin/markdown-extended.man';
        if (!empty($man_ok)) {
            if (!file_exists($man_path)) {
                $ok = $this->exec("php bin/markdown-extended -f man -o bin/markdown-extended.man docs/MANPAGE.md");
            }
            if (file_exists($man_path)) {
                $info = 'OK, you can now run "man ./bin/markdown-extended.man"';
            } else {
                $info = 'Can not launch "man" command, file not found or command not accessible ... Try to run "man ./bin/markdown-extended.man".';
            }
        }
        $this->write($info);
        $this->endRun();
        exit(0);
    }

    /**
     * Run the multi option
     *
     * @return  void
     */
    public function runOption_multi()
    {
        $this->multi = true;
        $this->info("Enabling 'multi' input mode");
    }

    /**
     * Run the output option
     *
     * @param   string  $file   The command line option argument
     * @return  void
     */
    public function runOption_output($file)
    {
        $this->output = $file;
        $this->info("Setting output to `$this->output`, parsed content will be written in file(s)");
    }

    /**
     * Run the config file option
     *
     * @param   string  $file   The command line option argument
     * @return  void
     */
    protected function runOption_config($file)
    {
        $this->config = $file;
        $this->info("Setting configuration file to `$this->config`");
    }

    /**
     * Run the HTML filter option
     *
     * @return  void
     */
    public function runOption_filter_html()
    {
        $this->filter_html = true;
        $this->info("Enabling HTML filter, all HTML will be parsed");
    }

    /**
     * Run the styles filter option
     *
     * @return  void
     */
    public function runOption_filter_styles()
    {
        $this->filter_styles = true;
        $this->info("Enabling HTML styles filter, will try to parse styles");
    }

    /**
     * Run the extract option

     * @param   string  $type   The command line option argument
     * @return  void
     */
    public function runOption_extract($type)
    {
        if (empty($type)) $type = 'meta';
        if (!array_key_exists($type, self::$extract_presets)) {
            $this->error("Unknown extract option '$type'!");
        }
        $this->extract = $type;
        $this->info("Setting 'extract' to `$this->extract`, only this part will be extracted");
    }

    /**
     * Run the template option

     * @param   string  $file   The command line option argument
     * @return  void
     */
    public function runOption_template($file)
    {
        if (empty($file)) $file = true;
        $this->template = $file;
        if (true===$this->template) {
            $this->info("Setting 'template' to default, content will be loaded in a template file");
        } else {
            $this->info("Setting 'template' to `$this->template`, content will be loaded in a template file");
        }
    }

    /**
     * Run the no-filter option
     *
     * @param   string  $str    The command line option argument
     * @return  void
     */
    public function runOption_nofilter($str)
    {
        $this->nofilter = explode(',', $str);
        $this->info("Setting 'nofilter' to `".join(', ', $this->nofilter)."`, these will be ignored during parsing");
    }

    /**
     * Run the format option
     *
     * @param   string  $str    The command line option argument
     * @return  void
     */
    public function runOption_format($str)
    {
        $this->format = $str;
        $this->info("Setting parser format to `".$this->format."`");
    }

    /**
     * Run the gamuts option : list gamuts pile of the parser
     *
     * @param   string  $name   The command line option argument
     * @return  void
     */
    protected function runOption_gamuts($name = null)
    {
        $_emd = $this->getMdeInstance();
        if (empty($name)) {
            $this->info("Getting lists of Gamuts from Markdown parser with current config");
        } else {
            $this->info("Getting '$name' list of Gamuts from Markdown parser with current config");
        }
        $str='';
        $gamuts = array();
        if (!empty($name)) {
            $gamuts[$name] = MarkdownExtended::getConfig($name);
            if (empty($gamuts[$name])) {
                unset($gamuts[$name]);
                $name .= '_gamut';
                $gamuts[$name] = MarkdownExtended::getConfig($name);
                if (empty($gamuts[$name])) {
                    unset($gamuts[$name]);
                    if ($this->verbose===true) {
                        $this->error("Unknown Gamut '$name'!");
                    }
                }
            }
        } else {
            $gamuts['initial_gamut'] = MarkdownExtended::getConfig('initial_gamut');
            $gamuts['transform_gamut'] = MarkdownExtended::getConfig('transform_gamut');
            $gamuts['document_gamut'] = MarkdownExtended::getConfig('document_gamut');
            $gamuts['span_gamut'] = MarkdownExtended::getConfig('span_gamut');
            $gamuts['block_gamut'] = MarkdownExtended::getConfig('block_gamut');
        }
        if (!empty($gamuts)) {
            $str = $this->_renderOutput($gamuts);
        } else {
            $this->info('Empty gamuts stack');
        }
        $this->write($str);
        $this->endRun();
        exit(0);
    }

    /**
     * Run the 'body' alias
     *
     * @return  void
     */
    public function runOption_body()
    {
        $this->runOption_extract('body');
    }

    /**
     * Run the 'simple' alias
     *
     * @return  void
     */
    public function runOption_simple()
    {
        $this->runOption_config(MarkdownExtended::SIMPLE_CONFIGFILE);
    }

// -------------------
// CLI methods
// -------------------

    /**
     * Run the command line options of the request
     *
     * @return  void
     */
    protected function runOptions()
    {
        parent::runOptions();
        if (!empty($this->input)) {
            if (count($this->input)>1 && $this->multi!==true) {
                $this->runOption_multi();
            }
            if ($this->multi===true) {
                $this->info("Multi-input is set to `".join('`, `', $this->input)."`");
            } else {
                $this->info("Input is set to `{$this->input[0]}`");
            }
        }
    }

    /**
     * Run the whole script depending on options set
     *
     * @return  void
     */
    public function run()
    {
        $this->info(PHP_EOL.">>>> let's go for the parsing ...".PHP_EOL, true, false);
        if (!empty($this->input)) {
            if ($this->multi===true) {
                $myoutput = $this->output;
                foreach ($this->input as $_input) {
                    if (!empty($this->output) && count($this->input)>1) {
                        $this->output = $this->_buildOutputFilename($myoutput);
                    }
                    $_ok = $this->runStoryOnOneFile($_input, true);
                }
                $this->separator();
            } else {
                $_ok = $this->runStoryOnOneFile($this->input[0]);
            }
        } else {
            $this->error("No input markdown file or string entered!");
        }
        $this->info(PHP_EOL.">>>> the parsing is complete.".PHP_EOL, true, false);
        $this->endRun(1);
    }

    /**
     * Run the MDE process on one file or input
     *
     * @param   string  $input
     * @param   bool    $title  Set on `true` in case of multi-input
     * @return  string
     */
    public function runStoryOnOneFile($input, $title = false)
    {
        if ($this->extract!==false) {
            $infos = $this->runOneFile($input, null, $this->extract, $title);
            if ($this->verbose===true) {
                $this->endRun(false, "Infos extracted from input `$input`"
                    .(is_string($this->extract) ? " for tag `$this->extract`" : '')
                    .' : '.PHP_EOL.$infos);
            } else {
                $this->endRun(false, $infos, false);
            }
            return $infos;
        } elseif (!empty($this->output)) {
            $fsize = $this->runOneFile($input, $this->output, null, $title);
            if ($this->quiet!==true)
                $this->endRun(0, "OK - File `$this->output` ($fsize) written with parsed content from file `$input`");
            return $fsize;
        } else {
            $clength = $this->runOneFile($input, null, null, $title);
            return $clength;
        }
    }

    /**
     * Actually run the MDE process on a file or string
     *
     * @param   string  $input
     * @param   string  $output     An optional output filename to write result in
     * @param   bool    $extract    An extractor tagname
     * @param   bool    $title      Set to `true` to add title string in case of multi-input
     * @return  string
     */
    public function runOneFile($input, $output = null, $extract = null, $title = false)
    {
        $return=null;
        if (!empty($input)) {
            $num = self::$parsedfiles_counter;
            $this->separator();
            $this->info( "[$num] >> parsing file `$input`" );
            if ($md_content = $this->getInput($input, $title)) {
                if (!is_null($extract)) {
                    $return = $this->extractContent($md_content, $extract);
                } else {
                    $md_parsed_content = $this->parseContent($md_content);
                    if (!empty($output)) {
                        $return = $this->writeOutputFile($md_parsed_content, $output);
                    } else {
                        $return = $this->writeOutput($md_parsed_content);
                    }
                }
            }
            self::$parsedfiles_counter++;
        }
        return $return;
    }

// -------------------
// Process
// -------------------

    /**
     * Use of the PHP Markdown Extended class as a singleton
     *
     * @param   array   $config
     * @return  void
     */
    protected function getMdeInstance(array $config = array())
    {
        $config['skip_filters'] = $this->nofilter;
        if (false!==$this->config) {
            $config['config_file'] = $this->config;
        }
        if (!empty($this->format)) {
            $config['output_format'] = $this->format;
        }           
        return parent::getMdeInstance($config);
    }
    
    /**
     * Creates a `\MarkdownExtended\API\ContentInterface` object from filename or string
     *
     * @param   string  $input
     * @param   bool    $title  Set to `true` to add title string in case of multi-input
     * @return  \MarkdownExtended\API\ContentInterface
     * @throws  any caught exception
     */
    public function getInput($input, $title = false)
    {
        $md_content=null;
        if (!empty($input)) {
            if (@file_exists($input)) {
                $this->info("Loading input file `$input` ... ");
                if ($title===true) {
                    $this->writeInputTitle($input);
                }
                try {
                    $md_content = MDE_API::factory('Content', array(null, $input));
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
            } elseif (!empty($input) && is_string($input)) {
                $this->info("Loading Markdown string from STDIN [strlen: ".strlen($input)."] ... ");
                if ($title===true) {
                    $this->writeInputTitle('STDIN input');
                }
                try {
                    $md_content = MDE_API::factory('Content', array($input));
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
            } else {
                $this->error("Entered input seems to be neither a file (not found) nor a well-formed string!");
            }
        }
        return $md_content;
    }

    /**
     * Process a Content parsing
     *
     * @param   \MarkdownExtended\API\ContentInterface  $md_content
     * @return  string
     * @throws  any caught exception
     */
    public function parseContent(\MarkdownExtended\API\ContentInterface $md_content)
    {
        $md_output=null;
        if (!empty($md_content)) {
            $_emd = $this->getMdeInstance();
            $this->info("Parsing Mardkown content ... ", false);
            try {
                if (!empty($this->template)) {
                    if (is_string($this->template)) {
                        $_emd->setConfig('template', true, 'templater');
                        $_emd->setConfig('user_template', $this->template, 'templater');
                    }
                    $parser = $_emd->get('Parser');
                    $md_output = $parser
                        ->parse($md_content)
                        ->getContent();
                    $mde_tpl = $_emd->getTemplater();
                    $md_output = $mde_tpl->parse()->__toString();
                } else {
                    $parser = $_emd->get('Parser');
                    $md_output = $parser
                        ->parse($md_content)
                        ->getFullContent();
                }
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
            if ($md_output) {
                $this->md_parsed_content .= $md_output;
                $this->info("OK", true, false);
            } else {
                $this->error("An error occurred while trying to parse Markdown content ! (try to run `cd dir/to/markdown-extended ...`)");
            }
        }
        return $md_output;
    }

    /**
     * Process a Content parsing just for special gamuts
     *
     * @param   \MarkdownExtended\API\ContentInterface   $md_content
     * @param   string                      $extract
     * @return  string
     * @throws  any caught exception
     */
    public function extractContent(\MarkdownExtended\API\ContentInterface $md_content, $extract)
    {
        $md_output = '';
        $preset = self::$extract_presets[$extract];
        if (!empty($preset) && !empty($md_content)) {
            $options = array();
            if (!empty($preset['gamuts'])) {
                $options['special_gamut'] = $preset['gamuts'];
            }
            $_emd = $this->getMdeInstance($options);
            $this->info("Extracting Mardkown $extract ... ", false);
            try {
                $parser = $_emd->get('Parser');
                $md_content_parsed = $parser
                    ->parse($md_content)
                    ->getContent();
                $output = call_user_func(
                    array($md_content_parsed, ucfirst($preset['getter']))
                );
                $md_output = $this->_renderOutput($output);
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
            if ($output) {
                if (is_string($output)) {
                    $length = strlen($output);
                } elseif (is_array($output)) {
                    $length = count($output);
                }
                $this->info("OK [entries: ".$length."]", true, false);
            } else {
                $this->error("An error occurred while trying to extract data form Markdown content ! (try to run `cd dir/to/markdown-extended ...`)");
            }
        }
        return $md_output;
    }

}

// Endfile
