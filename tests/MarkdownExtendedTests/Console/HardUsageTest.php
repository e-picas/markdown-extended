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

use MarkdownExtendedTests\ConsoleTest;

class HardUsageTest extends ConsoleTest
{
    /**
     * Test with no argument
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
            'Test of CLI with no argument (status)'
        );
        // stdout
        $this->assertEquals(
            substr(trim($res['stdout']), 0, strlen('usage')),
            'usage',
            'Test of CLI with no argument'
        );
    }

    /**
     * Test of the manpage generation
     *
     * @runInSeparateProcess
    public function testManpage()
    {
    $original = $this->getPath(array($this->getBasePath(), 'doc', 'MANPAGE.md'));
    $target = $this->getPath(array($this->getBasePath(), 'man', 'markdown-extended.3.man'));
    $res = $this->runCommand($this->getBaseCmd().' --format man '.$original);
    var_export($res);
    $this->assertEquals(
    trim($res['stdout']),
    $this->stripWhitespaces(file_get_contents($target)),
    'Test of the section 3 manpage generation with the CLI'
    );
    }
     */
}
