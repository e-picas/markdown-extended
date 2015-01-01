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
        $markdownParser = $this->createParser();
        $markdownContent1 = $this->createContent('my text with `some code` for test ...');
        $content1 = $markdownParser->parse($markdownContent1)->getContent();
        $this->assertEquals(
            '<p>my text with <code>some code</code> for test ...</p>',
            $this->getBody($content1), 'Code fails!');

        // code blocks
        $markdownContent4 = $this->createContent("
    My code here
        ");
        $content4 = $markdownParser->parse($markdownContent4)->getContent();
        $this->assertEquals(
            '<pre>My code here</pre>',
            $this->getBody($content4, true), 'Code block fails!');

        // fenced code blocks
        $markdownContent5 = $this->createContent("
~~~~
My code here
~~~~
        ");
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals(
            '<pre>My code here
</pre>',
            $this->getBody($content5, true), 'Fenced code block fails!');

        // fenced code blocks with language
        $markdownContent6 = $this->createContent("
~~~~html
My code here
~~~~
        ");
        $content6 = $markdownParser->parse($markdownContent6)->getContent();
        $this->assertEquals(
            '<pre data-language="html">My code here
</pre>',
            $this->getBody($content6, true), 'Fenced code block with language info fails!');


    }
    
}
