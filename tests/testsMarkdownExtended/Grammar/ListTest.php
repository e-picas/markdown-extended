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

class ListTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {

        // unordered list
        $this->processParseTest(
            "
-   first item
*   second item
    - first sub-item
    * second sub-item
-   third item
        ",
            '<ul><li>first item</li><li>second item  <ul><li>first sub-item</li><li>second sub-item</li></ul></li><li>third item</li></ul>',
            'Unordered list fails!',
            true, true
        );

        // ordered list
        $this->processParseTest(
            "
1.   first item
1.   second item
    1. first sub-item
    2. second sub-item
5.   third item
        ",
            '<ol><li>first item</li><li>second item  <ol><li>first sub-item</li><li>second sub-item</li></ol></li><li>third item</li></ol>',
            'Ordered list fails!',
            true, true
        );

    }
    
}
