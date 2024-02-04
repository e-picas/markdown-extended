<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Util\Helper;
use MarkdownExtended\API\Kernel;

/**
 * Process Markdown fenced code blocks
 *
 * Fenced code blocks may be written like:
 *
 *      ~~~~(language)
 *      my content ...
 *      ~~~~
 *
 */
class FencedCodeBlock extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function transform($text)
    {
        return preg_replace_callback(
            '{
                (?:\n|\A)               # 1: Opening marker
                (
                    ~{3,}|`{3,}         # Marker: three tildes or backticks or more.
                )
                (\w+)?                  # 2: Language
                [ ]* \n                 # Whitespace and newline following marker.
                (                       # 3: Content
                    (?>
                        (?!\1 [ ]* \n)  # Not a closing marker.
                        .*\n+
                    )+
                )
                \1 [ ]* \n              # Closing marker
            }xm',
            [$this, '_callback'],
            $text
        );
    }

    /**
     * Process the fenced code blocks
     *
     * @param   array   $matches    Results form the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $language  = $matches[2];
        $codeblock = Helper::escapeCodeContent($matches[3]);
        $codeblock = preg_replace_callback('/^\n+/', [$this, '_newlines'], $codeblock);

        $attributes = [];
        if (!empty($language)) {
            $attributes['language'] = $language;
        }
        $codeblock = Kernel::get('OutputFormatBag')
            ->buildTag('preformatted', $codeblock, $attributes);
        return "\n\n" . parent::hashBlock($codeblock) . "\n\n";
    }

    /**
     * Process the fenced code blocks new lines
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _newlines($matches)
    {
        return str_repeat(Kernel::get('OutputFormatBag')->buildTag('new_line'), strlen($matches[0]));
    }
}
