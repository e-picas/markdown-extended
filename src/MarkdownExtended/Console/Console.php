<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Console;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Parser;
use \MarkdownExtended\Util\Helper;

/**
 * This is the markdown-extended shell script definition
 *
 * It is called by the `bin/markdown-extended` script and
 * defines it whole process.
 */
class Console
    extends AbstractConsole
{
    protected $mde = array();
    protected $results = array();

    /**
     * Initialized the command
     */
    public function __construct()
    {
        parent::__construct();

        $script     = basename($this->script_path);
        $link       = MarkdownExtended::LINK;

        $this
            ->setName(MarkdownExtended::NAME)
            ->setShortname(MarkdownExtended::SHORTNAME)
            ->setVersion(MarkdownExtended::VERSION)
            ->setDescription(MarkdownExtended::DESC)
            ->setUsage(<<<DESC
This program converts markdown-extended syntax text(s) source(s) from specified file(s)
(or STDIN). The rendering can be the full parsed content or just a part of this content.
By default, result is written through STDOUT in HTML format.

To transform a file content, write its path as script argument. To process a list of input
files, just write the concerned paths as arguments, separated by a space.

To transform a string read from STDIN, write it as last argument between double-quotes or EOF.
To process a list of input strings, just write them as arguments, separated by a space.
You can also use the output of a previous command with the pipe notation.

Examples:
    {$script} [options ...] input_filename [input_filename] [...]
    {$script} [options ...] "markdown string read from STDIN"
    echo "*Markdown* __content__" | {$script} [options ...]

Additionally, you can call a special task running: `{$script} <task_name>`
Available tasks are:
    license         : read the full LICENSE of the application
    manifest        : read the full application manifest
    config-list     : dump current configuration settings list
    filters-list    : list runtime filters for current configuration

More information at <{$link}>.
DESC
            )
            ->setSynopsis(
                $script . ' [OPTIONS] "**markdown** _string_" [... string / file path]'
            )
            ->setShortVersionString(MarkdownExtended::getAppInfo(true))
            ->setLongVersionString(
                implode(PHP_EOL, MarkdownExtended::getAppInfo())
            )
            ->addCliOption('output', array(
                'shortcut'      => 'o',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING,
                'negate'        => true,
                'description'   => 'Write the result in concerned path(s).'
            ))
            ->addCliOption('config', array(
                'shortcut'      => 'c',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING,
                'description'   => 'Define a configuration file to over-write defaults.'
            ))
            ->addCliOption('format', array(
                'shortcut'      => 'f',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING,
                'default'       => 'html',
                'description'   => 'Define the final format to use ("HTML" by default).'
            ))
            ->addCliOption('extract', array(
                'shortcut'      => 'e',
                'argument'      => UserInput::ARG_OPTIONAL,
                'type'          => UserInput::TYPE_STRING,
                'default'       => false,
                'default_arg'   => 'metadata',
                'description'   => 'Extract only a part of parsed content ("metadata" by default).'
            ))
            ->addCliOption('template', array(
                'shortcut'      => 't',
                'argument'      => UserInput::ARG_OPTIONAL,
                'type'          => UserInput::TYPE_BOOL | UserInput::TYPE_PATH,
                'negate'        => true,
                'default'       => 'auto',
                'default_arg'   => true,
                'description'   => 'Define a template to load parsed content in (without argument, the default template will be used).'
            ))
            ->addCliOption('response', array(
                'shortcut'      => 'r',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING | UserInput::TYPE_LISTITEM,
                'default'       => 'plain',
                'list'          => array( 'plain', 'json', 'php', 'dump' ),
                'description'   => 'Define the response format in "plain" (default), "json" or "php".'
            ))
            ->addCliOption('force', array(
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Force some actions (i.e. does not create file backup).'
            ))
        ;

        $this
            ->initCommonOptions()
            ->parseOptions()
        ;
    }

    /**
     * Gets markdown parser with current options
     *
     * @return array|\MarkdownExtended\Parser
     */
    public function getMarkdownExtendedParser()
    {
        // create the MDE instance if needed
        if (empty($this->mde)) {
            $this->mde = new Parser($this->getMarkdownExtendedOptions());
        }
        return $this->mde;
    }

    /**
     * Gets rendering results
     *
     * @param bool $as_array
     * @return array
     */
    public function getResults($as_array = false)
    {
        $results = $this->results;
        if ($as_array) {
            $item_callback = function (&$item) {
                /* @var $item \MarkdownExtended\API\ContentInterface */
                return $item = (is_object($item) && Kernel::valid($item, Kernel::TYPE_CONTENT) ?
                    array_filter($item->__toArray()) : $item);
            };
            array_walk($results, $item_callback);
        }
        return $results;
    }

    /**
     * Constructs the options to pass to the parser from cli's options
     *
     * @return array
     */
    protected function getMarkdownExtendedOptions()
    {
        $mde_data = $this->options;

        // strip unused options
        foreach (array('help', 'version', 'debug', 'quiet', 'verbose', 'extract', 'response') as $n) {
            unset($mde_data[$n]);
        }

        // format <-> output_format
        $mde_data['output_format'] = $mde_data['format'];
        unset($mde_data['format']);

        // config <-> config_file
        $mde_data['config_file'] = $mde_data['config'];
        unset($mde_data['config']);

        // be sure to have a batch output name for multi-arguments
        if (count($this->arguments)>1 && $mde_data['output']) {
            $output = $mde_data['output'];
            if (false === strpos($output, '%%')) {
                $ext = pathinfo($output, PATHINFO_EXTENSION);
                $mde_data['output'] = substr($output, 0, -(strlen($ext) + 1)) . '-%%.' . $ext;
            }
        }

        // template negation
        if (isset($mde_data['no-template'])) {
            $mde_data['template'] = null;
            unset($mde_data['no-template']);
        }

        return $mde_data;
    }

    /**
     * Actually run the command process
     */
    public function run()
    {
        // common options
        $this->runCommonOptions();
        if (Stream::VERBOSITY_DEBUG <= $this->stream->getVerbosity()) {
            error_log(get_called_class() . ' :: starting a new run in debug mode');
        }

        // hard debug info
        $this->stream->debug(array('CLI available options:', Helper::debug($this->cli_options, null, false)));
        $this->stream->debug(array('User options:', Helper::debug($this->options, null, false)));
        $this->stream->debug(array('User arguments:', Helper::debug($this->arguments, null, false)));

        // a special task?
        if (count($this->arguments) === 1) {
            $args = $this->arguments;
            $task_method = 'runTask' . Helper::toCamelCase(str_replace('-', '_', array_shift($args)));
            if (method_exists($this, $task_method)) {
                call_user_func(array($this, $task_method));
                $this->stream->_exit();
            }
        }

        // parse arguments and render results
        $this
            ->parseArguments()
            ->renderOutput();
    }

    /**
     * Actually process arguments with markdown transformation
     *
     * @return $this
     */
    protected function parseArguments()
    {
        // treat arguments one by one
        $counter = 1;
        foreach ($this->arguments as $i=>$input) {
            if (false === strpos($input, PHP_EOL) && file_exists($input)) {
                $this->results[$input] =
                    $this->getMarkdownExtendedParser()->transformSource($input);
            } else {
                $this->results['STDIN#' . $counter] =
                    $this->getMarkdownExtendedParser()->transform($input, $counter);
                $counter++;
            }
        }

        // extraction?
        if (false !== $this->getOption('extract')) {
            $extract = $this->getOption('extract');
            if (!is_string($extract)) {
                $extract = 'metadata';
            }
            foreach ($this->results as $i=>$item) {
                $method = 'get' . Helper::toCamelCase($extract);
                if (method_exists($item, $method)) {
                    $this->results[$i] = call_user_func(array($item, $method));
                } else {
                    $this->results[$i] = call_user_func(array($item, 'getMetadata'), $extract);
                }
            }
        }

        return $this;
    }

    /**
     * Renders the process' output
     */
    protected function renderOutput()
    {
        switch ($this->getOption('response')) {
            case 'json':
                $this->renderOutputJson();
                break;
            case 'php':
                $this->renderOutputPhp();
                break;
            case 'dump':
                if ($this->stream->getVerbosity() === Stream::VERBOSITY_DEBUG) {
                    $this->renderOutputDump();
                }
            default:
            case 'plain':
                $this->renderOutputPlain();
        }
    }

    /**
     * Renders results in plain text format
     */
    protected function renderOutputPlain()
    {
        $results = $this->getResults();
        foreach ($results as $i=>$result) {
            if (count($results)>1) {
                $this->stream->writeln("==> $i <==");
            }
            $this->stream->write(Helper::getSafeString($result));
        }
        $this->stream->_exit();
    }

    /**
     * Renders results in JSON format
     */
    protected function renderOutputJson()
    {
        $results = Helper::getSafeArray($this->getResults(true));
        if (count($results)===1) {
            $result = array_shift($results);
            if ($this->stream->getVerbosity() === Stream::VERBOSITY_DEBUG && version_compare(PHP_VERSION, '5.4.0') >= 0) {
                $this->stream->write(
                    json_encode($result, JSON_PRETTY_PRINT)
                );
            } else {
                $this->stream->write(
                    json_encode($result)
                );
            }
            $this->stream->_exit();
        }
        if ($this->stream->getVerbosity() === Stream::VERBOSITY_DEBUG && version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $this->stream->write(
                json_encode($results, JSON_PRETTY_PRINT)
            );
        } else {
            $this->stream->write(
                json_encode($results)
            );
        }
        $this->stream->_exit();
    }

    /**
     * Renders results in PHP format
     */
    protected function renderOutputPhp()
    {
        $results = $this->getResults(true);
        if (count($results)===1) {
            $result = array_shift($results);
            $this->stream->write(
                serialize($result)
            );
            $this->stream->_exit();
        }
        $this->stream->write(
            serialize($results)
        );
        $this->stream->_exit();
    }

    /**
     * Dumps results (dev only)
     */
    protected function renderOutputDump()
    {
        $results = Helper::getSafeArray($this->getResults(true));
        if (count($results)===1) {
            $result = array_shift($results);
            $this->stream->write(
                var_export($result, true)
            );
            $this->stream->_exit();
        }
        $this->stream->write(
            var_export($results, true)
        );
        $this->stream->_exit();
    }

    /**
     * Dumps the LICENSE file
     */
    protected function runTaskLicense()
    {
        if (file_exists($license = getcwd() . DIRECTORY_SEPARATOR . self::LICENSE_FILE)) {
            $this->_writeTask(Helper::readFile($license), 'License');
        } else {
            $this->stream->writeln('LICENSE file not found', Stream::IO_STDERR);
        }
    }

    /**
     * Dumps the composer.json file
     */
    protected function runTaskManifest()
    {
        if (file_exists($manifest = getcwd() . DIRECTORY_SEPARATOR . self::MANIFEST_FILE)) {
            $content = json_decode(Helper::readFile($manifest), true);

            foreach (array('extra', 'autoload', 'autoload-dev', 'config', 'scripts', 'archive') as $entry) {
                if (isset($content[$entry])) {
                    unset($content[$entry]);
                }
            }

            if (isset($content['keywords']) && is_array($content['keywords'])) {
                $content['keywords'] = implode(',', $content['keywords']);
            }

            if (isset($content['authors'])) {
                foreach ($content['authors'] as $i => $author) {
                    if (is_array($author)) {
                        $content['authors'][$i] = implode(', ', $author);
                    }
                }
            }

            $this->_writeTask($content, 'Manifest');
        } else {
            $this->stream->writeln('Manifest file not found', Stream::IO_STDERR);
        }
    }

    /**
     * Lists runtime filters
     */
    protected function runTaskFiltersList()
    {
        // create the MDE instance
        $mde = $this->getMarkdownExtendedParser();

        $loader = Kernel::get('GamutLoader');

        $gamuts = array();
        foreach (Kernel::get('config')->getAll() as $var=>$val) {
            if ($loader->isGamutStackName($var)) {
                $gamuts[$var] = $val;
            }
        }
        array_walk($gamuts, function (&$item) {
            $item = array_flip($item);
        });

        $this->_writeTask($gamuts, 'Runtime filters');
    }

    /**
     * Lists runtime configuration settings
     */
    protected function runTaskConfigList()
    {
        // create the MDE instance
        $mde = $this->getMarkdownExtendedParser();

        $loader = Kernel::get('GamutLoader');

        $config = array();
        foreach (Kernel::get('config')->getAll() as $var=>$val) {
            if ($loader->isGamutStackName($var)) {
                continue;
            }
            if (is_null($val)) {
                $config[$var] = 'NULL';
            } elseif (is_bool($val)) {
                $config[$var] = $val===true ? 'true' : 'false';
            } elseif (is_callable($val)) {
                $config[$var] = 'function()';
            } elseif (is_array($val)) {
                foreach ($val as $subvar=>$subval) {
                    $config[$var . '.' . $subvar] = $subval;
                }
            } else {
                $config[$var] = $val;
            }
        }

//        var_export($config);
        $this->_writeTask($config, 'Configuration settings');
    }

    // actually write a task result with a title and current app's info
    private function _writeTask($content, $title)
    {
        $this->stream->writeln(
            (!empty($title) ? $title . ' - ' : ''). $this->short_version_str
        );
        $this->stream->writeln('----');
        if (is_array($content)) {
            $this->stream->writetable($content);
        } else {
            $this->stream->writeln($content);
        }
        $this->stream->writeln('----');
    }
}
