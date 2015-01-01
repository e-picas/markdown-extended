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

class AbbreviationTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // abbreviation
        $markdownContent5 = $this->createContent("
A text whit HTML expression.

*[HTML]: Hyper Text Markup Language
        ");
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals('<p>A text whit <abbr title="Hyper Text Markup Language">HTML</abbr> expression.</p>', $this->getBody($content5), 'Abbreviation fails!');

    }
    
}
