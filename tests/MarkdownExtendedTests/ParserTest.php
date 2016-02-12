<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests;

use \MarkdownExtended\MarkdownExtended;

class ParserTest
    extends BaseUnitTest
{
    const MD_STRING     = "my **markdown** _extended_ simple string";
    const PARSED_STRING = 'my <strong>markdown</strong> <em>extended</em> simple string';

    /**
     * Get the tests test file path
     *
     * @return  string
     */
    public function getTestFilepath()
    {
        return $this->getResourcePath('test.md');
    }

    /**
     * Validate class methods
     */
    public function testCreate()
    {
    }

    /**
     * Get the tests test file parsed full content
     *
     * @return  string
     */
    public function getFileExpectedContent_test()
    {
        $body = $this->getFileExpectedBody_test();
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
     * Get the tests test.md file parsed body
     *
     * @return  string
     */
    public function getFileExpectedBody_test()
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
