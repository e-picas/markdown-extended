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
use \MarkdownExtended\MarkdownExtended;

class AbbreviationTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        // abbreviation
        $this->processParseTest(
            "
A text whit HTML expression.

*[HTML]: Hyper Text Markup Language
        ",
            'A text whit <abbr title="Hyper Text Markup Language">HTML</abbr> expression.',
            'Abbreviation fails!'
        );
    }
    
}
