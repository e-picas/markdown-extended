<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Console;

abstract class AbstractConsole
{
    public const LICENSE_FILE              = 'LICENSE';

    public const MANIFEST_FILE             = 'composer.json';

    /**
     * @var null|string
     */
    protected $name                 = null;

    /**
     * @var null|string
     */
    protected $short_name           = null;

    /**
     * @var null|string
     */
    protected $version              = null;

    /**
     * @var null|string
     */
    protected $description          = null;

    /**
     * @var null|string
     */
    protected $short_version_str    = null;

    /**
     * @var null|string
     */
    protected $long_version_str     = null;

    /**
     * @var null|string
     */
    protected $usage                = null;

    /**
     * @var null|string
     */
    protected $synopsis             = null;

    /**
     * @var null|string
     */
    protected $script_path          = null;

    /**
     * @var array
     */
    protected $cli_options          = [];

    /**
     * @var array
     */
    protected $options              = [];

    /**
     * @var array
     */
    protected $arguments            = [];

    /**
     * @var \MarkdownExtended\Console\Stream
     */
    protected $stream;

    /**
     * @var \MarkdownExtended\Console\UserInput
     */
    protected $user_input;

    /**
     * @var bool
     */
    protected $arg_required;

    /**
     * Initializes a new console app
     */
    public function __construct()
    {
        // current script path
        $argv = UserInput::getSanitizedUserInput();
        if (!empty($argv) && is_array($argv) && isset($argv[0])) {
            $this->script_path = realpath($argv[0]);
        } else {
            $this->script_path = basename($_SERVER['SCRIPT_FILENAME']);
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
                $this->script_path . ' [options] <arguments>'
            )
        ;
    }

    /**
     * Sets console's stream handler
     *
     * @param \MarkdownExtended\Console\Stream $stream
     *
     * @return $this
     */
    public function setStream(Stream $stream)
    {
        $this->stream = $stream;
        $this->stream->setExceptionHandlerCallback([$this, 'runUsage']);
        return $this;
    }

    /**
     * Sets console's user input handler
     *
     * @param \MarkdownExtended\Console\UserInput $input
     *
     * @return $this
     */
    public function setUserInput(UserInput $input)
    {
        $this->user_input = $input;
        return $this;
    }

    /**
     * Defines if an argument is required or not
     *
     * If it is required, the `self::runCommonOptions()` method
     * will show the usage string without argument.
     *
     * @param bool $arg_required
     *
     * @return $this
     */
    public function setArgumentRequired($arg_required)
    {
        $this->arg_required = (bool) $arg_required;
        return $this;
    }

    /**
     * Sets the command's name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Sets the command's short-name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setShortname($name)
    {
        $this->short_name = $name;
        if (function_exists('cli_set_process_title')) {
            // the @ is for a PHP bug: "cli_set_process_title had an error: Not initialized correctly"
            @cli_set_process_title($this->short_name);
        } elseif (function_exists('setproctitle')) {
            setproctitle($this->short_name);
        }
        return $this;
    }

    /**
     * Sets the command's version number
     *
     * @param string $version
     *
     * @return $this
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Sets the command's short version string
     *
     * This is rendered when running `cmd --version --quiet`
     *
     * @param string $str
     *
     * @return $this
     */
    public function setShortVersionString($str)
    {
        $this->short_version_str = $str;
        return $this;
    }

    /**
     * Sets the command's long version string
     *
     * This is rendered when running `cmd --version`
     *
     * @param string $str
     *
     * @return $this
     */
    public function setLongVersionString($str)
    {
        $this->long_version_str = $str;
        return $this;
    }

    /**
     * Sets the command's description
     *
     * @param string $str
     *
     * @return $this
     */
    public function setDescription($str)
    {
        $this->description = $str;
        return $this;
    }

    /**
     * Sets the command's usage string
     *
     * @param string $str
     *
     * @return $this
     */
    public function setUsage($str)
    {
        $this->usage = $str;
        return $this;
    }

    /**
     * Sets the command's synopsis
     *
     * @param string $str
     *
     * @return $this
     */
    public function setSynopsis($str)
    {
        $this->synopsis = $str;
        return $this;
    }

    /**
     * Adds a new CLI option available for the command
     *
     * See the `\MarkdownExtended\Console\UserOption`
     * method for a full review of what `$opt` can contain.
     *
     * @param string $name
     * @param array $opt
     *
     * @return $this
     */
    public function addCliOption($name, array $opt)
    {
        $this->cli_options[$name] = $opt;
        return $this;
    }

    /**
     * Gets a script's option value
     *
     * @param string $name
     * @param null $default
     * @return null|mixed
     */
    public function getOption($name, $default = null)
    {
        return $this->options[$name] ?? $default;
    }

    /**
     * Enables hard debugging
     */
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
        if (empty($this->user_input)) {
            $this->setUserInput(new UserInput($this->cli_options));
        }
        $user_input         = $this->user_input->parseOptions();
        $this->options      = $user_input->options;
        $this->arguments    = $user_input->remain;

        $piped = $this->stream->getPipedInput();
        if (!empty($piped)) {
            $this->arguments[] = trim($piped, " \n");
        }

        return $this;
    }

    /**
     * Runs the `--version` option
     */
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

    /**
     * Runs the `--help` option
     */
    public function runHelp()
    {
        $help_info = [
            $this->name . ' - ' . $this->short_name . '@' . $this->version,
            '',
            'Usage:',
            Stream::PADDER . $this->synopsis,
            '',
            'Options:',
            $this->user_input->getOptionsInfo(),
            '',
            $this->usage,
        ];

        foreach ($help_info as $line) {
            if (is_string($line)) {
                $this->stream->writeln($line);
            } elseif (!empty($line)) {
                $this->stream->writetable($line);
            }
        }
    }

    /**
     * Runs the usage string
     */
    public function runUsage()
    {
        $help_info = [
            str_pad('usage:', strlen(str_repeat(Stream::PADDER, 2)), ' ')  . $this->synopsis,
        ];

        $usage_options  = $this->user_input->getOptionsSynopsis();
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

    /**
     * Actually run command's process
     */
    abstract public function run();

    /**
     * Load common options to command's sepcific ones
     */
    protected function initCommonOptions()
    {
        $this
            ->addCliOption('verbose', [
                'shortcut'      => 'v',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Increase script\'s verbosity.',
            ])
            ->addCliOption('quiet', [
                'shortcut'      => 'q',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Decrease script\'s verbosity.',
            ])
            ->addCliOption('debug', [
                'shortcut'      => 'x',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Drastically increase script\'s verbosity (for development).',
            ])
            ->addCliOption('version', [
                'shortcut'      => 'V',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Get script\'s current version.',
            ])
            ->addCliOption('help', [
                'shortcut'      => 'h',
                'argument'      => UserInput::ARG_NULL,
                'description'   => 'Get script\'s help information.',
            ])
        ;
        return $this;
    }

    /**
     * Runs common options if used
     */
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
            $this->stream->_exit();
        }
        // version string
        if ($this->getOption('version')) {
            $this->runVersion();
            $this->stream->_exit();
        }
        // no argument = usage
        if ($this->arg_required === true && empty($this->arguments)) {
            $this->runUsage();
            $this->stream->_exit();
        }

        return $this;
    }
}
