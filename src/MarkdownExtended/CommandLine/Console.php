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
 * Command line controller/interface for MarkdownExtended
 */
class Console extends AbstractConsole
{

    /**
     * @var \MarkdownExtended\MarkdownExtended
     */
    protected static $emd_instance;

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
    /**#@-*/

    /**
     * Command line options
     */
    static $cli_options = array(
        'v'=>'version', 
        'h'=>'help', 
        'x'=>'verbose', 
        'q'=>'quiet', 
        'o:'=>'output:', 
        'm'=>'multi', 
        'c:'=>'config:', 
        'f:'=>'format:', 
        'gamuts::', 
//      'filter-html', 
//      'filter-styles', 
        'nofilter:', 
        'extract::'
    );

    /**
     * @static array
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
     * Setup the input/output, verify that we are in CLI mode and that something is requested
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

    /**
     * Run the command line options of the request
     */
    protected function runOptions()
    {
        parent::runOptions();
        if (!empty($this->input)) {
            if (count($this->input)>0 && $this->multi!==true) {
                $this->multi = true;
            }
            if ($this->multi===true) {
                $this->info("Input files are setted on `".join(', ', $this->input)."`");
            } else {
                $this->info("Input file is setted on `{$this->input[0]}`");
            }
        }
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
        if (!empty($this->input)) {
            if ($this->multi===true) {
                $myoutput = $this->output;
                foreach ($this->input as $_input) {
                    if (!empty($this->output)) {
                        $this->output = $this->_buildOutputFilename( $myoutput );
                    }
                    $_ok = $this->runStoryOnOneFile($_input);
                }
                $this->separator();
            } else {
                $_ok = $this->runStoryOnOneFile($this->input[0]);
            }
        } else {
            $this->error("No input markdown file entered!");
        }
        $this->info(PHP_EOL.">>>> the parsing is complete.".PHP_EOL, true, false);
        $this->endRun(1);
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
[ {$class_name} {$class_version} - CLI interface ]

Converts text(s) in specified file(s) (or stdin) from markdown syntax source(s).
The rendering can be the full parsed content or just a part of this content.
By default, result is written through stdout in HTML format.

Usage:
    ~$ php path/to/markdown_extended [OPTIONS ...] [INPUT FILE(S) OR STRING(S)]

Options:
    -v | --version          get Markdown version information
    -h | --help             get this help information
    -x | --verbose          increase verbosity of the script
    -q | --quiet            do not write Markdown Parser or PHP error messages
    -m | --multi            multi-files input (automatic if multiple file names found)
    -o | --output=FILE      specify a file (or a file mask) to write generated content in
    -c | --config=FILE      configuration file to use for Markdown instance (INI format)
    -f | --format=NAME      format of the output (default is HTML)
    --gamuts[=NAME]         get the list of gamuts (or just one if specified) processed on Markdown input
    --nofilter=A,B          specify a list of filters that will be ignored during Markdown parsing
    --extract[=META]        extract some data (the meta data array by default) from the Markdown input

More infos at <{$class_sources}>
EOT;
        $this->write($help_str);
        $this->endRun();
        exit(0);
    }

    /**
     * Run the version option
     */
    public function runOption_version()
    {
        $this->write(MDE_Helper::info());
        $this->endRun();
        exit(0);
    }

    /**
     * Run the multi option
     */
    public function runOption_multi()
    {
        $this->multi = true;
        $this->info("Enabling 'multi' input mode");
    }

    /**
     * Run the output option
     */
    public function runOption_output($file)
    {
        $this->output = $file;
        $this->info("Setting 'output' on `$this->output`, parsed content will be written in file(s)");
    }

    /**
     * Run the config file option
     */
    protected function runOption_config($file)
    {
        $this->config = $file;
        $this->info("Setting Markdown config file on `$this->config`");
    }

    /**
     * Run the HTML filter option
     */
    public function runOption_filter_html()
    {
        $this->filter_html = true;
        $this->info("Enabling HTML filter, all HTML will be parsed");
    }

    /**
     * Run the styles filter option
     */
    public function runOption_filter_styles()
    {
        $this->filter_styles = true;
        $this->info("Enabling HTML styles filter, will try to parse styles");
    }

    /**
     * Run the extract option
     */
    public function runOption_extract($type)
    {
        if (empty($type)) $type = 'meta';
        if (!array_key_exists($type, self::$extract_presets)) {
            $this->error("Unknown extract option '$type'!");
        }
        $this->extract = $type;
        $this->info("Setting 'extract' on `$this->extract`, only this part will be extracted");
    }

    /**
     * Run the no-filter option
     */
    public function runOption_nofilter($str)
    {
        $this->nofilter = explode(',', $str);
        $this->info("Setting 'nofilter' on `".join(', ', $this->nofilter)."`, these will be ignored during parsing");
    }

    /**
     * Run the format option
     */
    public function runOption_format($str)
    {
        $this->format = $str;
        $this->info("Setting 'format' on `".$this->format."`");
    }

