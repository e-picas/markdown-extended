<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

use MarkdownExtendedTests\ConsoleTestCase;

class ConsoleTest extends ConsoleTestCase
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

}
