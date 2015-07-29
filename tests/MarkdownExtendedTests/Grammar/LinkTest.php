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

class LinkTest extends ParserTest
{
    public function testCreate()
    {

        // classic link
        $md = '[Composer](http://getcomposer.org/)';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, array('template'=>false)),
            '<a href="http://getcomposer.org/" title="See online http://getcomposer.org/">Composer</a>',
            '[parsing] test of simple links'
        );

        // link with a title
        $md = '[Composer](http://getcomposer.org/ "My title")';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, array('template'=>false)),
            '<a href="http://getcomposer.org/" title="My title">Composer</a>',
            '[parsing] test of links with title'
        );
    }
}