    /**
     * Run the gamuts option : list gamuts pile of the parser
     */
    protected function runOption_gamuts($name = null)
    {
        $_emd = $this->getEmdInstance();
        if (empty($name)) {
            $this->info("Getting lists of Gamuts from Markdown parser with current config");
        } else {
            $this->info("Getting $name list of Gamuts from Markdown parser with current config");
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
                    $this->error("Unknown Gamut '$name'!");
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

// -------------------
// Process
// -------------------

    /**
     * Use of the PHP Markdown Extended class as a singleton
     */
    protected function getEmdInstance(array $config = array())
    {
        if (empty(self::$emd_instance)) {
            $config['skip_filters'] = $this->nofilter;
            if (false!==$this->config) {
                $config['config_file'] = $this->config;
            }
            if (!empty($this->format)) {
                $config['output_format'] = $this->format;
            }           
            $this->info("Creating a MarkdownExtended instance with options ["
                .str_replace("\n", '', var_export($_options,1))
                ."]");
            self::$emd_instance = MarkdownExtended::create()
                ->get('Parser', $config);
        }
        return self::$emd_instance;
    }
    
    public function runStoryOnOneFile($input)
    {
        if ($this->extract!==false) {
            $infos = $this->runOneFile($input, null, $this->extract);
            if ($this->quiet!==true) {
                $this->endRun(false, "Infos extracted from input `$input`"
                    .(is_string($this->extract) ? " for tag `$this->extract`" : '')
                    .' : '.PHP_EOL.$infos);
            } else {
                $this->endRun(false, $infos, false);
            }
            return $infos;
        } elseif (!empty($this->output)) {
            $fsize = $this->runOneFile($input, $this->output);
            if ($this->quiet!==true)
                $this->endRun(0, "OK - File `$this->output` ($fsize) written with parsed content from file `$input`");
            return $fsize;
        } else {
            $clength = $this->runOneFile( $this->input[0] );
            return $clength;
        }
    }

    public function runOneFile($input, $output = null, $extract = null)
    {
        $return=null;
        if (!empty($input)) {
            $num = self::$parsedfiles_counter;
            $this->separator();
            $this->info( "[$num] >> parsing file `$input`" );
            if ($md_content = self::getInput($input)) {
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

    public function getInput($input)
    {
        $md_content=null;
        if (!empty($input)) {
            if (@file_exists($input)) {
                $this->info("Reading input file `$input` ... ", false);
                if ($md_content = @file_get_contents( $input )) {
                    $this->md_content .= $md_content;
                    $this->info("OK [strlen: ".strlen($md_content)."]", true, false);
                } else {
                    $this->error("Could not open input file `$input`!");
                }
            } else {
                $this->error("Entered input markdown file `$input` not found!");
            }
        }
        return $md_content;
    }

    public function parseContent($md_content)
    {
        $md_output=null;
        if (!empty($md_content)) {
            $_emd = $this->getEmdInstance();
            $this->info("Parsing Mardkown content ... ", false);
            if ($md_content = $_emd->parse(new \MarkdownExtended\Content($md_content))) {
                $md_output = $md_content->getFullContent($md_content_object);
                $this->md_parsed_content .= $md_output;
                $this->info("OK [strlen: ".strlen($md_output)."]", true, false);
            } else {
                $this->error("An error occured while trying to parse Markdown content ! (try to run `cd dir/to/markdown_extended ...`)");
            }
        }
        return $md_output;
    }

    public function extractContent($md_content, $extract)
    {
        $md_output = '';
        $preset = self::$extract_presets[$extract];
        if (!empty($preset) && !empty($md_content)) {
            $options = array();
            if (!empty($preset['gamuts'])) {
                $options['special_gamut'] = $preset['gamuts'];
            }
            $_emd = $this->getEmdInstance($options);
            $this->info("Extracting Mardkown $extract ... ", false);
            if ($ok = $_emd->parse(
                new \MarkdownExtended\Content($md_content)
            )) {
                $output = call_user_func(
                    array(MarkdownExtended::getInstance()->getContent(),
                        ucfirst($preset['getter']))
                );
                $md_output = $this->_renderOutput($output);
                if (is_string($output)) {
                    $length = strlen($output);
                } elseif (is_array($output)) {
                    $length = count($output);
                }
                $this->info("OK [entries: ".$length."]", true, false);
            } else {
                $this->error("An error occured while trying to extract data form Markdown content ! (try to run `cd dir/to/markdown_extended ...`)");
            }
        }
        return $md_output;
    }

    public function writeOutputFile($output, $output_file)
    {
        $fsize=null;
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

// ----------------------
// Utilities
// ----------------------

    protected function _buildOutputFilename($filename)
    {
        $ext = strrchr($filename, '.');
        $_f = str_replace($ext, '', $filename);
        return $_f.'_'.self::$parsedfiles_counter.$ext;
    }

    /**
     * Writes an output safely for STDOUT (string or arrays)
     *
     * @param misc $content
     * @param int $indent internal indentation flag
     * @return string
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
                    .($indent>0 ? str_repeat('    ', $indent) : '')
                    .str_pad($var, $max_length, ' ').' : '
                    .$this->_renderOutput($val, ($indent+1));
            }
        }
        return ($indent===0 ? trim($text, PHP_EOL) : $text);
    }

}

// Endfile
