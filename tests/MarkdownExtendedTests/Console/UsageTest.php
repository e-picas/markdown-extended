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
class UsageTest extends ConsoleTestCase
{
    /**
     * Test a call on a simple string with template empty argument
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringTemplate()
    {
        $line = ParserTestCase::PARSED_STRING;
        $html = $this->stripWhitespaceAndNewLines(
            <<<MSG
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>1</title>
</head>
<body>
<p>{$line}</p>
</body>
</html>
MSG
        );

        $res1 = $this->runCommand($this->getBaseCmd().' --template "'.ParserTestCase::MD_STRING.'"');
        // status with long option
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res1['status'],
            '[console] test of the CLI on a simple string with "--template" long option (status)'
        );
        // stdout with long option
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($res1['stdout']),
            '[console] test of the CLI on a simple string with "--template" long option'
        );

        $res2 = $this->runCommand($this->getBaseCmd().' -t "'.ParserTestCase::MD_STRING.'"');
        // status with short option
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res2['status'],
            '[console] test of the CLI on a simple string with "-t" short option (status)'
        );
        // stdout with short option
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($res2['stdout']),
            '[console] test of the CLI on a simple string with "-t" short option'
        );
    }

    /**
     * Test a call on a simple string with a custom template
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringCustomTemplate()
    {
        $tpl    = $this->getTestFileTemplate_filepath();
        $res    = $this->runCommand($this->getBaseCmd().' --template=' .$tpl. ' "'.ParserTestCase::MD_STRING.'"');
        $line   = ParserTestCase::PARSED_STRING;
        $html   = $this->stripWhitespaceAndNewLines(<<<EOT
<custom>
<p>{$line}</p>
</custom>
EOT
);

        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on a simple string with "--template=test-template" long option (status)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($res['stdout']),
            '[console] test of the CLI on a simple string with "--template=test-template" long option'
        );
    }

    /**
     * Test a call on a simple string with an invalid template path
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringCustomTemplateError()
    {
        $res = $this->runCommand($this->getBaseCmd().' --template=notexisting "'.ParserTestCase::MD_STRING.'"');
        // status NOT 0
        $this->assertNotEquals(
            ConsoleTestCase::STATUS_OK,
            $res['status'],
            '[console] test of the CLI on a simple string with a wrong template path (status NOT 0)'
        );
        // stderr not empty
        $this->assertNotEmpty(
            $res['stderr'],
            '[console] test of the CLI on a simple string with a wrong template path (stderr not empty)'
        );
        // stderr message
        $this->assertStringEndsWith(
            'must be a valid file path',
            $res['stderr'],
            '[console] test of the CLI on a simple string with a wrong template path (error string)'
        );
    }

    /**
     * Test a call on a simple string as JSON
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringAsJson()
    {
        $res1 = $this->runCommand($this->getBaseCmd().' --response json "'.ParserTestCase::MD_STRING.'"');
        $res2 = $this->runCommand($this->getBaseCmd().' --response=json "'.ParserTestCase::MD_STRING.'"');
        $res3 = $this->runCommand($this->getBaseCmd().' -r json "'.ParserTestCase::MD_STRING.'"');
        $res4 = $this->runCommand($this->getBaseCmd().' -r=json "'.ParserTestCase::MD_STRING.'"');
        $json = '{"content":"my <strong>markdown<\/strong> <em>extended<\/em> simple string","charset":"utf-8","title":"1","body":"<p>my <strong>markdown<\/strong> <em>extended<\/em> simple string<\/p>"}';

        // status with long option and no equal sign
        $this->assertEquals(
            ConsoleTestCase::STATUS_OK,
            $res1['status'],
            '[console] test of the CLI on a simple string with "--response json" long option (status)'
        );
        // stdout with long option and no equal sign
        $this->assertEquals(
            $json,
            $this->stripNewLines($res1['stdout']),
            '[console] test of the CLI on a simple string with "--response json" long option'
        );

        // status with long option and equal sign
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res2['status'],
            '[console] test of the CLI on a simple string with "--response=json" long option (status)'
        );
        // stdout with long option and equal sign
        $this->assertEquals(
            $json,
            $this->stripNewLines($res2['stdout']),
            '[console] test of the CLI on a simple string with "--response=json" long option'
        );

        // status with short option and no equal sign
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res3['status'],
            '[console] test of the CLI on a simple string with "-r json" short option (status)'
        );
        // stdout with short option and no equal sign
        $this->assertEquals(
            $json,
            $this->stripNewLines($res3['stdout']),
            '[console] test of the CLI on a simple string with "-r json" short option'
        );

        // status with short option and equal sign
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res4['status'],
            '[console] test of the CLI on a simple string with "-r=json" short option (status)'
        );
        // stdout with short option and equal sign
        $this->assertEquals(
            $json,
            $this->stripNewLines($res4['stdout']),
            '[console] test of the CLI on a simple string with "-r=json" short option'
        );
    }

    /**
     * Test a call on a file with and without metadata and auto template
     *
     * @runInSeparateProcess
     */
    public function testTemplateAuto()
    {
        $file       = $this->getTestFile_filepath();
        $file_meta  = $this->getTestFileLong_filepath();
        $body       = $this->stripWhitespaceAndNewLines($this->getTestFile_parsedHtmlContent());
        $html       = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedHtmlContent());

