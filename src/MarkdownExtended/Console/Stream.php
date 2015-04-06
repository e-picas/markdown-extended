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


class Stream
{

    const VERBOSITY_QUIET   = 1;
    const VERBOSITY_NORMAL  = 2;
    const VERBOSITY_VERBOSE = 4;
    const VERBOSITY_DEBUG   = 8;

    /**
     * @var int
     */
    protected $verbosity    = self::VERBOSITY_NORMAL;

    const IO_STDIN          = 'stdin';
    const IO_STDOUT         = 'stdout';
    const IO_STDERR         = 'stderr';

    /**
     * @var resource
     */
    protected $stdin;

    /**
     * @var resource
     */
    protected $stdout;

    /**
     * @var resource
     */
    protected $stderr;

    /**
     * @var callable
     */
    protected $exception_callback;

    const PADDER            = '    ';
    const VERBOSE_PREFIX    = '[V] ';
    const DEBUG_PREFIX      = '[D] ';

    public function __construct()
    {
        set_exception_handler(array($this, 'handleException'));
        $this
            ->setStream(self::IO_STDIN,  defined('STDOUT') ? STDOUT : fopen('php://stdout', 'c+'))
            ->setStream(self::IO_STDOUT, defined('STDIN')  ? STDIN  : fopen('php://stdin', 'c+'))
            ->setStream(self::IO_STDERR, defined('STDERR') ? STDERR : fopen('php://stderr', 'c+'))
        ;
    }

    public function setStream($type, $stream)
    {
        if (!is_resource($stream)) {
            throw new \InvalidArgumentException(
                sprintf('A "%s" console stream must be a resource (got "%s")', $type, gettype($stream))
            );
        }
        $this->{$type} = $stream;
        return $this;
    }

    public function setExceptionHandlerCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new \InvalidArgumentException(
                sprintf('A handler callback must be callable (got "%s")', gettype($callback))
            );
        }
        $this->exception_callback = $callback;
        return $this;
    }

    public function setVerbosity($int)
    {
        $this->verbosity = $int;
        return $this;
    }

    public function getVerbosity()
    {
        return $this->verbosity;
    }

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
                    dirname(dirname(dirname(__DIR__))), '', $e->getFile()
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

        $this->_exit($e->getCode());
    }

    /**
     * Write an info to CLI output
     *
     * @param   string  $str        The information to write
     * @param   bool    $new_line   May we pass a line after writing the info
     * @param   string  $stream     The stream to write to (stdin, sdtout or stderr)
     * @return  void
     */
    public function write($str, $new_line = true, $stream = self::IO_STDOUT)
    {
        if (!property_exists($this, $stream)) {
            throw new \InvalidArgumentException(
                sprintf('Unknown IO stream type "%s"', $stream)
            );
        }
        fwrite($this->{$stream}, $str . ($new_line===true ? PHP_EOL : ''));
        fflush($this->{$stream});
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
        foreach ($table as $var=>$val) {
            if (is_array($val)) {
                $counter        = 0;
                $lineBuilder    = function($item, $key) use ($maxlen, $stream, $var, &$counter) {
                    $str = ' '
                        . str_pad(($counter===0 ? $var : ''), $maxlen, ' ', STR_PAD_LEFT)
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
                $stack = array($stack);
            }
            foreach ($stack as $item) {
                if (is_string($item)) {
                    $this->write(self::DEBUG_PREFIX . $item, $new_line, $stream);
                } else {
                    $this->write(var_export($item,true), $new_line, $stream);
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

}
