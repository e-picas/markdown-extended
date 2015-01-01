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

class LinkTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // classic link
        $markdownContent3 = $this->createContent('[Composer](http://getcomposer.org/)');
        $content3 = $markdownParser->parse($markdownContent3)->getContent();
        $this->assertEquals('<p><a href="http://getcomposer.org/" title="See online http://getcomposer.org/">Composer</a></p>', $this->getBody($content3), 'Simple links not work!');

        // link with a title
        $markdownContent4 = $this->createContent('[Composer](http://getcomposer.org/ "My title")');
        $content4 = $markdownParser->parse($markdownContent4)->getContent();
        $this->assertEquals('<p><a href="http://getcomposer.org/" title="My title">Composer</a></p>', $this->getBody($content4), 'Links with title does not work!');

    }
    
}
