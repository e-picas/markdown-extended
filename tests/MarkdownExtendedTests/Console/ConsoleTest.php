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

use MarkdownExtendedTests\ConsoleTestCase;
use MarkdownExtendedCli\Console;

/**
 * @group console
 */
class ConsoleTest extends ConsoleTestCase
{
    /**
     * Validates the command runner of this class
     */
    public function testPhpCli()
    {
        $res = $this->runCommand('php -r "echo \'TEST\';"');
        $this->assertEquals(
            'TEST',
            $res['stdout'],
            '[system] test if the PHP CLI is working'
        );
    }

    /**
     * Test with no argument
     *
     * @runInSeparateProcess
     * @covers MarkdownExtendedCli\Console::run()
     */
    public function testNoArgument()
    {
        $res = $this->runCommand($this->getBaseCmd());
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            'Test of CLI with no argument (status)'
        );
        // stdout
        $this->assertEquals(
            'usage',
            substr(trim($res['stdout']), 0, strlen('usage')),
            '[console] test if a call with no argument outputs something like "usage..."'
        );
    }

    /**
     * Test of the manpage generation
     *
     * @runInSeparateProcess
     */
    public function testManpage()
    {
        $original = $this->getPath(array($this->getBasePath(), 'doc', 'MANPAGE.md'));
        $target = $this->getPath(array($this->getBasePath(), 'man', 'markdown-extended.3.man'));
        $res = $this->runCommand($this->getBaseCmd().' --format man '.$original);
        $this->assertEquals(
            $this->stripWhitespaces(file_get_contents($target)),
            trim($res['stdout']),
            '[console] test of the section 3 manpage generation with the CLI'
        );
    }
}
