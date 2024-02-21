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
use MarkdownExtended\Util\Helper;
use MarkdownExtended\Exception\FileSystemException;

class HelperTest extends ParserTestCase
{

    function testGetPath()
    {
        $this->assertEquals(
            '/my/path/with/many/parts',
            Helper::getPath([
                '/my/path',
                'with',
                'many',
                'parts'

            ]),
            '[dev] MarkdownExtended\Util\Helper::getPath() should construct a valid path string'
        );

        $this->assertEquals(
            '/My/Class/Name',
            Helper::getPath([
                '\My\Class\Name'

            ]),
            '[dev] MarkdownExtended\Util\Helper::getPath() on a class path should construct a valid path string'
        );

        $this->assertEquals(
            '/my/path/with/many/parts',
            Helper::getPath([
                '/my/path/with/many/parts'
            ]),
            '[dev] MarkdownExtended\Util\Helper::getPath() should accept a single array item'
        );

        $this->assertEquals(
            '/my/path/with/many/parts',
            Helper::getPath('/my/path/with/many/parts'),
            '[dev] MarkdownExtended\Util\Helper::getPath() should accept a string'
        );
    }

    function testReadFileOnInexistentFile()
    {
        $this->expectException(
            FileSystemException::class,
            '[dev] calling the MarkdownExtended\Util\Helper::readFile() method'
                .' with a wrong path'
                .' must throw a MarkdownExtended\Exception\FileSystemException'
        );

        $ctt = Helper::readFile('/i/do/not/exist'.rand());
    }

    function testReadFile()
    {
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->getTestFile_content()),
            $this->stripWhitespaceAndNewLines(Helper::readFile($this->getTestFile_filepath())),
            '[dev] MarkdownExtended\Util\Helper::readFile() must return the file contents'
        );
    }

}
    