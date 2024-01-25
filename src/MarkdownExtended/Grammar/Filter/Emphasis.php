<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\Grammar\Lexer;

/**
 * Process Markdown emphasis: bold & italic
 */
class Emphasis extends Filter
{
    /**#@+
     * Redefining emphasis markers so that emphasis by underscore does not
     * work in the middle of a word.
     */
    public $em_relist = [
        ''  => '(?:(?<!\*)\*(?!\*)|(?<![a-zA-Z0-9_])_(?!_))(?=\S|$)(?![\.,:;]\s)',
        '*' => '(?<=\S|^)(?<!\*)\*(?!\*)',
        '_' => '(?<=\S|^)(?<!_)_(?![a-zA-Z0-9_])',
    ];

    public $strong_relist = [
        ''   => '(?:(?<!\*)\*\*(?!\*)|(?<![a-zA-Z0-9_])__(?!_))(?=\S|$)(?![\.,:;]\s)',
        '**' => '(?<=\S|^)(?<!\*)\*\*(?!\*)',
        '__' => '(?<=\S|^)(?<!_)__(?![a-zA-Z0-9_])',
    ];

    public $em_strong_relist = [
        ''    => '(?:(?<!\*)\*\*\*(?!\*)|(?<![a-zA-Z0-9_])___(?!_))(?=\S|$)(?![\.,:;]\s)',
        '***' => '(?<=\S|^)(?<!\*)\*\*\*(?!\*)',
        '___' => '(?<=\S|^)(?<!_)___(?![a-zA-Z0-9_])',
    ];

    public static $em_strong_prepared;
    /**#@-*/

    /**
     * Prepare regular expressions for searching emphasis tokens in any context.
     */
    public function prepare()
    {
        foreach ($this->em_relist as $em => $em_re) {
            foreach ($this->strong_relist as $strong => $strong_re) {
                // Construct list of allowed token expressions.
                $token_relist = [];
                if (isset($this->em_strong_relist["$em$strong"])) {
                    $token_relist[] = $this->em_strong_relist["$em$strong"];
                }
                $token_relist[] = $em_re;
                $token_relist[] = $strong_re;

                // Construct master expression from list.
                $token_re = '{('. implode('|', $token_relist) .')}';
                self::$em_strong_prepared["$em$strong"] = $token_re;
            }
        }
    }

    /**
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        $token_stack = [''];
        $text_stack = [''];
        $italic = '';
        $strong = '';
        $tree_char_em = false;

        while (1) {

            // Get prepared regular expression for seraching emphasis tokens in current context.
            $token_re = self::$em_strong_prepared["$italic$strong"];

            // Each loop iteration search for the next emphasis token.
            // Each token is then passed to handleSpanToken.
            $parts = preg_split($token_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE);
            $text_stack[0] .= $parts[0];
            $token = & $parts[1];
            $text = & $parts[2];

            if (empty($token)) {
                // Reached end of text span: empty stack without emitting any more emphasis.
                while ($token_stack[0]) {
                    $text_stack[1] .= array_shift($token_stack);
                    $text_stack[0] .= array_shift($text_stack);
                }
                break;
            }

            $token_len = strlen($token);
            if ($tree_char_em) {
                // Reached closing marker while inside a three-char emphasis.
                if ($token_len == 3) {
                    // Three-char closing marker, close em and strong.
                    array_shift($token_stack);
                    $span = Lexer::runGamut('span_gamut', array_shift($text_stack));
                    $span = Kernel::get('OutputFormatBag')
                        ->buildTag('italic', $span);
                    $span = Kernel::get('OutputFormatBag')
                        ->buildTag('bold', $span);
                    $text_stack[0] .= parent::hashPart($span);
                    $italic = '';
                    $strong = '';
                } else {
                    // Other closing marker: close one em or strong and
                    // change current token state to match the other
                    $token_stack[0] = str_repeat($token[0], 3 - $token_len);
                    $tag = $token_len == 2 ? "bold" : "italic";
                    $span = Lexer::runGamut('span_gamut', $text_stack[0]);
                    $span = Kernel::get('OutputFormatBag')
                        ->buildTag($tag, $span);
                    $text_stack[0] = parent::hashPart($span);
                    $$tag = ''; // $$tag stands for $italic or $strong
                }
                $tree_char_em = false;
            } elseif ($token_len == 3) {
                if ($italic) {
                    // Reached closing marker for both em and strong.
                    // Closing strong marker:
                    for ($i = 0; $i < 2; ++$i) {
                        $shifted_token = array_shift($token_stack);
                        $tag = strlen($shifted_token) == 2 ? "bold" : "italic";
                        $span = Lexer::runGamut('span_gamut', array_shift($text_stack));
                        $span = Kernel::get('OutputFormatBag')
                            ->buildTag($tag, $span);
                        $text_stack[0] .= parent::hashPart($span);
                        $$tag = ''; // $$tag stands for $italic or $strong
                    }
                } else {
                    // Reached opening three-char emphasis marker. Push on token
                    // stack; will be handled by the special condition above.
                    $italic = $token[0];
                    $strong = "$italic$italic";
                    array_unshift($token_stack, $token);
                    array_unshift($text_stack, '');
                    $tree_char_em = true;
                }
            } elseif ($token_len == 2) {
                if ($strong) {
                    // Unwind any dangling emphasis marker:
                    if (strlen($token_stack[0]) == 1) {
                        $text_stack[1] .= array_shift($token_stack);
                        $text_stack[0] .= array_shift($text_stack);
                    }
                    // Closing strong marker:
                    array_shift($token_stack);
                    $span = Lexer::runGamut('span_gamut', array_shift($text_stack));
                    $span = Kernel::get('OutputFormatBag')
                        ->buildTag('bold', $span);
                    $text_stack[0] .= parent::hashPart($span);
                    $strong = '';
                } else {
                    array_unshift($token_stack, $token);
                    array_unshift($text_stack, '');
                    $strong = $token;
                }
            } else {
                // Here $token_len == 1
                if ($italic) {
                    if (strlen($token_stack[0]) == 1) {
                        // Closing emphasis marker:
                        array_shift($token_stack);
                        $span = Lexer::runGamut('span_gamut', array_shift($text_stack));
                        $span = Kernel::get('OutputFormatBag')
                            ->buildTag('italic', $span);
                        $text_stack[0] .= parent::hashPart($span);
                        $italic = '';
                    } else {
                        $text_stack[0] .= $token;
                    }
                } else {
                    array_unshift($token_stack, $token);
                    array_unshift($text_stack, '');
                    $italic = $token;
                }
            }
        }
        return $text_stack[0];
    }
}
