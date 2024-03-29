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

class HardbreakTest extends ParserTestCase
{
    public function testHardBreak()
    {
        $md = <<<MSG
This is a definition with two paragraphs. Lorem ipsum
dolor sit amet, consectetuer adipiscing elit. Aliquam
hendrerit mi posuere lectus.

Vestibulum enim wisi, viverra nec, fringilla in, laoreet
vitae, risus.
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '<p>This is a definition with two paragraphs. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam hendrerit mi posuere lectus.</p><p>Vestibulum enim wisi, viverra nec, fringilla in, laoreet vitae, risus.</p>',
            '[parsing] test of hard break'
        );
    }
}
