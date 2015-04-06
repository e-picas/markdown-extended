<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedDev;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Console\AbstractConsole;
use \MarkdownExtended\Console\UserInput;
use \MarkdownExtended\Util\Helper;

class Console
    extends AbstractConsole
{

    public function __construct()
    {
        parent::__construct();
        $script = basename($this->script_path);
        $this
            ->setName('MDE dev tools')
            ->setSynopsis(
                $script . ' [OPTIONS] make-phar / check-phar'
            )
            ->setUsage(<<<MSG
Dev tasks:
    make-phar       : build or rebuild the PHAR of the app
                      ("markdown-extended.phar" in the current directory by default)
    check-phar      : extract current PHAR contents in a directory
                      (in a "tmp/phar-extract" directory by default)
    make-manpage-3  : rebuild the "man/markdown-extended.3.man" manpage
                      (default source is "doc/MANPAGE.md")
    make-manpage-7  : rebuild the "man/markdown-extended.7.man" manpage
                      (default source is "doc/DOCUMENTATION.md")
    make-manpages   : rebuild both manpages

You can also call Composer's scripts:
    composer test               : run PHPUnit test suite
    composer code-coverage      : play the code coverage analysis

MSG
            )
            ->addCliOption('output', array(
                'shortcut'      => 'o',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING,
                'description'   => 'Write the result of current task in concerned path.'
            ))
            ->addCliOption('input', array(
                'shortcut'      => 'i',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_PATH,
                'description'   => 'Set concerned path as input for current task.'
            ))
            ->addCliOption('base-path', array(
                'shortcut'      => 'b',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_PATH,
                'description'   => 'Set the base path for the CLI work.'
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

        // the task to run
        $task = array_shift($this->arguments);
        $this->stream->debug('Task(s) to run:' . Helper::debug($task, null, false));
        $this->stream->debug('User options:' . Helper::debug($this->options, null, false));

        // base path ?
        $base_path = $this->getOption('base-path');
        if (!empty($base_path)) {
            if (!file_exists($base_path) || !is_dir($base_path)) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid base path "%s" (no existing or not a directory)', $base_path)
                );
            }
            chdir($base_path);
        }
        if (!file_exists('src/MarkdownExtended/MarkdownExtended.php')) {
            throw new \RuntimeException(
                'You MUST run this script from the repository base path (file "src/MarkdownExtended/MarkdownExtended.php" not found)'
            );
        }

        // actually run task
        try {
            switch ($task) {
                case 'make-phar':
                    $this->makePhar();
                    break;
                case 'check-phar':
                    $this->checkPhar();
                    break;
                case 'make-manpage-3':
                    $this->makeManpage3();
                    break;
                case 'make-manpage-7':
                    $this->makeManpage7();
                    break;
                case 'make-manpages':
                    $this->makeManpage3();
                    $this->makeManpage7();
                    break;
                default:
                    throw new \InvalidArgumentException(
                        sprintf('Unknown task "%s"', $task)
                    );
            }

        } catch (\Exception $e) {
            echo 'Error: ['.get_class($e).'] '
                .$e->getMessage().' at '.$e->getFile().':'.$e->getLine();
            $this->stream->_exit(1);
        }

        $this->stream->_exit();
    }

    protected function makePhar()
    {
        $output = $this->getOption('output');
        if (empty($output)) {
            $output = Compiler::PHAR_FILE;
        }

        $this->stream->verboseln(
            sprintf('Calling the compiler to generate PHAR in "%s" ...', $output)
        );

        $compiler   = new Compiler();
        $logs       = $compiler->compile($output, getcwd());
        $this->stream->writeln(
            sprintf('> ok, phar generated in file "%s"', $output)
        );
        $this->stream->debug(array('Generation logs:', var_export($logs, true)));
    }

    protected function checkPhar()
    {
        $output = $this->getOption('output');
        if (empty($output)) {
            $output = 'tmp/phar-extract';
        }

        $input = $this->getOption('input');
        if (empty($input)) {
            $input = Compiler::PHAR_FILE;
        }
        if (!file_exists($input)) {
            throw new \InvalidArgumentException(
                sprintf('PHAR "%s" not found', $input)
            );
        }

        if (file_exists($output)) {
            $this->stream->verboseln(
                sprintf('Removing existing "%s" ...', $output)
            );
            exec("rm -rf $output");
        }
        exec("mkdir -p $output");

        $this->stream->verboseln(
            sprintf('Extracting "%s" to "%s" ...', $input, $output)
        );
        $phar = new \Phar($input);
        $phar->extractTo($output);
        $this->stream->writeln(
            sprintf('> ok, PHAR "%s" extracted to "%s"', $input, $output)
        );
    }

    protected function makeManpage3()
    {
        $this->makeManpage('doc/MANPAGE.md', 'man/markdown-extended.3.man');
    }

    protected function makeManpage7()
    {
        $this->makeManpage('doc/DOCUMENTATION.md', 'man/markdown-extended.7.man');
    }

    protected function makeManpage($default_input, $default_output)
    {
        $output = $this->getOption('output');
        if (empty($output)) {
            $output = $default_output;
        }

        $input = $this->getOption('input');
        if (empty($input)) {
            $input = $default_input;
        }
        if (!file_exists($input)) {
            throw new \InvalidArgumentException(
                sprintf('Manpage source "%s" not found', $input)
            );
        }

        if (file_exists($output)) {
            $this->stream->verboseln(
                sprintf('Removing existing "%s" ...', $output)
            );
            exec("rm -f $output");
        }

        $this->stream->verboseln(
            sprintf('Generating "%s" from "%s" ...', $output, $input)
        );
        $mde = new MarkdownExtended(array(
            'output_format' => 'man',
            'output'        => $output
        ));
        $this->stream->writeln(
            $mde->transformSource($input)
        );
    }

}
