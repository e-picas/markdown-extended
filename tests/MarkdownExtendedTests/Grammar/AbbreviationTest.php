<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use \MarkdownExtendedTests\ParserTest;
use \MarkdownExtended\MarkdownExtended;

class AbbreviationTest extends ParserTest
{

    public function testCreate()
    {
        $md = "
A text whit HTML expression.

*[HTML]: Hyper Text Markup Language
        ";
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, array('template'=>false)),
            'A text whit <abbr title="Hyper Text Markup Language">HTML</abbr> expression.',
            '[parsing] Test of abbreviation'
        );
    }
}
