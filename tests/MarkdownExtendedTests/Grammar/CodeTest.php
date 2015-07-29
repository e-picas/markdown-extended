<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use \MarkdownExtendedTests\ParserTest;
use \MarkdownExtended\MarkdownExtended;

class CodeTest extends ParserTest
{
    public function testCreate()
    {

        // simple code
        $md = 'my text with `some code` for test ...';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, array('template'=>false)),
            'my text with <code>some code</code> for test ...',
            '[parsing] test of code span'
        );

        // code blocks
        $md = <<<MSG
para1

    My code here

para2
MSG;
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<p>para1</p><pre>My code here</pre><p>para2</p>',
            '[parsing] test of code block'
        );

        // fenced code blocks
        $md = <<<MSG
~~~~
My code here
~~~~
MSG;
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<pre>My code here
</pre>',
            '[parsing] test of fenced code block'
        );

        // fenced code blocks with language
        $md = <<<MSG

~~~~html
My code here
~~~~

MSG;
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<pre class="language-html">My code here
</pre>',
            '[parsing] test of fenced code block with language info'
        );
    }
}
