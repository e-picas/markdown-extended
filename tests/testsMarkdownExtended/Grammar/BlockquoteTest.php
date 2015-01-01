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

class BlockquoteTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // abbreviation
        $markdownContent5 = $this->createContent("
> My citation
>
> With a paragraph and some `code`
>
>     and even a preformatted string
        ");
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals(
            '<blockquote><p>My citation</p><p>With a paragraph and some <code>code</code></p><pre>and even a preformatted string</pre></blockquote>',
            $this->getBody($content5, true), 'Blockquote fails!');

    }
    
}
