<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

use MarkdownExtended\OutputFormatBag;
use MarkdownExtended\Exception\InvalidArgumentException;

class OutputFormatBagTest extends ParserTestCase
{

    function testWrongOutputFormatBag()
    {
        $this->expectException(
            InvalidArgumentException::class,
            '[dev] calling the MarkdownExtended\OutputFormatBag::load() method'
                .' with an unknown class name'
                .' must throw a MarkdownExtended\Exception\InvalidArgumentException'
        );

        $bag = new OutputFormatBag();
        $bag->load('TheClassThatDoesNotExist');
    }

}
