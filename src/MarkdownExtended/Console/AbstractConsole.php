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

use MarkdownExtended\MarkdownExtended;

abstract class AbstractConsole
{

    const LICENSE_FILE              = 'LICENSE';
    const MANIFEST_FILE             = 'composer.json';

    protected $name                 = null;
    protected $short_name           = null;
    protected $version              = null;
    protected $description          = null;
    protected $short_version_str    = null;
    protected $long_version_str     = null;
    protected $usage                = null;
    protected $synopsis             = null;
    protected $script_path          = null;
    protected $cli_options          = array();
    protected $options              = array();
    protected $arguments            = array();
    protected $stream;
    protected $arg_required;

    public function __construct()
    {
        // current script path
        if (isset($_SERVER['argv']) && is_array($_SERVER['argv']) && isset($_SERVER['argv'][0])) {
            $this->script_path = realpath($_SERVER['argv'][0]);
        } else {
            $this->script_path = getcwd();
        }

        // default required info
        $this
            ->setStream(new Stream())
            ->setArgumentRequired(true)
            ->setName(get_class($this))
            ->setShortname(get_class($this))
            ->setVersion('?')
            ->setDescription('?')
            ->setSynopsis(
                $this->script_path . ' [OPTIONS] <arguments>'
            )
        ;
    }

    public function setStream(Stream $stream)
    {
        $this->stream = $stream;
        $this->stream->setExceptionHandlerCallback(array($this, 'runUsage'));
        return $this;
    }

    public function setArgumentRequired($arg_required)
    {
        $this->arg_required = (bool) $arg_required;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setShortname($name)
    {
        $this->short_name = $name;
        if (function_exists('cli_set_process_title')) {
            cli_set_process_title($this->short_name);
        } elseif (function_exists('setproctitle')) {
            setproctitle($this->short_name);
        }
        return $this;
    }

    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    public function setShortVersionString($str)
    {
        $this->short_version_str = $str;
        return $this;
    }

    public function setLongVersionString($str)
    {
        $this->long_version_str = $str;
        return $this;
    }

    public function setDescription($str)
    {
        $this->description = $str;
        return $this;
    }

    public function setUsage($str)
    {
        $this->usage = $str;
        return $this;
    }

    public function setSynopsis($str)
    {
        $this->synopsis = $str;
        return $this;
    }

    public function addCliOption($name, array $opt)
    {
        $this->cli_options[$name] = $opt;
        return $this;
    }

    public function getOption($name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    public function enableHardDebug()
    {
        $this->options['debug'] = true;
        error_log(get_called_class() . ' :: hard debug enabled');
    }

    /**
     * Get the command line user options
     *
     * @return  array   ( options , arguments )
     */
    public function parseOptions()
    {
        $user_input = UserInput::parseOptions($this->cli_options);
        $this->options = $user_input->options;
        $this->arguments = $user_input->remain;

        $piped = $this->stream->getPipedInput();
        if (!empty($piped)) {
            $this->arguments[] = trim($piped, " \n");
        }

        return $this;
    }

    public function runVersion()
    {
        if ($this->stream->getVerbosity() === Stream::VERBOSITY_QUIET) {
            $info = $this->short_version_str;
        } else {
            $info = $this->long_version_str;
        }
        if (empty($info)) {
            $info = $this->short_name . '@' . $this->version;
        }
        $this->stream->writeln($info);
    }

    public function runHelp()
    {
        $help_info = array(
            $this->name . ' - ' . $this->short_name . '@' . $this->version,
            '',
            'Usage:',
            Stream::PADDER . $this->synopsis,
            '',
            'Options:',
            UserInput::getOptionsInfo($this->cli_options),
            '',
            $this->usage,
        );

        foreach ($help_info as $line) {
            if (is_string($line)) {
                $this->stream->writeln($line);
            } elseif (!empty($line)) {
                $this->stream->writetable($line);
            }
        }
    }

    public function runUsage()
    {
        $help_info = array(
            str_pad('usage:', strlen(str_repeat(Stream::PADDER, 2)), ' ')  . $this->synopsis
        );

        $usage_options  = UserInput::getOptionsSynopsis($this->cli_options);
        $counter        = 0;
        $linelen        = 2;
        while ($counter < count($usage_options)) {
            $help_info[] = str_repeat(Stream::PADDER, 3) . implode(' ', array_slice($usage_options, $counter, $linelen));
            $counter = $counter + $linelen;
        }

        $help_info[] = 'Use option `--help` to get help.';

        foreach ($help_info as $line) {
            $this->stream->writeln($line);
        }
    }

    abstract function run();

    protected function initCommonOptions()
    {
        $this
            ->addCliOption('verbose', array(
                'shortcut'      => 'v',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Increase script\'s verbosity.'
            ))
            ->addCliOption('quiet', array(
                'shortcut'      => 'q',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Decrease script\'s verbosity.'
            ))
            ->addCliOption('debug', array(
                'shortcut'      => 'x',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Drastically increase script\'s verbosity (for development).'
            ))
            ->addCliOption('version', array(
                'shortcut'      => 'V',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Get script\'s current version.'
            ))
            ->addCliOption('help', array(
                'shortcut'      => 'h',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Get script\'s help information.'
            ))
        ;
        return $this;
    }

    protected function runCommonOptions()
    {
        // set verbosity
        if ($this->getOption('debug')) {
            $this->stream->setVerbosity(Stream::VERBOSITY_DEBUG);
        } elseif ($this->getOption('verbose')) {
            $this->stream->setVerbosity(Stream::VERBOSITY_VERBOSE);
        } elseif ($this->getOption('quiet')) {
            $this->stream->setVerbosity(Stream::VERBOSITY_QUIET);
        }

        // help string
        if ($this->getOption('help')) {
            $this->runHelp();
            exit(0);
        }
        // version string
        if ($this->getOption('version')) {
            $this->runVersion();
            exit(0);
        }
        // no argument = usage
        if ($this->arg_required===true && empty($this->arguments)) {
            $this->runUsage();
            exit(0);
        }

        return $this;
    }

}
