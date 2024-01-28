<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use MarkdownExtendedTests\ParserTestCase;
use MarkdownExtended\MarkdownExtended;

class BlockquoteTest extends ParserTestCase
{
    public function testBlockquote()
    {
        $md = "
> My citation
>
> With a paragraph and some `code`
>
>     and even a preformatted string
        ";
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '<blockquote><p>My citation</p><p>With a paragraph and some <code>code</code></p><pre>and even a preformatted string</pre></blockquote>',
            '[parsing] test of blockquote'
        );
    }
}
