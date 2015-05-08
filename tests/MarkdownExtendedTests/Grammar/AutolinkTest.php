<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use \MarkdownExtendedTests\ParserTest;
use \MarkdownExtended\MarkdownExtended;

class AutolinkTest extends ParserTest
{

    public function testCreate()
    {
        // autolink
        $md = '<http://getcomposer.org/>';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, array('template'=>false)),
            '<a href="http://getcomposer.org/" title="See online http://getcomposer.org/">http://getcomposer.org/</a>',
            '[parsing] test of autolink'
        );

        // autolink email
        $md = '<piero.wbmstr@gmail.com>';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, array('template'=>false)),
            '<a href="&#109;&#97;&#x69;&#x6c;&#116;&#111;&#58;&#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;" title="Contact &#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;">&#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;</a>',
            '[parsing] test of email autolink'
        );
    }
}