        // full content without metadata
        $res1 = $this->runCommand($this->getBaseCmd().' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res1['status'],
            '[console] test of the CLI on a file with no metadata and automatic templating (status)'
        );
        $this->assertEquals(
            $body,
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            '[console] test of the CLI on a file with no metadata and automatic templating'
        );

        // full content with metadata
        $res2 = $this->runCommand($this->getBaseCmd().' '.$file_meta);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res2['status'],
            '[console] test of the CLI on a file with metadata and automatic templating (status)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res2['stdout'])),
            '[console] test of the CLI on a file with metadata and automatic templating'
        );
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractFullContent()
    {
        $file = $this->getTestFileLong_filepath();
        $html   = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedHtmlContent());

        // full content
        $res1 = $this->runCommand($this->getBaseCmd().' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res1['status'],
            '[console] test of the CLI on a file with metadata (status)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            '[console] test of the CLI on a file with metadata'
        );
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractMetadata()
    {
        $file = $this->getTestFileLong_filepath();
        $meta = [
            'meta1' => 'a value for meta 1',
            'meta2' => 'another value for meta 2',
        ];
        $meta_str = '';
        foreach ($meta as $var => $val) {
            $meta_str .= $var.': '.$val.PHP_EOL;
        }

        // extraction of metadata
        $res2 = $this->runCommand($this->getBaseCmd().' -e '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res2['status'],
            '[console] test of the CLI on a file with metadata extraction without argument (short option "-e") (status)'
        );
        $this->assertStringEndsWith(
            trim($meta_str),
            trim($res2['stdout']),
            '[console] test of the CLI on a file with metadata extraction without argument (short option "-e")'
        );
        $res3 = $this->runCommand($this->getBaseCmd().' --extract '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res3['status'],
            '[console] test of the CLI on a file with metadata extraction without argument (long option "--extract") (status)'
        );
        $this->assertStringEndsWith(
            trim($meta_str),
            trim($res3['stdout']),
            '[console] test of the CLI on a file with metadata extraction without argument (long option "--extract")'
        );
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractSingleMetadata()
    {
        $file = $this->getTestFileLong_filepath();
        $meta = [
            'meta1' => 'a value for meta 1',
            'meta2' => 'another value for meta 2',
        ];

        // extraction of a single metadata
        $res4 = $this->runCommand($this->getBaseCmd().' -e=meta1 '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res4['status'],
            '[console] test of the CLI on a file with one single metadata extraction (short option "-e=meta1") (status)'
        );
        $this->assertEquals(
            $meta['meta1'],
            trim($res4['stdout']),
            '[console] test of the CLI on a file with one single metadata extraction (short option "-e=meta1")'
        );
        $res5 = $this->runCommand($this->getBaseCmd().' --extract=meta1 '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res5['status'],
            '[console] test of the CLI on a file with one single metadata extraction (long option "--extract=meta1") (status)'
        );
        $this->assertEquals(
            $meta['meta1'],
            trim($res5['stdout']),
            '[console] test of the CLI on a file with one single metadata extraction (long option "--extract=meta1")'
        );
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractBody()
    {
        $file = $this->getTestFileLong_filepath();
        $body   = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedHtmlBody());

        // extraction of the body
        $res6 = $this->runCommand($this->getBaseCmd().' -e=body '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res6['status'],
            '[console] test of the CLI on a file with body extraction (short option "-e=body") (status)'
        );
        $this->assertEquals(
            $body,
            $this->stripWhitespaceAndNewLines($res6['stdout']),
            '[console] test of the CLI on a file with body extraction (short option "-e=body")'
        );
        $res7 = $this->runCommand($this->getBaseCmd().' --extract=body '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res7['status'],
            '[console] test of the CLI on a file with body extraction (long option "--extract=body") (status)'
        );
        $this->assertEquals(
            $body,
            $this->stripWhitespaceAndNewLines($res7['stdout']),
            '[console] test of the CLI on a file with body extraction (long option "--extract=body")'
        );
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractTitle()
    {
        $file = $this->getTestFileLong_filepath();
        $title  = $this->stripWhitespaceAndNewLines($this->getTestFileLong_title());

        // extraction of the title
        $res8 = $this->runCommand($this->getBaseCmd().' -e=title '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res8['status'],
            '[console] test of the CLI on a file with body extraction (short option "-e=title") (status)'
        );
        $this->assertEquals(
            $title,
            $this->stripWhitespaceAndNewLines($res8['stdout']),
            '[console] test of the CLI on a file with body extraction (short option "-e=title")'
        );
        $res9 = $this->runCommand($this->getBaseCmd().' --extract=title '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res9['status'],
            '[console] test of the CLI on a file with body extraction (long option "--extract=title") (status)'
        );
        $this->assertEquals(
            $title,
            $this->stripWhitespaceAndNewLines($res9['stdout']),
            '[console] test of the CLI on a file with body extraction (long option "--extract=title")'
        );
    }

    /**
     * Test a call on a file with metadata and output generation
     *
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $this->flushTempDir();

        $file   = $this->getTestFileLong_filepath();
        $output = $this->getPath([$this->getBasePath(), 'tmp', 'test-output-%s.html']);
        $html   = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedHtmlContent());

        // short option
        $output1 = sprintf($output, '1');
        $res1 = $this->runCommand($this->getBaseCmd().' -o '.$output1.' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res1['status'],
            '[console] test of the CLI on a file with short option output generation (status)'
        );
        $this->assertEquals(
            $this->cleanupBasePath($output1),
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            '[console] test of the CLI on a file with short option output generation (file path output)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath(file_get_contents($output1))),
            '[console] test of the CLI on a file with short option output generation (file content)'
        );

        // long option
        $output2 = sprintf($output, '2');
        $res2 = $this->runCommand($this->getBaseCmd().' --output '.$output2.' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res2['status'],
            '[console] test of the CLI on a file with long option output generation (status)'
        );
        $this->assertEquals(
            $this->cleanupBasePath($output2),
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res2['stdout'])),
            '[console] test of the CLI on a file with long option output generation (file path output)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath(file_get_contents($output2))),
            '[console] test of the CLI on a file with long option output generation (file content)'
        );

        $this->flushTempDir();
    }

    /**
     * Test a call on a file with metadata and output generation and backup
     *
     * @runInSeparateProcess
     */
    public function testOutputBackup()
    {
        $this->flushTempDir();

        $file   = $this->getTestFileLong_filepath();
        $output = $this->getPath([$this->getBasePath(), 'tmp', 'test-output.html']);
        $regex  = basename($output).'~([0-9]{2}-?){6}';

        // first generation
        $res1 = $this->runCommand($this->getBaseCmd().' -o '.$output.' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res1['status'],
            '[console] test of the CLI on a file with short option output generation N (status)'
        );
        $this->assertEquals(
            $this->cleanupBasePath($output),
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            '[console] test of the CLI on a file with short option output generation N (file path output)'
        );
        $this->assertFileExists(
            $this->stripWhitespaceAndNewLines($res1['stdout']),
            '[console] test of the CLI on a file with short option output generation N (file exists)'
        );

        // second generation
        $res2 = $this->runCommand($this->getBaseCmd().' -o '.$output.' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res2['status'],
            '[console] test of the CLI on a file with short option output generation N+1 (status)'
        );
        $this->assertEquals(
            $this->cleanupBasePath($output),
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res2['stdout'])),
            '[console] test of the CLI on a file with short option output generation N+1 (file path output)'
        );
        $this->assertFileExists(
            $this->stripWhitespaceAndNewLines($res2['stdout']),
            '[console] test of the CLI on a file with short option output generation N+1 (file exists)'
        );

        // test if the file was backuped
        $this->assertTrue(
            $this->tempFileExists($regex),
            '[console] test of the CLI on a file with short option output generation N+1 (backup exists)'
        );

        $this->flushTempDir();

        // third generation
        $res3 = $this->runCommand($this->getBaseCmd().' --force -o '.$output.' '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res3['status'],
            '[console] test of the CLI on a file with short option output generation N+2 and the force option (status)'
        );
        $this->assertEquals(
            $this->cleanupBasePath($output),
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res3['stdout'])),
            '[console] test of the CLI on a file with short option output generation N+2 and the force option (file path output)'
        );
        $this->assertFileExists(
            $this->stripWhitespaceAndNewLines($res3['stdout']),
            '[console] test of the CLI on a file with short option output generation N+2 and the force option (file exists)'
        );

        // test if the file was NOT backuped
        $this->assertFalse(
            $this->tempFileExists($regex),
            '[console] test of the CLI on a file with short option output generation N+2 and the force option (backup may NOT exist)'
        );

        $this->flushTempDir();
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractManpage()
    {
        $file = $this->getTestFileLong_filepath();
        $html = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedManContent());

        // extraction of the body
        $res6 = $this->runCommand($this->getBaseCmd().' -f=man '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res6['status'],
            '[console] test of the CLI on a file with MAN format and body extraction (short option "-f=man") (status)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($res6['stdout']),
            '[console] test of the CLI on a file with MAN format and body extraction (short option "-f=man")'
        );
        $res7 = $this->runCommand($this->getBaseCmd().' --format=man '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res7['status'],
            '[console] test of the CLI on a file with MAN format and body extraction (long option "--format=man") (status)'
        );
        $this->assertEquals(
            $html,
            $this->stripWhitespaceAndNewLines($res7['stdout']),
            '[console] test of the CLI on a file with MAN format and body extraction (long option "--format=man")'
        );
    }

    /**
     * Test a call on a file with metadata and a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtractBodyManpage()
    {
        $file = $this->getTestFileLong_filepath();
        $body   = $this->stripWhitespaceAndNewLines($this->getTestFileLong_parsedManBody());

        // extraction of the body
        $res6 = $this->runCommand($this->getBaseCmd().' -f=man -e=body '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res6['status'],
            '[console] test of the CLI on a file with MAN format and body extraction (short options "-f=man -e=body") (status)'
        );
        $this->assertEquals(
            $body,
            $this->stripWhitespaceAndNewLines($res6['stdout']),
            '[console] test of the CLI on a file with MAN format and body extraction (short options "-f=man -e=body")'
        );
        $res7 = $this->runCommand($this->getBaseCmd().' --format=man --extract=body '.$file);
        $this->assertEquals(
             ConsoleTestCase::STATUS_OK,
            $res7['status'],
            '[console] test of the CLI on a file with MAN format and body extraction (long options "--format=man --extract=body") (status)'
        );
        $this->assertEquals(
            $body,
            $this->stripWhitespaceAndNewLines($res7['stdout']),
            '[console] test of the CLI on a file with MAN format and body extraction (long options "--format=man --extract=body")'
        );
    }

}
