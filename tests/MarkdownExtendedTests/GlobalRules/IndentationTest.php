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

/**
 * @group mde-manifest
 * @see mde-manifest:B.5
 */
class IndentationTest extends ParserTestCase
{
    public function testIndentation()
    {
        $md = '    This should be treated as a code block (indented)';
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '<pre>This should be treated as a code block (indented)</pre>',
            '[parsing] indenting by 4 spaces is considered as a tab'
        );
    }

    public function testIndentationLessThanATab()
    {
        $md = '   This should NOT be treated as a code block (indented by 3 spaces only)';
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            'This should NOT be treated as a code block (indented by 3 spaces only)',
            '[parsing] indenting by 3 spaces is NOT considered as a tab'
        );
    }
}
