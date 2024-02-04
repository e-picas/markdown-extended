<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Console;

use MarkdownExtended\Exception\RuntimeException;
use MarkdownExtended\Exception\UnexpectedValueException;

/**
 * This class is a handler for terminal IO
 */
class Stream
{
    /**
     * Quiet verbosity flag
     */
    const VERBOSITY_QUIET   = 1;

    /**
     * Normal verbosity flag
     */
    const VERBOSITY_NORMAL  = 2;

    /**
     * Verbose verbosity flag
     */
    const VERBOSITY_VERBOSE = 4;

    /**
     * Debug verbosity flag
     */
    const VERBOSITY_DEBUG   = 8;

    /**
     * Verbosity level: one of the `VERBOSITY_` constants of the class
     *
     * @var int
     */
    protected $verbosity    = self::VERBOSITY_NORMAL;

    /**
     * Use this instead of written raw 'stdin'
     */
    const IO_STDIN          = 'stdin';

    /**
     * Use this instead of written raw 'stdout'
     */
    const IO_STDOUT         = 'stdout';

    /**
     * Use this instead of written raw 'stderr'
     */
    const IO_STDERR         = 'stderr';

    /**
     * Current STDIN
     *
     * @var resource
     */
    public $stdin;

    /**
     * Current STDOUT
     *
     * @var resource
     */
    public $stdout;

    /**
     * Current STDERR
     *
     * @var resource
     */
    public $stderr;

    /**
     * The exception handler callback
     *
     * @var callable
     */
    protected $exception_callback;

    const PADDER            = '    ';

    const VERBOSE_PREFIX    = '[V] ';

    const DEBUG_PREFIX      = '[D] ';

    /**
     * Initializes all streams
     */
    public function __construct()
    {
        set_exception_handler([$this, 'handleException']);
        $this
            ->setStream(self::IO_STDIN, fopen('php://stdin', 'w'))
            ->setStream(self::IO_STDOUT, fopen('php://stdout', 'w'))
            ->setStream(self::IO_STDERR, fopen('php://stderr', 'w'))
        ;
    }

    /**
     * Sets a stream resource by type in stdin, stdout or stderr
     *
     * @param   string      $type
     * @param   resource    $stream
     *
     * @return  $this
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if the stream is not a valid resource or has a wrong type
     */
    public function setStream($type, $stream)
    {
        if (!is_resource($stream) || 'stream' !== get_resource_type($stream)) {
            throw new UnexpectedValueException(
                sprintf('A "%s" console stream must be a resource (got "%s")', $type, gettype($stream))
            );
        }
        switch ($type) {
            case self::IO_STDIN:
                $this->stdin = $stream;
                break;
            case self::IO_STDOUT:
                $this->stdout = $stream;
                break;
            case self::IO_STDERR:
                $this->stderr = $stream;
                break;
            default:
                throw new UnexpectedValueException(
                    sprintf('Unknown stream type "%s"', $type)
                );
        }
        return $this;
    }

    /**
     * Gets a stream by type: stdin, stdout or stderr
     *
     * @param   string $type
     *
     * @return  resource
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if the type is not known
     */
    public function getStream($type)
    {
        switch ($type) {
            case self::IO_STDIN:
                return $this->stdin;
                break;
            case self::IO_STDOUT:
                return $this->stdout;
                break;
            case self::IO_STDERR:
                return $this->stderr;
                break;
            default:
                throw new UnexpectedValueException(
                    sprintf('Unknown stream type "%s"', $type)
                );
        }
    }

