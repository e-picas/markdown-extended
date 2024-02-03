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

class HeaderTest extends ParserTestCase
{
    public function testHeader()
    {
        $md = '#Hello World';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<h1 id="hello-world">Hello World</h1>',
            '[parsing] test of header'
        );
    }
}
