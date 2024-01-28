<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use MarkdownExtendedTests\ParserTestCase;
use MarkdownExtended\MarkdownExtended;

class EmphasisTest extends ParserTestCase
{
    public function testInlineEmphasisWithUnderscores()
    {
        $md = '__Hello__ _World_';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello</strong> <em>World</em>',
            '[parsing] test of emphasis'
        );

        $md = '__Hello World__ _is common_';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello World</strong> <em>is common</em>',
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisWithAsterisks()
    {
        $md = '**Hello** *World*';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello</strong> <em>World</em>',
            '[parsing] test of emphasis'
        );

        $md = '**Hello World** *is common*';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello World</strong> <em>is common</em>',
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisMixingBoth()
    {
        $md = '__Hello__ *World*';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello</strong> <em>World</em>',
            '[parsing] test of emphasis'
        );

        $md = '__Hello World__ *is common*';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello World</strong> <em>is common</em>',
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisWithUnderscoresInWord()
    {
        $md = '_Hello_World_';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<em>Hello_World</em>',
            '[parsing] test of emphasis'
        );

        $md = '__Hello_World__ _is_common_';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '<strong>Hello_World</strong> <em>is_common</em>',
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisWithAsterisksInWord()
    {
        $md = 'in-w*or*d em**phas**is';
        $this->assertEquals(
            (string) MarkdownExtended::parse($md, ['template' => false]),
            'in-w<em>or</em>d em<strong>phas</strong>is',
            '[parsing] test of emphasis'
        );



    }
}
