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
use MarkdownExtended\Grammar\Lexer;
use MarkdownExtended\Util\Helper;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\Grammar\GamutLoader;

/**
 * Process Markdown code blocks
 */
class CodeBlock extends Filter
{
    /**
     *  Process Markdown `<pre><code>` blocks.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback(
            '{
                (?:\n\n|\A\n?)
                (                                                     # $1 = the code block -- one or more lines, starting with a space/tab
                  (?>
                    [ ]{'.Kernel::getConfig('tab_width').'} # Lines must start with a tab or a tab-width of spaces
                    .*\n+
                  )+
                )
                ((?=^[ ]{0,'.Kernel::getConfig('tab_width').'}\S)|\Z) # Lookahead for non-space at line-start, or end of doc
            }xm',
            [$this, '_callback'],
            $text
        );
    }

    /**
     * Build `<pre><code>` blocks.
     *
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $codeblock = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':Outdent', $matches[1]);
        $codeblock = Helper::escapeCodeContent($codeblock);
        # trim leading newlines and trailing newlines
        $codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);
        $codeblock = Kernel::get('OutputFormatBag')
            ->buildTag('preformatted', $codeblock);
        return "\n\n".parent::hashBlock($codeblock)."\n\n";
    }

    /**
     * Create a code span markup for $code. Called from handleSpanToken.
     *
     * @param   string  $code
     * @return  string
     */
    public function span($code)
    {
        $codeblock = Kernel::get('OutputFormatBag')
            ->buildTag('code', Helper::escapeCodeContent(trim($code)));
        return parent::hashPart($codeblock);
    }
}
