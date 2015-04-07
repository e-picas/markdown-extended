<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Console;

use \MarkdownExtendedTests\ConsoleTest;

class ConsoleUsageTest
    extends ConsoleTest
{


    /**
     * Test a default call with no argument
     *
     * @runInSeparateProcess
     */
    public function testNoArgument()
    {
        $res = $this->runCommand($this->getBaseCmd());

        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'CLI with no argument status failure'
        );

    }

    /**
     * Test a default call with no argument
     *
     * @runInSeparateProcess
     */
    public function testSimpleString()
    {

        $res = $this->runCommand($this->getBaseCmd().' "my **markdown** _extended_ simple string"');

        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'CLI on simple string status failure'
        );

        // stdout
        $this->assertEquals(
            $res['stdout'],
            'my <strong>markdown</strong> <em>extended</em> simple string',
            'CLI on simple string failure'
        );

    }

}
