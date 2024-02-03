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
 * Process Markdown blockquotes
 *
 * Blockquotes may be written like:
 *
 *      > Citation text
 *          multi-line if required and **tagged**
 *
 */
class BlockQuote extends Filter
{
    /**
     * Create blockquotes blocks
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback(
            '/
              (                         # Wrap whole match in $1
                (?>
                  ^[ ]*>[ ]?            # ">" at the start of a line
                    (?:\((.+?)\))?
                    .+\n                # rest of the first line
                  (.+\n)*               # subsequent consecutive lines
                  \n*                   # blanks
                )+
              )
            /xm',
            [$this, '_callback'],
            $text
        );
    }

    /**
     * Build each blockquote block
     *
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $blockq = $matches[1];
        $cite = isset($matches[2]) ? $matches[2] : null;
        // trim one level of quoting - trim whitespace-only lines
        $blockq = preg_replace('/^[ ]*>[ ]?(\((.+?)\))?|^[ ]+$/m', '', $blockq);
        $blockq = Lexer::runGamut('html_block_gamut', $blockq); # recurse
        $blockq = preg_replace('/^/m', "  ", $blockq);
        // These leading spaces cause problem with <pre> content,
        // so we need to fix that:
        $blockq = preg_replace_callback('{(\s*<pre>.+?</pre>)}sx', [$this, '_callback_spaces'], $blockq);

        $attributes = [];
        if (!empty($cite)) {
            $attributes['cite'] = $cite;
        }
        $block = Kernel::get('OutputFormatBag')
    //            ->buildTag('blockquote', "\$blockq\n", $attributes);
            ->buildTag('blockquote', $blockq, $attributes);
        return "\n" . parent::hashBlock($block) . "\n\n";
    }

    /**
     * Deletes the last spaces, for <pre> blocks
     *
     * @param   array   $matches    A set of results of the `_callback()` function
     * @return  string
     */
    protected function _callback_spaces($matches)
    {
        $pre = $matches[1];
        $pre = preg_replace('/^  /m', '', $pre);
        return $pre;
    }
}
