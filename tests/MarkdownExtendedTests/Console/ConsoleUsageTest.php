<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Console;

use \MarkdownExtendedTests\ConsoleTest;

class ConsoleUsageTest
    extends ConsoleTest
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
            $res['status'],
            '0',
            'Test of the CLI with no argument (status)'
        );
        // output
        $this->assertNotEmpty(
            $res['stdout'],
            'Test of the CLI with no argument has a not empty output on stdout'
        );
    }

    /**
     * Test a call on a simple string with no argument
     *
     * @runInSeparateProcess
     */
    public function testSimpleString()
    {
        $res = $this->runCommand($this->getBaseCmd().' "my **markdown** _extended_ simple string"');
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI on simple string with no option (status)'
        );
        // stdout
        $this->assertEquals(
            $res['stdout'],
            'my <strong>markdown</strong> <em>extended</em> simple string',
            'Test of the CLI on simple string with no option'
        );
    }

    /**
     * Test a call on a simple string with template empty argument
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringTemplate()
    {

        $res1 = $this->runCommand($this->getBaseCmd().' --template "my **markdown** _extended_ simple string"');
        $res2 = $this->runCommand($this->getBaseCmd().' -t "my **markdown** _extended_ simple string"');
        $html = $this->stripWhitespaceAndNewLines(
<<<MSG
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>1</title>

</head>
<body>
<p>my <strong>markdown</strong> <em>extended</em> simple string</p>

</body>
</html>

MSG
);

        // status with long option
        $this->assertEquals(
            $res1['status'],
            '0',
            'Test of the CLI on a simple string with "--template" long option (status)'
        );
        // stdout with long option
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res1['stdout']),
            $html,
            'Test of the CLI on a simple string with "--template" long option'
        );

        // status with short option
        $this->assertEquals(
            $res2['status'],
            '0',
            'Test of the CLI on a simple string with "-t" short option (status)'
        );
        // stdout with short option
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res2['stdout']),
            $html,
            'Test of the CLI on a simple string with "-t" short option'
        );
    }

    /**
     * Test a call on a simple string with a custom template
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringCustomTemplate()
    {
        $tpl = $this->getPath(array($this->getBasePath(), 'tests', 'test-template.tpl'));
        $res = $this->runCommand($this->getBaseCmd().' --template=' .$tpl. ' "my **markdown** _extended_ simple string"');
        $html = $this->stripWhitespaceAndNewLines(
            <<<MSG
<custom>
<p>my <strong>markdown</strong> <em>extended</em> simple string</p>
</custom>
MSG
        );
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res['stdout']),
            $html,
            'Test of the CLI on a simple string with "--template=test-template" long option'
        );
    }

    /**
     * Test a call on a simple string with an invalid template path
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringCustomTemplateError()
    {
        $res = $this->runCommand($this->getBaseCmd().' --template=notexisting "my **markdown** _extended_ simple string"');
        // status NOT 0
        $this->assertNotEquals(
            $res['status'],
            '0',
            'Test of the CLI on a simple string with a wrong template path (status NOT 0)'
        );
        // stderr not empty
        $this->assertNotEmpty(
            $res['stderr'],
            'Test of the CLI on a simple string with a wrong template path (stderr not empty)'
        );
        // stderr message
        $this->assertStringEndsWith(
            'must be a valid file path',
            $res['stderr'],
            'Test of the CLI on a simple string with a wrong template path (error string)'
        );
    }

    /**
     * Test a call on a simple string as JSON
     *
     * @runInSeparateProcess
     */
    public function testSimpleStringAsJson()
    {

        $res1 = $this->runCommand($this->getBaseCmd().' --response json "my **markdown** _extended_ simple string"');
        $res2 = $this->runCommand($this->getBaseCmd().' --response=json "my **markdown** _extended_ simple string"');
        $res3 = $this->runCommand($this->getBaseCmd().' -r json "my **markdown** _extended_ simple string"');
        $res4 = $this->runCommand($this->getBaseCmd().' -r=json "my **markdown** _extended_ simple string"');
        $json = '{"content":"my <strong>markdown<\/strong> <em>extended<\/em> simple string","charset":"utf-8","title":"1","body":"<p>my <strong>markdown<\/strong> <em>extended<\/em> simple string<\/p>"}';

        // status with long option and no equal sign
        $this->assertEquals(
            $res1['status'],
            '0',
            'Test of the CLI on a simple string with "--response json" long option (status)'
        );
        // stdout with long option and no equal sign
        $this->assertEquals(
            $this->stripNewLines($res1['stdout']),
            $json,
            'Test of the CLI on a simple string with "--response json" long option'
        );

        // status with long option and equal sign
        $this->assertEquals(
            $res2['status'],
            '0',
            'Test of the CLI on a simple string with "--response=json" long option (status)'
        );
        // stdout with long option and equal sign
        $this->assertEquals(
            $this->stripNewLines($res2['stdout']),
            $json,
            'Test of the CLI on a simple string with "--response=json" long option'
        );

        // status with short option and no equal sign
        $this->assertEquals(
            $res3['status'],
            '0',
            'Test of the CLI on a simple string with "-r json" short option (status)'
        );
        // stdout with short option and no equal sign
        $this->assertEquals(
            $this->stripNewLines($res3['stdout']),
            $json,
            'Test of the CLI on a simple string with "-r json" short option'
        );

        // status with short option and equal sign
        $this->assertEquals(
            $res4['status'],
            '0',
            'Test of the CLI on a simple string with "-r=json" short option (status)'
        );
        // stdout with short option and equal sign
        $this->assertEquals(
            $this->stripNewLines($res4['stdout']),
            $json,
            'Test of the CLI on a simple string with "-r=json" short option'
        );
    }

}
