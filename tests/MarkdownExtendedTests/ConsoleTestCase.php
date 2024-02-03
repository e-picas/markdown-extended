<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

class ConsoleTestCase extends ParserTestCase
{

    /**
     * Gets the basic 'php bin/markdown-extended' string
     *
     * We force here a default timezone to avoid PHP errors while
     * using internal `DateTime` objects.
     *
     * @return string
     */
    public function getBaseCmd()
    {
        return 'php -d date.timezone=UTC ' . $this->getPath(['.', 'bin', 'markdown-extended']);
    }

    /**
     * Run a command and returns its result as
     *
     * @param string $command The command to run
     * @param string $path The path to go to
     *
     * @return array An array like ( stdout , status , stderr )
     */
    public function runCommand($command, $path = null, $env = [])
    {
        if (is_null($path)) {
            $path = $this->getBasePath();
        }

        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $pipes = [];

        $resource = proc_open($command, $descriptors, $pipes, $path, $env);
        if (!is_resource($resource)) {
            return false;
        }

        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        foreach ($pipes as $pipe) {
            fclose($pipe);
        }
        $status = proc_close($resource);

        /*/
        echo PHP_EOL . "running cmd: " . var_export($command,true);
        echo PHP_EOL . "result: " . var_export(array(
                'stdout' => trim($stdout, PHP_EOL),
                'status' => $status,
                'stderr' => trim($stderr, PHP_EOL)
            ),true);
        //*/
        return [
            'stdout' => trim($stdout, PHP_EOL),
            'status' => $status,
            'stderr' => trim($stderr, PHP_EOL),
        ];
    }
}
