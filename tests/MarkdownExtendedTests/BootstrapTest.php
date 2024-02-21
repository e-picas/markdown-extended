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

class BootstrapTest extends ParserTestCase
{
    /**
     * Validates the paths getters of this class
     */
    public function testPathsExist()
    {
        $this->assertFileExists(
            $this->getPath([dirname(__DIR__), 'bootstrap.php']),
            '[internal test] getPath() to "tests/bootstrap.php"'
        );
        $this->assertFileExists(
            $this->getPath([$this->getBasePath(), 'composer.json']),
            '[internal test] getBasePath() to "composer.json"'
        );
        $this->assertFileExists(
            $this->getResourcePath('test-1.md'),
            '[internal test] getResourcePath() to "tests/test-1.md" as a string'
        );
        $this->assertFileExists(
            $this->getResourcePath('test-2.md'),
            '[internal test] getResourcePath() to "tests/test-2.md" as an array'
        );
    }
}
