<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace testsMarkdownExtended;

use \MarkdownExtended\MarkdownExtended;

class MarkdownExtendedBaseTest
    extends \PHPUnit_Framework_TestCase
{

    /*
        $this->processParseTest(
            $md,
            $html,
            $title
        );
     */
    public function processParseTest($md, $html, $title, $strip_whitespaces = false, $strip_new_lines = false)
    {
        $content = (string) MarkdownExtended::parse($md);
        if ($strip_whitespaces) {
            $content = $this->stripWhitespaces($content);
        }
        if ($strip_new_lines) {
            $content = $this->stripNewLines($content);
        }
        $this->assertEquals($html, $content, $title);
    }

    /**
     * Get the tests test file path
     *
     * @return  string
     */
    public function getTestFilepath()
    {
        return __DIR__.'/test.md';
    }

    /**
     * Create a markdown parser
     *
     * @param   array   $configuration  Optional configuration
     * @return  \MarkdownExtended\Parser
    public function createParser($configuration = null)
    {
    return \MarkdownExtended\MarkdownExtended::getInstance()
    ->get('Parser', $configuration);
    }
     */

    /**
     * Create a markdown content
     *
     * @param   string  $content
     * @return  \MarkdownExtended\Content
    public function createContent($content = null)
    {
    return new \MarkdownExtended\Content($content);
    }
     */

    /**
     * Create a markdown content from file
     *
     * @param   string $filepath
     * @return  \MarkdownExtended\Content
    public function createSourceContent($filepath = null)
    {
    return new \MarkdownExtended\Content(null, $filepath);
    }
     */

    /**
     * Get a trimed content body
     *
     * @param   object  $content
     * @param   bool    $strip_whitespaces
     * @return  string
    public function getBody($content = null, $strip_whitespaces = false)
    {
    $ctt = trim($content->getBody());
    if (true===$strip_whitespaces) $ctt = $this->stripWhitespaces($ctt);
    return $ctt;
    }
     */

    /**
     * Strip whitespaces between tags in a string
     *
     * @param   string  $content
     * @return  string
     */
    public function stripWhitespaces($content = '')
    {
        return preg_replace('~>\s+<~', '><', $content);
    }

    public function stripNewLines($content = '')
    {
        return str_replace("\n", ' ', $content);
    }

    /**
     * Validate class methods
     */
    public function testCreate()
    {
/*
        $this->assertInstanceOf('\MarkdownExtended\Parser', $this->createParser(), 'baseTest->createParser failure!');

        $this->assertInstanceOf('\MarkdownExtended\Content', $this->createContent('test'), 'baseTest->createContent failure!');

        $this->assertFileExists($this->getTestFilepath(), 'baseTest->getTestFilepath failure!');

        $this->assertInstanceOf('\MarkdownExtended\Content', $this->createSourceContent($this->getTestFilepath()), 'baseTest->createSourceContent failure!');
*/
    }

    /**
     * Get the tests test file parsed full content
     *
     * @return  string
     */
    public function getTestExpectedFullContent()
    {
        $body = $this->getTestExpectedBody();
        return <<<EOF
<!DOCTYPE html>
<head>
<meta charset="utf-8" />
</head><body>

{$body}


<p>Last updated at Sun, 09 Jun 2013 11:34:50 +0000</p>

</body>
</html>
EOF;
    }

    /**
     * Get the tests test file parsed body
     *
     * @return  string
     */
    public function getTestExpectedBody()
    {
        return <<<EOF
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
EOF;
    }

}
