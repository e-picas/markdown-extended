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

class HorizontalRuleTest extends ParserTest
{

    public function testCreate()
    {
        $md = <<<MSG
This is a definition with two paragraphs. Lorem ipsum
dolor sit amet, consectetuer adipiscing elit. Aliquam
hendrerit mi posuere lectus.

----

Vestibulum enim wisi, viverra nec, fringilla in, laoreet
vitae, risus.
MSG;
        $this->assertEquals(
            $this->stripNewLines($this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            )),
            '<p>This is a definition with two paragraphs. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam hendrerit mi posuere lectus.</p><hr /><p>Vestibulum enim wisi, viverra nec, fringilla in, laoreet vitae, risus.</p>',
            'Horizontal rule fails!'
        );
    }
    
}
