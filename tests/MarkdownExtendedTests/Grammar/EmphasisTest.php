<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
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
            '<strong>Hello</strong> <em>World</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );

        $md = '__Hello World__ _is common_';
        $this->assertEquals(
            '<strong>Hello World</strong> <em>is common</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisWithAsterisks()
    {
        $md = '**Hello** *World*';
        $this->assertEquals(
            '<strong>Hello</strong> <em>World</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );

        $md = '**Hello World** *is common*';
        $this->assertEquals(
            '<strong>Hello World</strong> <em>is common</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisMixingBoth()
    {
        $md = '__Hello__ *World*';
        $this->assertEquals(
            '<strong>Hello</strong> <em>World</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );

        $md = '__Hello World__ *is common*';
        $this->assertEquals(
            '<strong>Hello World</strong> <em>is common</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisWithUnderscoresInWord()
    {
        $md = '_Hello_World_';
        $this->assertEquals(
            '<em>Hello_World</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );

        $md = '__Hello_World__ _is_common_';
        $this->assertEquals(
            '<strong>Hello_World</strong> <em>is_common</em>',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );
    }

    public function testInlineEmphasisWithAsterisksInWord()
    {
        $md = 'in-w*or*d em**phas**is';
        $this->assertEquals(
            'in-w<em>or</em>d em<strong>phas</strong>is',
            (string) MarkdownExtended::parse($md, ['template' => false]),
            '[parsing] test of emphasis'
        );



    }
}
