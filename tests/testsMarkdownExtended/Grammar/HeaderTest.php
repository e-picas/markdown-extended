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

class HeaderTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $this->processParseTest(
            '#Hello World',
            '<h1 id="hello-world">Hello World</h1>',
            'Header fails!'
        );
    }
    
}
