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
use MarkdownExtended\OutputFormat\Html;
use MarkdownExtended\Exception\InvalidArgumentException;

class OutputFormatHtmlTest extends ParserTestCase
{

    protected $_formater;

    function setUp():void
    {
        $this->_formater = new Html();
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
            '<h1 attr_name="attr_val">My title</h1>',
            $this->_formater->buildTag(
                'h1',
                'My title',
                [
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::buildTag() must build a well-formed tag string with all attributes - h1'
        );

        $this->assertEquals(
            '<li class="my-li-class" attr_name="attr_val">my content</li>',
            $this->_formater->buildTag(
                'li',
                'my content',
                [
                    'class' => 'my-li-class',
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::buildTag() must build a well-formed tag string with all attributes - li'
        );

        $this->assertEquals(
            '<a href="#the_anchor" attr_name="attr_val">a link</a>',
            $this->_formater->buildTag(
                'a',
                'a link',
                [
                    'href' => '#the_anchor',
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::buildTag() must build a well-formed tag string with all attributes - a'
        );

    }

    /**
     * @param   string  $content        Concerned content
     * @param   string  $tag_name       The tag name to construct
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     *
     * @return  string
    public function getTagString($content, $tag_name, array $attributes = []);
     */
    function testGetTagString()
    {
        $this->assertEquals(
            '<h1 attr_name="attr_val">My title</h1>',
            $this->_formater->getTagString(
                'My title',
                'h1',
                [
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::getTagString() must build a well-formed tag string with all attributes - h1'
        );

        $this->assertEquals(
            '<li class="my-li-class" attr_name="attr_val">my content</li>',
            $this->_formater->getTagString(
                'my content',
                'li',
                [
                    'class' => 'my-li-class',
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::getTagString() must build a well-formed tag string with all attributes - li'
        );

        $this->assertEquals(
            '<a href="#the_anchor" attr_name="attr_val">a link</a>',
            $this->_formater->getTagString(
                'a link',
                'a',
                [
                    'href' => '#the_anchor',
                    'attr_name' => 'attr_val'
                ]
            ),
            '[dev] MarkdownExtended\OutputFormat\Html::getTagString() must build a well-formed tag string with all attributes - a'
        );

    }

}
