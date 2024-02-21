<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use MarkdownExtendedTests\ParserTestCase;
use MarkdownExtended\MarkdownExtended;

class ListTest extends ParserTestCase
{
    public function testListUnordered()
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
            '<ul><li>first item</li><li>second item  <ul><li>first sub-item</li><li>second sub-item</li></ul></li><li>third item</li></ul>',
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '[parsing] test of unordered list'
        );
    }

    public function testListOrdered()
    {
        // ordered list
        $md = <<<MSG
1.   first item
1.   second item
    1. first sub-item
    2. second sub-item
5.   third item
MSG;
        $this->assertEquals(
            '<ol><li>first item</li><li>second item  <ol><li>first sub-item</li><li>second sub-item</li></ol></li><li>third item</li></ol>',
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '[parsing] test of ordered list'
        );
    }
}
