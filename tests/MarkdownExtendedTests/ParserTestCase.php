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

class ParserTestCase extends BaseUnitTestCase
{
    const MD_STRING     = "my **markdown** _extended_ simple string";

    const PARSED_STRING = 'my <strong>markdown</strong> <em>extended</em> simple string';

    /**
     * Get the tests test file path
     *
     * @return  string
     */
    public function getTestFile_filename()
    {
        return 'test-1.md';
    }

    /**
     * Get the tests test file path
     *
     * @return  string
     */
    public function getTestFile_filepath()
    {
        return $this->getResourcePath($this->getTestFile_filename());
    }

    /**
     * Get the tests test file raw content
     *
     * @return  string
     */
    public function getTestFile_content()
    {
        return <<<EOF
At vero eos et accusamus et **iusto odio dignissimos ducimus qui blanditiis** praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.

>   Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
    mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
    expedita distinctio.

Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.
EOF;
    }

    /**
     * Get the tests test.md file parsed body
     *
     * @return  string
     */
    public function getTestFile_parsedHtmlBody()
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

    /**
     * Get the tests test.md file parsed full content
     *
     * @return  string
     */
    public function getTestFile_parsedHtmlContent()
    {
        return $this->getTestFile_parsedHtmlBody();
    }

    /**
     * Get the tests test.md file parsed body
     *
     * @return  string
     */
    public function getTestFile_parsedManBody()
    {
        return <<<EOF
.TH  "" "3" "" "" ""
.PP
At vero eos et accusamus et \fBiusto odio dignissimos ducimus qui blanditiis\fP praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.
.RS

"
.PP
  Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
      mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
      expedita distinctio.
"
.RE
.PP
Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.
EOF;
    }

    /**
     * Get the tests test.md file parsed full content
     *
     * @return  string
     */
    public function getTestFile_parsedManContent()
    {
        return $this->getTestFile_parsedManBody();
    }

    /**
     * Get the tests test file parsed full content
     *
     * @return  string
     */
    public function getTestFile_parsedContentWithTemplate()
    {
        $body = $this->getTestFile_parsedHtmlBody();
        return <<<EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>test-meta</title>
    <meta name="meta1" content="a value for meta 1" />
    <meta name="meta2" content="another value for meta 2" />
</head>
<body>
{$body}
</body>
</html>
EOF;
    }

    /**
     * Get the tests test.md file parsed title
     *
     * @return  string
     */
    public function getTestFile_title()
    {
        return "test-1";
    }

    /**
     * Get the tests test file name
     *
     * @return  string
     */
    public function getTestFileLong_filename()
    {
        return 'test-2.md';
    }

    /**
     * Get the tests test file path
     *
     * @return  string
     */
    public function getTestFileLong_filepath()
    {
        return $this->getResourcePath($this->getTestFileLong_filename());
    }

    /**
     * Get the tests test file raw content
     *
     * @return  string
     */
    public function getTestFileLong_content()
    {
        return <<<EOF
meta1: a value for meta 1
meta2: another value for meta 2

# Document title

At vero eos et accusamus et **iusto odio dignissimos ducimus qui blanditiis** praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.

>   Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
    mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
    expedita distinctio.

Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.[^1]
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.

[^1]: The footnote content...
EOF;
    }

    /**
     * Get the tests test file parsed full content
     *
     * @return  string
     */
    public function getTestFileLong_parsedHtmlContent()
    {
        $body = $this->getTestFileLong_parsedHtmlBody();
        return <<<EOF
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Document title</title>
    <meta name="meta1" content="a value for meta 1" />
<meta name="meta2" content="another value for meta 2" />
</head>
<body>
{$body}
</body>
</html>
EOF;
    }

    /**
     * Get the tests test.md file parsed body
     *
     * @return  string
     */
    public function getTestFileLong_parsedHtmlBody()
    {
        return <<<EOF
<h1 id="document-title">Document title</h1>
<p>At vero eos et accusamus et <strong>iusto odio dignissimos ducimus qui blanditiis</strong> praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.</p>
<blockquote>  
  <p>Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
      mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
      expedita distinctio.</p>
</blockquote>
<p>Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.[^1]
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.</p>
EOF;
    }

    /**
     * Get the tests test.md file parsed body
     *
     * @return  string
     */
    public function getTestFileLong_parsedManBody()
    {
        return <<<EOF
.TH  "" "3" "" "" ""
.SH DOCUMENT TITLE
.PP
At vero eos et accusamus et \\fBiusto odio dignissimos ducimus qui blanditiis\\fP praesentium
voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi.
.RS

"
.PP
  Sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt
      mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et
      expedita distinctio.
"
.RE
.PP
Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id
quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus.[^1]
Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet
ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic
tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut
perferendis doloribus asperiores repellat.
EOF;
    }

    /**
     * Get the tests test.md file parsed full content
     *
     * @return  string
     */
    public function getTestFileLong_parsedManContent()
    {
        $body = $this->getTestFileLong_parsedManBody();
        return <<<EOF
.\\" meta1: a value for meta 1
.\\" meta2: another value for meta 2
{$body}
EOF;
    }

    /**
     * Get the tests test.md file parsed title
     *
     * @return  string
     */
    public function getTestFileLong_title()
    {
        return "Document title";
    }

    /**
     * Get the test template file name
     *
     * @return  string
     */
    public function getTestFileTemplate_filename()
    {
        return 'test-template.tpl';
    }

    /**
     * Get the test template file path
     *
     * @return  string
     */
    public function getTestFileTemplate_filepath()
    {
        return $this->getResourcePath($this->getTestFileTemplate_filename());
    }

    /**
     * Get the test template file raw content
     *
     * @return  string
     */
    public function getTestFileTemplate_content()
    {
        return <<<EOF
<custom>
{% BODY %}
</custom>
EOF;
    }

}
