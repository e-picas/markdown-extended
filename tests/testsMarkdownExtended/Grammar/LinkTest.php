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

class LinkTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {

        // classic link
        $this->processParseTest(
            '[Composer](http://getcomposer.org/)',
            '<a href="http://getcomposer.org/" title="See online http://getcomposer.org/">Composer</a>',
            'Simple links not work!'
        );

        // link with a title
        $this->processParseTest(
            '[Composer](http://getcomposer.org/ "My title")',
            '<a href="http://getcomposer.org/" title="My title">Composer</a>',
            'Links with title does not work!'
        );

    }
    
}
