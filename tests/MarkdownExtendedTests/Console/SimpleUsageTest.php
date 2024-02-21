<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Console;

use MarkdownExtendedTests\ConsoleTestCase;
use MarkdownExtendedTests\ParserTestCase;

/**
 * @group console
 */
class SimpleUsageTest extends ConsoleTestCase
{
    /**
     * Test a default call with no argument
     *
     * @runInSeparateProcess
     */
    public function testNoArgument()
    {
        $res = $this->runCommand($this->getBaseCmd());
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI with no argument (status)'
        );
        // output
        $this->assertNotEmpty(
            $res['stdout'],
            '[console] test of the CLI with no argument has a not empty output on stdout'
        );
    }

    /**
     * Test a call on a simple string with no argument
     *
     * @runInSeparateProcess
     */
    public function testSimpleString()
    {
        $res = $this->runCommand($this->getBaseCmd().' "'.ParserTestCase::MD_STRING.'"');
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on simple string with no option (status)'
        );
        // stdout
        $this->assertEquals(
            ParserTestCase::PARSED_STRING,
            $res['stdout'],
            '[console] test of the CLI on simple string with no option'
        );
    }

    /**
     * Test a call on a simple string pied to mde
     *
     * @runInSeparateProcess
     */
    public function testPipedSimpleString()
    {
        $res = $this->runCommand('echo "'.ParserTestCase::MD_STRING.'" | '.$this->getBaseCmd());
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on a piped simple string (status)'
        );
        // stdout
        $this->assertEquals(
            ParserTestCase::PARSED_STRING,
            $res['stdout'],
            '[console] test of the CLI on a piped simple string'
        );
    }

    /**
     * Test a call on two simple strings
     *
     * @runInSeparateProcess
     */
    public function testMultipleStrings()
    {
        $res    = $this->runCommand($this->getBaseCmd().' "'.ParserTestCase::MD_STRING.'" "'.ParserTestCase::MD_STRING.'"');
        $line   = ParserTestCase::PARSED_STRING;
        $output = <<<MSG
==> STDIN#1 <==
{$line}
==> STDIN#2 <==
{$line}
MSG;
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on multiple strings with no option (status)'
        );
        // stdout
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($output),
            $this->stripWhitespaceAndNewLines($res['stdout']),
            '[console] test of the CLI on multiple strings with no option'
        );
    }

    /**
     * Test a call on an input file
     *
     * @runInSeparateProcess
     */
    public function testSimpleFile()
    {
        $file   = $this->getTestFile_filepath();
        $body   = $this->stripWhitespaceAndNewLines($this->getTestFile_parsedHtmlBody());
        $res    = $this->runCommand($this->getBaseCmd().' '.$file);
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on an input file (status)'
        );
        // stdout
        $this->assertEquals(
            $body,
            $this->stripWhitespaceAndNewLines($res['stdout']),
            '[console] test of the CLI on an input file'
        );
    }

    /**
     * Test a call on multiple input files
     *
     * @runInSeparateProcess
     */
    public function testMultipleFiles()
    {
        $file   = $this->getTestFile_filepath();
        $file2  = $this->getTestFileLong_filepath();
        $filename   = $this->getTestFile_filename();
        $filename2  = $this->getTestFileLong_filename();
        $body   = $this->stripWhitespaceAndNewLines($this->getTestFile_parsedHtmlContent());
        $body2  = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedHtmlContent());
        $output = $this->stripWhitespaceAndNewLines(
            <<<MSG
==> tests/{$filename} <==
{$body}
==> tests/{$filename2} <==
{$body2}
MSG
        );
        $res = $this->runCommand($this->getBaseCmd().' '.$file.' '.$file2);
        // status
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on multiple input files (status)'
        );
        // stdout
        $this->assertEquals(
            $output,
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res['stdout'])),
            '[console] test of the CLI on multiple input files'
        );
    }
}
