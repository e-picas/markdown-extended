<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Compatibility;

use \MarkdownExtendedTests\ParserTest;

class OldInterfaceTest
    extends ParserTest
{

    /**
     * Get the `markdown.php` file path
     *
     * @return  string
     */
    public function getOldInterfaceFilepath()
    {
        return $this->getPath(array(
             $this->getBasePath(), 'src', 'markdown.php'
        ));
    }

    /**
     * Test the `Markdown()` and `MarkdownFromSource()` aliases
     */
    public function testCreate()
    {
        // file exists
        $this->assertFileExists(
            $this->getOldInterfaceFilepath(),
            'markdown.php file existence for compatibility'
        );
        require_once $this->getOldInterfaceFilepath();

        // Markdown
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->getFileExpectedBody_test()),
            $this->stripWhitespaceAndNewLines(Markdown(file_get_contents($this->getTestFilepath()))->getBody()),
            'Test of the Markdown() function'
        );

        // MarkdownFromSource
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->getFileExpectedBody_test()),
            $this->stripWhitespaceAndNewLines(MarkdownFromSource($this->getTestFilepath())->getBody()),
            'Test of the MarkdownFromSource() function'
        );
    }
}
