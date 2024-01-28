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
 * @see mde-manifest:C.3
 * @see mde-manifest:D.6
 */
class CodeTest extends ParserTestCase
{

    /**
     * @see mde-manifest:C.3
     */
    public function testInlineCodeBlock()
    {

        // simple code
        $md = 'This variable `$var` can be accessed using the `->get(...)` method';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            'This variable <code>$var</code> can be accessed using the <code>-&gt;get(...)</code> method',
            '[parsing] test of code span'
        );

    }

    /**
     * @see mde-manifest:D.6.a
     */
    public function testIndentedCodeBlock()
    {
        // code blocks
        $md = <<<MSG
            para1

                My code here

            para2
            MSG;
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '<p>para1</p><pre>My code here</pre><p>para2</p>',
            '[parsing] test of code block'
        );
    }

    /**
     * @see mde-manifest:D.6.b
     */
    public function testFencedCodeBlock()
    {
        // fenced code blocks
        $md = <<<MSG
            ~~~~
            My code here
            ~~~~
            MSG;
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '<pre>My code here
</pre>',
            '[parsing] test of fenced code block'
        );
    }

    /**
     * @see mde-manifest:D.6.b.2
     */
    public function testFencedCodeBlockWithLanguage()
    {
        // fenced code blocks with language
        $md = <<<MSG

            ~~~~html
            My code here
            ~~~~

            MSG;
        $this->assertEquals(
            $this->stripWhitespaces(
                (string) MarkdownExtended::parse($md, ['template' => false])
            ),
            '<pre class="language-html">My code here
</pre>',
            '[parsing] test of fenced code block with language info'
        );
    }
}
