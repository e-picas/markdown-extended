<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Console;

use \MarkdownExtendedTests\ConsoleTest;

class TasksTest
    extends ConsoleTest
{
    /**
     * Test task LICENSE
     *
     * @runInSeparateProcess
     */
    public function testLicense()
    {
        $res = $this->runCommand($this->getBaseCmd().' license');
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI license task (status)'
        );
        // output
        $this->assertNotEmpty(
            $res['stdout'],
            'Test of the CLI license task (output not empty)'
        );
    }

    /**
     * Test task manifest
     *
     * @runInSeparateProcess
     */
    public function testManifest()
    {
        $res = $this->runCommand($this->getBaseCmd().' manifest');
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI manifest task (status)'
        );
        // output
        $this->assertNotEmpty(
            $res['stdout'],
            'Test of the CLI manifest task (output not empty)'
        );
    }

    /**
     * Test task config-list
     *
     * @runInSeparateProcess
     */
    public function testConfigList()
    {
        $res = $this->runCommand($this->getBaseCmd().' config-list');
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI config-list task (status)'
        );
        // output
        $this->assertNotEmpty(
            $res['stdout'],
            'Test of the CLI config-list task (output not empty)'
        );
    }

    /**
     * Test task filters-list
     *
     * @runInSeparateProcess
     */
    public function testFiltersList()
    {
        $res = $this->runCommand($this->getBaseCmd().' filters-list');
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI filters-list task (status)'
        );
        // output
        $this->assertNotEmpty(
            $res['stdout'],
            'Test of the CLI filters-list task (output not empty)'
        );
    }
}
