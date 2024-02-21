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

class LinkTest extends ParserTestCase
{
    public function testLink()
    {

        // classic link
        $md = '[Composer](http://getcomposer.org/)';
        $this->assertEquals(
            '<a href="http://getcomposer.org/" title="See online http://getcomposer.org/">Composer</a>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of simple links'
        );
    }

    public function testLinkWithTitle()
    {
        // link with a title
        $md = '[Composer](http://getcomposer.org/ "My title")';
        $this->assertEquals(
            '<a href="http://getcomposer.org/" title="My title">Composer</a>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of links with title'
        );
    }

    /**
     * @group not-implemented-yet
     */
    public function testLinkWithTitleAndAttribute()
    {
        // link with a title
        $md = '[Composer](http://getcomposer.org/ "My title" class=myclass)';
        $this->assertEquals(
            '<a href="http://getcomposer.org/" title="My title" class="myclass">Composer</a>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
        );
    }
}
