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

/**
 * @group mde-manifest
 * @see mde-manifest:F.7
 */
class AnchorTest extends ParserTestCase
{
    public function testAnchorOnHeader()
    {
        // header with anchor
        $md = '## My title {#my-anchor}';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<h2 id="my-anchor">My title</h2>',
            '[parsing] test of a header with a user defined anchor'
        );

    }
}
