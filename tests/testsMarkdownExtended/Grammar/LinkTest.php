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

class LinkTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {

        // classic link
        $markdownParser = $this->createParser();
        $markdownContent3 = $this->createContent('[Composer](http://getcomposer.org/)');
        $content3 = $markdownParser->parse($markdownContent3)->getContent();
        $this->assertEquals('<p><a href="http://getcomposer.org/" title="See online http://getcomposer.org/">Composer</a></p>', $this->getBody($content3), 'Simple links not work!');

        // autolink
        $markdownContent5 = $this->createContent('<http://getcomposer.org/>');
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals('<p><a href="http://getcomposer.org/" title="See online http://getcomposer.org/">http://getcomposer.org/</a></p>', $this->getBody($content5), 'Autolink fails!');

        // autolink email
        $markdownContent2 = $this->createContent('<piero.wbmstr@gmail.com>');
        $content2 = $markdownParser->parse($markdownContent2)->getContent();
        $this->assertEquals('<p><a href="&#109;&#97;&#x69;&#x6c;&#116;&#111;&#58;&#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;" title="Contact &#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;">&#x70;&#x69;&#101;&#114;&#x6f;&#x2e;w&#98;&#109;&#x73;&#x74;&#114;&#64;&#x67;&#x6d;a&#105;&#108;&#x2e;&#x63;&#111;&#109;</a></p>', $this->getBody($content2), 'Email autolink fails!');

    }
    
}
