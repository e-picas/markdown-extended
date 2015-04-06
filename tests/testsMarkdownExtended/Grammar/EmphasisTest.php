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

class EmphasisTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $this->processParseTest(
            '**Hello** _World_',
            '<strong>Hello</strong> <em>World</em>',
            'Emphasis fails!'
        );
    }
    
}
