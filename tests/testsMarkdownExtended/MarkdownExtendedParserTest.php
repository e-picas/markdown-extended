<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
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
namespace testsMarkdownExtended;

class MarkdownExtendedParserTest extends MarkdownExtendedBaseTest
{

    /**
     * Simple test to ensure that parser can be created and most basic of
     * Markdown can be transformed.
     */
    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // test 1: titles
        $markdownContent = $this->createContent('#Hello World');
        $content = $markdownParser->parse($markdownContent)->getContent();
        $this->assertEquals('<h1 id="hello_world">Hello World</h1>', $this->getBody($content), 'Simple H1 not work!');

        // test 2: emphasis
        $markdownContent2 = $this->createContent('**Hello** _World_');
        $content2 = $markdownParser->parse($markdownContent2)->getContent();
        $this->assertEquals('<p><strong>Hello</strong> <em>World</em></p>', $this->getBody($content2), 'Simple emphasis not work!');

    }
    
}
