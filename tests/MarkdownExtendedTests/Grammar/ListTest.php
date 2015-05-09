<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use \MarkdownExtendedTests\ParserTest;
use \MarkdownExtended\MarkdownExtended;

class ListTest extends ParserTest
{

    public function testCreate()
    {

        // unordered list
        $md = <<<MSG
-   first item
*   second item
    - first sub-item
    * second sub-item
-   third item
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<ul><li>first item</li><li>second item  <ul><li>first sub-item</li><li>second sub-item</li></ul></li><li>third item</li></ul>',
            '[parsing] test of unordered list'
        );

        // ordered list
        $md = <<<MSG
1.   first item
1.   second item
    1. first sub-item
    2. second sub-item
5.   third item
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<ol><li>first item</li><li>second item  <ol><li>first sub-item</li><li>second sub-item</li></ol></li><li>third item</li></ol>',
            '[parsing] test of ordered list'
        );
    }
}
