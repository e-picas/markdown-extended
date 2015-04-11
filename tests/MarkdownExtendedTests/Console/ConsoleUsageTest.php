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

    /**
     * Test a call on a file with a custom template
     *
     * @runInSeparateProcess
     */
    public function testExtract()
    {
        $file = $this->getPath(array($this->getBasePath(), 'tests', 'test-meta.md'));
        $meta = array(
            'meta1' => 'a value for meta 1',
            'meta2' => 'another value for meta 2'
        );
        $meta_str = '';
        foreach ($meta as $var=>$val) {
            $meta_str .= $var.': '.$val.PHP_EOL;
        }
        $body = $this->stripWhitespaceAndNewLines(
            <<<MSG
<p>At vero eos et accusamus et <strong>iusto odio dignissimos ducimus qui blanditiis</strong> praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.</p>
<blockquote>
  <p>Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
      mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
      expedita distinctio.</p>
</blockquote>
<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.</p>
MSG
        );
        $html = $this->stripWhitespaceAndNewLines(
            <<<MSG
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>tests/test-meta.md</title>
    <meta name="meta1" content="a value for meta 1" />
<meta name="meta2" content="another value for meta 2" />
</head>
<body>
{$body}
</body>
</html>
MSG
        );

        // full content
        $res1 = $this->runCommand($this->getBaseCmd().' '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            $html,
            'Test of the CLI on a file with metadata'
        );

        // extraction of metadata
        $res2 = $this->runCommand($this->getBaseCmd().' -e '.$file);
        $this->assertEquals(
            trim($res2['stdout']),
            trim($meta_str),
            'Test of the CLI on a file with metadata extraction without argument (short option "-e")'
        );
        $res3 = $this->runCommand($this->getBaseCmd().' --extract '.$file);
        $this->assertEquals(
            trim($res3['stdout']),
            trim($meta_str),
            'Test of the CLI on a file with metadata extraction without argument (long option "--extract")'
        );

        // extraction of a single metadata
        $res4 = $this->runCommand($this->getBaseCmd().' -e=meta1 '.$file);
        $this->assertEquals(
            trim($res4['stdout']),
            $meta['meta1'],
            'Test of the CLI on a file with one single metadata extraction (short option "-e=meta1")'
        );
        $res5 = $this->runCommand($this->getBaseCmd().' --extract=meta1 '.$file);
        $this->assertEquals(
            trim($res5['stdout']),
            $meta['meta1'],
            'Test of the CLI on a file with one single metadata extraction (long option "--extract=meta1")'
        );

        // extraction of the body
        $res6 = $this->runCommand($this->getBaseCmd().' -e=body '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res6['stdout']),
            $body,
            'Test of the CLI on a file with body extraction (short option "-e=body")'
        );
        $res7 = $this->runCommand($this->getBaseCmd().' --extract=body '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res7['stdout']),
            $body,
            'Test of the CLI on a file with body extraction (short option "--extract=body")'
        );
    }

    /**
     * Test a call on a file with output generation
     *
     * @runInSeparateProcess
     */
    public function testOutput()
    {
        $this->flushTempDir();

        $file   = $this->getPath(array($this->getBasePath(), 'tests', 'test-meta.md'));
        $output = $this->getPath(array($this->getBasePath(), 'tmp', 'test-output-%s.html'));
        $html = $this->stripWhitespaceAndNewLines(
            <<<MSG
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>tests/test-meta.md</title>
    <meta name="meta1" content="a value for meta 1" />
<meta name="meta2" content="another value for meta 2" />
</head>
<body>
<p>At vero eos et accusamus et <strong>iusto odio dignissimos ducimus qui blanditiis</strong> praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.</p>
<blockquote>
  <p>Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
      mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
      expedita distinctio.</p>
</blockquote>
<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.</p>
</body>
</html>
MSG
        );

        // short option
        $output1 = sprintf($output, '1');
        $res1 = $this->runCommand($this->getBaseCmd().' -o '.$output1.' '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            $this->cleanupBasePath($output1),
            'Test of the CLI on a file with short option output generation (file path output)'
        );
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath(file_get_contents($output1))),
            $html,
            'Test of the CLI on a file with short option output generation (file content)'
        );

        // long option
        $output2 = sprintf($output, '2');
        $res2 = $this->runCommand($this->getBaseCmd().' --output '.$output2.' '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res2['stdout'])),
            $this->cleanupBasePath($output2),
            'Test of the CLI on a file with long option output generation (file path output)'
        );
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath(file_get_contents($output2))),
            $html,
            'Test of the CLI on a file with long option output generation (file content)'
        );

        $this->flushTempDir();
    }

    /**
     * Test a call on a file with output generation and backup
     *
     * @runInSeparateProcess
     */
    public function testOutputBackup()
    {
        $this->flushTempDir();

        $file   = $this->getPath(array($this->getBasePath(), 'tests', 'test-meta.md'));
        $output = $this->getPath(array($this->getBasePath(), 'tmp', 'test-output.html'));
        $regex  = basename($output).'~([0-9]{2}-?){6}';

        // first generation
        $res1 = $this->runCommand($this->getBaseCmd().' -o '.$output.' '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res1['stdout'])),
            $this->cleanupBasePath($output),
            'Test of the CLI on a file with short option output generation N (file path output)'
        );
        $this->assertFileExists(
            $this->stripWhitespaceAndNewLines($res1['stdout']),
            'Test of the CLI on a file with short option output generation N (file exists)'
        );

        // second generation
        $res2 = $this->runCommand($this->getBaseCmd().' -o '.$output.' '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res2['stdout'])),
            $this->cleanupBasePath($output),
            'Test of the CLI on a file with short option output generation N+1 (file path output)'
        );
        $this->assertFileExists(
            $this->stripWhitespaceAndNewLines($res2['stdout']),
            'Test of the CLI on a file with short option output generation N+1 (file exists)'
        );

        // test if the file was backuped
        $this->assertTrue(
            $this->tempFileExists($regex),
            'Test of the CLI on a file with short option output generation N+1 (backup exists)'
        );

        $this->flushTempDir();

        // third generation
        $res3 = $this->runCommand($this->getBaseCmd().' --force -o '.$output.' '.$file);
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res3['stdout'])),
            $this->cleanupBasePath($output),
            'Test of the CLI on a file with short option output generation N+2 and the force option (file path output)'
        );
        $this->assertFileExists(
            $this->stripWhitespaceAndNewLines($res3['stdout']),
            'Test of the CLI on a file with short option output generation N+2 and the force option (file exists)'
        );

        // test if the file was NOT backuped
        $this->assertFalse(
            $this->tempFileExists($regex),
            'Test of the CLI on a file with short option output generation N+2 and the force option (backup may NOT exist)'
        );

        $this->flushTempDir();
    }
}
