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
namespace testsMarkdownExtended\Grammar;

use \testsMarkdownExtended\MarkdownExtendedBaseTest;

class CodeTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();
        $markdownContent4 = $this->createContent('my text with `some code` for test ...');
        $content4 = $markdownParser->parse($markdownContent4)->getContent();
        $this->assertEquals('<p>my text with <code>some code</code> for test ...</p>', $this->getBody($content4), 'Code fails!');
    }
    
}
