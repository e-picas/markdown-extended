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

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Util\ContentCollection;
use MarkdownExtended\Exception\UnexpectedValueException;
use MarkdownExtended\Content;
use MarkdownExtended\API\ContentInterface;


class ContentColletionTest extends BaseUnitTestCase
{

    function testAppendWrongObject()
    {
        $this->expectException(
            UnexpectedValueException::class,
            '[dev] calling the MarkdownExtended\Util\ContentCollection::append() method'
                .' with object not implementing MarkdownExtended\API\ContentInterface'
                .' must throw a MarkdownExtended\Exception\UnexpectedValueException'
        );

        $collection = new ContentCollection();
        $collection->append(new \StdClass);
    }

    function testOffsetSetWrongObject()
    {
        $this->expectException(
            UnexpectedValueException::class,
            '[dev] calling the MarkdownExtended\Util\ContentCollection::offsetSet() method'
                .' with object not implementing MarkdownExtended\API\ContentInterface'
                .' must throw a MarkdownExtended\Exception\UnexpectedValueException'
        );

        $collection = new ContentCollection();
        $collection->offsetSet(1, new \StdClass);
    }

}
