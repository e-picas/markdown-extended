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

use \MarkdownExtendedTests\ParserTest;
use \MarkdownExtended\MarkdownExtended;

class DefinitionsTest extends ParserTest
{
    public function testCreate()
    {
        $md = <<<MSG
Term 1
:   This is a definition with two paragraphs. Lorem ipsum
    dolor sit amet, consectetuer adipiscing elit. Aliquam
    hendrerit mi posuere lectus.

    Vestibulum enim wisi, viverra nec, fringilla in, laoreet
    vitae, risus.

:   Second definition for term 1, also wrapped in a paragraph
    because of the blank line preceding it.
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<dl><dt>Term 1</dt><dd><p>This is a definition with two paragraphs. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam hendrerit mi posuere lectus.</p><p>Vestibulum enim wisi, viverra nec, fringilla in, laoreet vitae, risus.</p></dd><dd><p>Second definition for term 1, also wrapped in a paragraph because of the blank line preceding it.</p></dd></dl>',
            '[parsing] test of definitions list'
        );
    }
}
