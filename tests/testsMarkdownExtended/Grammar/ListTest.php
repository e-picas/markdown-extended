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

class ListTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // unordered list
        $markdownContent5 = $this->createContent("
-   first item
*   second item
    - first sub-item
    * second sub-item
-   third item
        ");
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals(
            '<ul><li>first item</li><li>second item  <ul><li>first sub-item</li><li>second sub-item</li></ul></li><li>third item</li></ul>',
            str_replace("\n", ' ', $this->getBody($content5, true)), 'Unordered list fails!');

        // ordered list
        $markdownContent6 = $this->createContent("
1.   first item
1.   second item
    1. first sub-item
    2. second sub-item
5.   third item
        ");
        $content6 = $markdownParser->parse($markdownContent6)->getContent();
        $this->assertEquals(
            '<ol><li>first item</li><li>second item  <ol><li>first sub-item</li><li>second sub-item</li></ol></li><li>third item</li></ol>',
            str_replace("\n", ' ', $this->getBody($content6, true)), 'Ordered list fails!');

    }
    
}
