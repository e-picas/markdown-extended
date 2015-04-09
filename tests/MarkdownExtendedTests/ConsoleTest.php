<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

class ConsoleTest
    extends BaseUnitTest
{

    /**
     * Validates the command runner of this class
     */
    public function testCommandRunner()
    {
        $res = $this->runCommand('php -r "echo \'TEST\';"');
        $this->assertEquals(
            $res['stdout'],
            'TEST',
            '[internal test] command runner'
        );
    }

    /**
     * Gets the basic 'php bin/markdown-extended' string
     * @return string
     */
    public function getBaseCmd()
    {
        return 'php ' . $this->getPath(array('.', 'bin', 'markdown-extended'));
    }

    /**
     * Run a command and returns its result as
     *
     * @param string $command The command to run
     * @param string $path The path to go to
     *
     * @return array An array like ( stdout , status , stderr )
     */
    public function runCommand($command, $path = null, $env = array())
    {
        if (is_null($path)) {
            $path = $this->getBasePath();
        }

        $descriptors = array(
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        );
        $pipes = array();

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
        return array(
            'stdout' => trim($stdout, PHP_EOL),
            'status' => $status,
            'stderr' => trim($stderr, PHP_EOL)
        );
    }

}
