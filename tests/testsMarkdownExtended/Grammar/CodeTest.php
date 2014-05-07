<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
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
