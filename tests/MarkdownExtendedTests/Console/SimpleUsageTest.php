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

class SimpleUsageTest
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
     * Test a call on a simple string pied to mde
     *
     * @runInSeparateProcess
     */
    public function testPipedSimpleString()
    {
        $res = $this->runCommand('echo "my **markdown** _extended_ simple string" | '.$this->getBaseCmd());
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI on a piped simple string (status)'
        );
        // stdout
        $this->assertEquals(
            $res['stdout'],
            'my <strong>markdown</strong> <em>extended</em> simple string',
            'Test of the CLI on a piped simple string'
        );
    }

    /**
     * Test a call on two simple strings
     *
     * @runInSeparateProcess
     */
    public function testMultipleStrings()
    {
        $res = $this->runCommand($this->getBaseCmd().' "my **markdown** _extended_ simple string" "my **markdown** _extended_ simple string"');
        $output = <<<MSG
==> STDIN#1 <==
my <strong>markdown</strong> <em>extended</em> simple string
==> STDIN#2 <==
my <strong>markdown</strong> <em>extended</em> simple string
MSG;
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI on multiple strings with no option (status)'
        );
        // stdout
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res['stdout']),
            $this->stripWhitespaceAndNewLines($output),
            'Test of the CLI on multiple strings with no option'
        );
    }

    /**
     * Test a call on an input file
     *
     * @runInSeparateProcess
     */
    public function testSimpleFile()
    {
        $file       = $this->getPath(array($this->getBasePath(), 'tests', 'test.md'));
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
        $res = $this->runCommand($this->getBaseCmd().' '.$file);
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI on an input file (status)'
        );
        // stdout
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($res['stdout']),
            $body,
            'Test of the CLI on an input file'
        );
    }

    /**
     * Test a call on multiple input files
     *
     * @runInSeparateProcess
     */
    public function testMultipleFiles()
    {
        $file = $this->getPath(array($this->getBasePath(), 'tests', 'test.md'));
        $file2 = $this->getPath(array($this->getBasePath(), 'tests', 'test-2.md'));
        $body = <<<MSG
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
MSG;
        $output = $this->stripWhitespaceAndNewLines(
<<<MSG
==> tests/test.md <==
{$body}
==> tests/test-2.md <==
{$body}
MSG
        );
        $res = $this->runCommand($this->getBaseCmd().' '.$file.' '.$file2);
        // status
        $this->assertEquals(
            $res['status'],
            '0',
            'Test of the CLI on multiple input files (status)'
        );
        // stdout
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines($this->cleanupBasePath($res['stdout'])),
            $output,
            'Test of the CLI on multiple input files'
        );
    }
}
