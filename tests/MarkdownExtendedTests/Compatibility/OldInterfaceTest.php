<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Compatibility;

use MarkdownExtendedTests\ParserTestCase;

/**
 * @group compatibilty
 */
class OldInterfaceTest extends ParserTestCase
{
    /**
     * Get the `markdown.php` file path
     *
     * @return  string
     */
    public function getOldInterfaceFilepath()
    {
        return $this->getPath([
             $this->getBasePath(), 'src', 'markdown.php',
        ]);
    }

    /**
     * Test the `Markdown()` and `MarkdownFromSource()` aliases
     */
    public function testCompatibilityFileExists()
    {
        // file exists
        $this->assertFileExists(
            $this->getOldInterfaceFilepath(),
            'markdown.php file existence for compatibility'
        );
    }

    /**
     * Test the `Markdown()` and `MarkdownFromSource()` aliases
     */
    public function testOldMarkdownFunction()
    {
        require_once $this->getOldInterfaceFilepath();

        // Markdown
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->getFileExpectedBody_test()),
            $this->stripWhitespaceAndNewLines(Markdown(file_get_contents($this->getTestFilepath()))->getBody()),
            'Test of the Markdown() function'
        );
    }

    /**
     * Test the `Markdown()` and `MarkdownFromSource()` aliases
     */
    public function testOldMarkdownFromSourceFunction()
    {
        require_once $this->getOldInterfaceFilepath();

        // MarkdownFromSource
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->getFileExpectedBody_test()),
            $this->stripWhitespaceAndNewLines(MarkdownFromSource($this->getTestFilepath())->getBody()),
            'Test of the MarkdownFromSource() function'
        );
    }
}
