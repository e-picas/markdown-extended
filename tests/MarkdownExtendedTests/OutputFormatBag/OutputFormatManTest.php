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

use MarkdownExtendedTests\ParserTestCase;
use MarkdownExtended\OutputFormat\Man;
use MarkdownExtended\Exception\InvalidArgumentException;

class OutputFormatManTest extends ParserTestCase
{

    protected $_formater;

    function setUp():void
    {
        $this->_formater = new Man();
    }

    /**
     * This may construct a valid string for concerned tag, content and attributes
     *
     * @param   string  $tag_name       The tag name to construct
     * @param   string  $content        Concerned content
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     *
     * @return  string
    public function buildTag($tag_name, $content = null, array $attributes = []);
     */
    function testBuildTag()
    {

        $this->assertEquals(
            '`\fSMy code\fP`',
            $this->_formater->buildTag(
                'code',
                'My code',
                [
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::buildTag() must build a well-formed tag string with all attributes - code'
        );

        $this->assertEquals(
            '\fBPHP-Markdown-Extended\fP',
            $this->_formater->buildTag(
                'bold',
                'PHP-Markdown-Extended',
                [
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::buildTag() must build a well-formed tag string with all attributes - bold'
        );

    }

}