    /**
     * Sets a callback triggered when an exception is caught
     *
     * The callback is triggered like `callback( exception )`
     *
     * @param   callable $callback
     *
     * @return  $this
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if the argument is not a valid callback
     */
    public function setExceptionHandlerCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new UnexpectedValueException(
                sprintf('A handler callback must be callable (got "%s")', gettype($callback))
            );
        }
        $this->exception_callback = $callback;
        return $this;
    }

    /**
     * Sets stream verbosity flag
     *
     * @param int $int
     *
     * @return $this
     */
    public function setVerbosity($int)
    {
        $this->verbosity = $int;
        return $this;
    }

    /**
     * Gets stream verbosity flag
     *
     * @return int
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }

    /**
     * Actually exits current process
     *
     * @param int $code
     */
    public function _exit($code = 0)
    {
        exit($code);
    }

    /**
     * Write a caught exception
     *
     * @param   \Exception  $e
     * @return  void
     */
    public function handleException(\Exception $e)
    {
        if (self::VERBOSITY_DEBUG <= $this->getVerbosity()) {
            $str = sprintf(
                PHP_EOL . 'Caught "%s": %s'. PHP_EOL .'in file %s at line %d'
                . PHP_EOL . 'Stack trace:' . PHP_EOL . '%s' . PHP_EOL,
                get_class($e),
                $e->getMessage(),
                str_replace(
                    dirname(dirname(dirname(__DIR__))),
                    '',
                    $e->getFile()
                ),
                $e->getLine(),
                $e->getTraceAsString()
            );
        } else {
            $str = PHP_EOL . '!! > ' . $e->getMessage() . PHP_EOL;
        }
        $this->write($str, true, self::IO_STDERR);

        if (!is_null($this->exception_callback) && is_callable($this->exception_callback)) {
            call_user_func($this->exception_callback, $e);
        }

        $this->_exit($e->getCode() > 0 ? $e->getCode() : 1);
    }

    /**
     * Write an info to CLI output
     *
     * @param   string  $str        The information to write
     * @param   bool    $new_line   May we pass a line after writing the info
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     *
     * @return  void
     *
     * @throws \MarkdownExtended\Exception\UnexpectedValueException if the stream type can not be found
     * @throws \MarkdownExtended\Exception\RuntimeException if can not write in stream
     */
    public function write($str, $new_line = true, $stream = self::IO_STDOUT)
    {
        if (!property_exists($this, $stream)) {
            throw new UnexpectedValueException(
                sprintf('Unknown IO stream type "%s"', $stream)
            );
        }
        $stream_io = $this->getStream($stream);
        if (false === fwrite($stream_io, $str . ($new_line === true ? PHP_EOL : ''))) {
            throw new RuntimeException(
                sprintf('Can not write output to stream "%s"', $stream)
            );
        }
        fflush($stream_io);
    }

    /**
     * Write an info to CLI output with line break
     *
     * @param   string  $str        The information to write
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function writeln($str, $stream = self::IO_STDOUT)
    {
        $this->write($str, true, $stream);
    }

    /**
     * Write an info to CLI output with line break
     *
     * @param   array   $table      The table to write
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function writetable(array $table, $stream = self::IO_STDOUT)
    {
        $maxlen = 0;
        foreach (array_keys($table) as $index) {
            $maxlen = max($maxlen, strlen($index));
        }
        foreach ($table as $var => $val) {
            if (is_array($val)) {
                $counter        = 0;
                $lineBuilder    = function ($item, $key) use ($maxlen, $stream, $var, &$counter) {
                    $str = ' '
                        . str_pad(($counter === 0 ? $var : ''), $maxlen, ' ', STR_PAD_LEFT)
                        . self::PADDER
                        . (is_string($key) ? $key . ': ' : '')
                        . (string) $item;
                    $this->write($str, true, $stream);
                    $counter++;
                };
                array_walk($val, $lineBuilder);
            } else {
                $str = ' '
                    . str_pad((is_string($var) ? $var : ''), $maxlen, ' ', STR_PAD_LEFT)
                    . self::PADDER . $val;
                $this->write($str, true, $stream);
            }
        }
    }

    /**
     * Write an info to CLI output in verbose mode
     *
     * @param   string  $str        The information to write
     * @param   bool    $new_line   May we pass a line after writing the info
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function verbose($str, $new_line = true, $stream = self::IO_STDOUT)
    {
        if (self::VERBOSITY_VERBOSE <= $this->getVerbosity()) {
            $this->write(self::VERBOSE_PREFIX . $str, $new_line, $stream);
        }
    }

    /**
     * Write an info to CLI output with line break in verbose mode
     *
     * @param   string  $str        The information to write
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function verboseln($str, $stream = self::IO_STDOUT)
    {
        $this->verbose($str, true, $stream);
    }

    /**
     * Write an info to CLI output in debug mode
     *
     * @param   string|array    $stack      The information to write (can be an array or object)
     * @param   bool            $new_line   May we pass a line after writing the info
     * @param   string          $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function debug($stack, $new_line = true, $stream = self::IO_STDOUT)
    {
        if (self::VERBOSITY_DEBUG <= $this->getVerbosity()) {
            if (!is_array($stack)) {
                $stack = [$stack];
            }
            foreach ($stack as $item) {
                if (is_string($item)) {
                    $this->write(self::DEBUG_PREFIX . $item, $new_line, $stream);
                } else {
                    $this->write(var_export($item, true), $new_line, $stream);
                }
            }
        }
    }

    /**
     * Write an info to CLI output with line break in debug mode
     *
     * @param   string|array    $stack      The information to write (can be an array or object)
     * @param   string          $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function debugln($stack, $stream = self::IO_STDOUT)
    {
        $this->debug($stack, true, $stream);
    }

    /**
     * Get any output from previous command STDIN piped
     * see <http://stackoverflow.com/a/9711142/2512020>
     *
     * @return  string|null
     */
    public function getPipedInput()
    {
        $data   = '';
        $read   = [$this->stdin];
        $write  = [];
        $except = [];
        try {
            $result = stream_select($read, $write, $except, 0);
            if ($result !== false && $result > 0) {
                while (!feof($this->stdin)) {
                    $data .= fgets($this->stdin);
                }
            }
            /*            if (is_file($this->stdin)) {
                            file_put_contents($this->stdin, '');
                        }
            */
        } catch (\Exception $e) {
            $data = null;
        }
        return $data;
    }
}
