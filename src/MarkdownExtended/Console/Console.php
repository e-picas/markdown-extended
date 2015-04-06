<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Console;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Util\Helper;

class Console
    extends AbstractConsole
{

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
files, just write file paths as arguments, separated by space.

To transform a string read from STDIN, write it as last argument between double-quotes or EOF.
You can also use the output of a previous command using the pipe notation.

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
                'list'          => array( 'plain', 'json', 'php' ),
                'description'   => 'Define the response format in "plain" (default), "json" or "php".'
            ))
            ->addCliOption('force', array(
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Force some actions (no created files backup).'
            ))
        ;

        $this
            ->initCommonOptions()
            ->parseOptions()
        ;
    }

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

        // create the MDE instance
        $mde = new MarkdownExtended($this->getMarkdownExtendedOptions());

        // a special task?
        if (count($this->arguments) === 1) {
            $args = $this->arguments;
            $task_method = 'runTask' . Helper::toCamelCase(str_replace('-', '_', array_shift($args)));
            if (method_exists($this, $task_method)) {
                call_user_func(array($this, $task_method));
                exit(0);
            }
        }

        // treat arguments one by one
        $results = array();
        $counter = 1;
        foreach ($this->arguments as $i=>$input) {
            if (false === strpos($input, PHP_EOL) && file_exists($input)) {
                $results[$input] = $mde->transformSource($input);
            } else {
                $results['STDIN#' . $counter] = $mde->transform($input, $counter);
                $counter++;
            }
        }

        // extraction?
        if (false !== $this->getOption('extract')) {
            $extract = $this->getOption('extract');
            if (!is_string($extract)) {
                $extract = 'metadata';
            }
            foreach ($results as $i=>$item) {
                $method = 'get' . Helper::toCamelCase($extract);
                if (method_exists($item, $method)) {
                    $results[$i] = call_user_func(array($item, $method));
                } else {
                    $results[$i] = call_user_func(array($item, 'getMetadata'), $extract);
                }
            }
        }

        $this->renderOutput($results);
    }

    protected function getMarkdownExtendedOptions()
    {
        $mde_data = $this->options;

        // strip unused options
        foreach (array('help', 'version','debug','quiet','verbose','extract','response') as $n) {
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

    protected function renderOutput(array $results)
    {
        $item_callback = function(&$item) {
            return $item = (is_object($item) && Kernel::valid($item, Kernel::TYPE_CONTENT) ?
                array_filter($item->__toArray()) : $item);
        };

        // JSON output
        if ($this->getOption('response')==='json') {
            if (count($results)===1) {
                $this->stream->write(
                    json_encode($item_callback(array_shift($results)))
                );
                exit(0);
            }
            array_walk($results, $item_callback);
            $this->stream->write(
                json_encode($results)
            );
            exit(0);
        }

        // PHP output
        if ($this->getOption('response')==='php') {
            if (count($results)===1) {
                $this->stream->write(
                    serialize($item_callback(array_shift($results)))
                );
                exit(0);
            }
            array_walk($results, $item_callback);
            $this->stream->write(
                serialize($results)
            );
            exit(0);
        }

        // plain output
        foreach ($results as $i=>$result) {
            if (count($results)>1) {
                $this->stream->writeln("==> $i <==");
            }
            $this->stream->write(Helper::getSafeString($result));
        }

    }

    // dump LICENSE file
    protected function runTaskLicense()
    {
        if (file_exists($license = getcwd() . DIRECTORY_SEPARATOR . self::LICENSE_FILE)) {
            $this->_writeTask(file_get_contents($license), 'License');
        } else {
            $this->stream->writeln('LICENSE file not found', Stream::IO_STDERR);
        }
    }

    // dump composer.json file
    protected function runTaskManifest()
    {
        if (file_exists($manifest = getcwd() . DIRECTORY_SEPARATOR . self::MANIFEST_FILE)) {
            $content = json_decode(file_get_contents($manifest), true);

            foreach (array('extra', 'autoload', 'config', 'scripts') as $entry) {
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

    // list runtime filters
    protected function runTaskFiltersList()
    {
        $loader = Kernel::get('Grammar\GamutLoader');

        $gamuts = array();
        foreach (Kernel::get('config')->getAll() as $var=>$val) {
            if ($loader->isGamutStackName($var)) {
                $gamuts[$var] = $val;
            }
        }
        array_walk($gamuts, function(&$item) {
            $item = array_flip($item);
        });

        $this->_writeTask($gamuts, 'Runtime filters');
    }

    // list runtime config
    protected function runTaskConfigList()
    {
        $loader = Kernel::get('Grammar\GamutLoader');

        $config = array();
        foreach (Kernel::get('config')->getAll() as $var=>$val) {
            if (!$loader->isGamutStackName($var)) {
                if (is_null($val)) {
                    $config[$var] = 'NULL';
                } elseif (is_bool($val)) {
                    $config[$var] = $val===true ? 'true' : 'false';
                } elseif (is_array($val)) {
                    foreach ($val as $subvar=>$subval) {
                        $config[$var . '.' . $subvar] = $subval;
                    }
                } else {
                    $config[$var] = $val;
                }
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
