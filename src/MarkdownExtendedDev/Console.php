<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedDev;

use MarkdownExtended\Console\AbstractConsole;
use MarkdownExtended\Console\UserInput;
use MarkdownExtended\Exception\FileSystemException;
use MarkdownExtended\Exception\InvalidArgumentException;
use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Parser;
use MarkdownExtended\Util\Helper;

class Console extends AbstractConsole
{
    public function __construct()
    {
        parent::__construct();
        $script = basename($this->script_path);
        $this
            ->setName('MDE dev tools')
            ->setSynopsis(
                $script . ' [OPTIONS] make-phar / check-phar / make-release / make-manpage-(3/7) / make-manpages'
            )
            ->setUsage(
                <<<MSG
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
    make-release    : increase version-number and prepare a release
                      (use the "--release" option to set the release number)

You can also call Composer's scripts:
    composer test               : run PHPUnit test suite
    composer code-coverage      : play the code coverage analysis

MSG
            )
            ->addCliOption('output', [
                'shortcut'      => 'o',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING,
                'description'   => 'Write the result of current task in concerned path.',
            ])
            ->addCliOption('input', [
                'shortcut'      => 'i',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_PATH,
                'description'   => 'Set concerned path as input for current task.',
            ])
            ->addCliOption('base-path', [
                'shortcut'      => 'b',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_PATH,
                'description'   => 'Set the base path for the CLI work.',
            ])
            ->addCliOption('release', [
                'shortcut'      => 'r',
                'argument'      => UserInput::ARG_REQUIRED,
                'type'          => UserInput::TYPE_STRING,
                'description'   => [
                    'Set a version number for the "make-release" action.',
                    '(you can use "major", "minor" and "patch" for automation ; default is "patch")',
                ],
            ])
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
                throw new FileSystemException(
                    sprintf('Invalid base path "%s" (no existing or not a directory)', $base_path)
                );
            }
            chdir($base_path);
        }
        if (!file_exists('src/MarkdownExtended/MarkdownExtended.php')) {
            throw new InvalidArgumentException(
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
                case 'make-release':
                    $this->makeRelease();
                    break;
                default:
                    throw new InvalidArgumentException(
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
        $this->stream->debug(['Generation logs:', var_export($logs, true)]);
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
            throw new FileSystemException(
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

    protected function makeRelease()
    {
        $target = $this->getOption('release');
        if (empty($target)) {
            $target = 'patch';
        }
        $target_parts = explode('-', $target);

        $actual = MarkdownExtended::VERSION;
        $this->stream->verboseln(
            sprintf('Actual version is "%s"', MarkdownExtended::VERSION)
        );
        $actual_parts = explode('-', $actual);

        [$major, $minor, $patch] = explode('.', $actual_parts[0]);
        $final = null;
        switch ($target) {
            case 'major':
                $major++;
                $minor = $patch = 0;
                break;
            case 'minor':
                $minor++;
                $patch = 0;
                break;
            case 'patch':
                $patch++;
                break;
                // if user sets a complete release string, take it as-is
            default:
                $final = $target_parts[0];
                $target_parts = $actual_parts = [];
        }


        if (is_null($final)) {
            $final = implode('.', [$major, $minor, $patch]);
        }
        if (count($target_parts) > 1) {
            $final .= '-' . $target_parts[1];
        } elseif (count($actual_parts) > 1) {
            $final .= '-' . $actual_parts[1];
        }
        $date = date('Y-m-d');

        $this->stream->writeln(
            sprintf('New version is "%s". You have 2 sec to cancel ("Ctrl+C") ...', $final)
        );
        sleep(2);

        // the MDE class file
        $md_class = realpath(Helper::getPath([
            dirname(__DIR__),
            'MarkdownExtended',
            'MarkdownExtended.php',
        ]));
        $this->stream->verboseln(
            sprintf('Updating "%s" ...', $md_class)
        );
        $content = Helper::readFile($md_class);
        $content = preg_replace(
            Helper::buildRegex('VERSION([ ]+)=([ ]+)\'' . $actual . '\';'),
            'VERSION$1=$2\'' . $final . '\';',
            $content
        );
        $content = preg_replace(
            Helper::buildRegex('DATE([ ]+)=([ ]+)\'\d{4}\-\d{2}-\d{2}\';'),
            'DATE$1=$2\'' . $date . '\';',
            $content
        );
        if (Helper::writeFile($md_class, $content, false)) {
            $this->stream->writeln($md_class);
        }

        // the section 3 manpage
        $man3 = realpath(Helper::getPath([
            dirname(dirname(__DIR__)),
            'doc',
            'MANPAGE.md',
        ]));
        $this->stream->verboseln(
            sprintf('Updating "%s" ...', $man3)
        );
        $content = Helper::readFile($man3);
        $content = preg_replace(
            Helper::buildRegex('Version:([ ]+)' . $actual),
            'Version:${1}' . $final,
            $content
        );
        $content = preg_replace(
            Helper::buildRegex('Date:([ ]+)\d{4}\-\d{2}-\d{2}'),
            'Date:${1}' . $date,
            $content
        );
        if (Helper::writeFile($man3, $content, false)) {
            $this->stream->writeln($man3);
        }

        // the section 7 manpage
        $man7 = realpath(Helper::getPath([
            dirname(dirname(__DIR__)),
            'doc',
            'DOCUMENTATION.md',
        ]));
        $this->stream->verboseln(
            sprintf('Updating "%s" ...', $man7)
        );
        $content = Helper::readFile($man7);
        $content = preg_replace(
            Helper::buildRegex('Version:([ ]+)' . $actual),
            'Version:${1}' . $final,
            $content
        );
        $content = preg_replace(
            Helper::buildRegex('Date:([ ]+)\d{4}\-\d{2}-\d{2}'),
            'Date:${1}' . $date,
            $content
        );
        if (Helper::writeFile($man7, $content, false)) {
            $this->stream->writeln($man7);
        }

        $this->makeManpage3();
        $this->makeManpage7();

        $this->stream->writeln(
            'OK, new release number and date updated ... you should now commit changes.'
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
            throw new FileSystemException(
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
        $mde = new Parser([
            'force'         => false,
            'output_format' => 'man',
            'output'        => $output,
        ]);
        $this->stream->writeln(
            $mde->transformSource($input)
        );
    }
}
