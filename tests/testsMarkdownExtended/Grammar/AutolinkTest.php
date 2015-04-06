<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace testsMarkdownExtended\Grammar;

use \testsMarkdownExtended\MarkdownExtendedBaseTest;
use \MarkdownExtended\MarkdownExtended;

class AutolinkTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        // autolink
        $this->processParseTest(
            '<http://getcomposer.org/>',
            '<a href="http://getcomposer.org/" title="See online http://getcomposer.org/">http://getcomposer.org/</a>',
            'Autolink fails!'
        );

        // autolink email
        $this->processParseTest(
            '<piero.wbmstr@gmail.com>',
            '<a href="&#109;&#97;&#x69;&#x6c;&#116;&#111;&#58;&#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;" title="Contact &#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;">&#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;</a>',
            'Email autolink fails!'
        );

    }
    
}
