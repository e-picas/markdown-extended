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

class CodeTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {

        // simple code
        $this->processParseTest(
            'my text with `some code` for test ...',
            'my text with <code>some code</code> for test ...',
            'Code fails!'
        );

        // code blocks
        $this->processParseTest(
            "para1

    My code here

para2",
            '<p>para1</p><pre>My code here</pre><p>para2</p>',
            'Code block fails!',
            true
        );

        // fenced code blocks
        $this->processParseTest(
            "
~~~~
My code here
~~~~
        ",
            '<pre>My code here
</pre>',
            'Fenced code block fails!',
            true
        );

        // fenced code blocks with language
        $this->processParseTest(
            "
~~~~html
My code here
~~~~
        ",
            '<pre class="language-html">My code here
</pre>',
            'Fenced code block with language info fails!',
            true
        );

    }
    
}
