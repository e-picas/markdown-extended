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

class HorizontalRuleTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // abbreviation
        $markdownContent5 = $this->createContent("
This is a definition with two paragraphs. Lorem ipsum
dolor sit amet, consectetuer adipiscing elit. Aliquam
hendrerit mi posuere lectus.

----

Vestibulum enim wisi, viverra nec, fringilla in, laoreet
vitae, risus.
        ");
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals(
            '<p>This is a definition with two paragraphs. Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aliquam hendrerit mi posuere lectus.</p><hr /><p>Vestibulum enim wisi, viverra nec, fringilla in, laoreet vitae, risus.</p>',
            str_replace("\n", ' ', $this->getBody($content5, true)), 'Horizontal rule fails!');

    }
    
}
