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

/**
 * Process Markdown mathematics
 *
 * taken from <http://github.com/drdrang/php-markdown-extra-math>
 */
class Maths extends Filter
{
    /**
     * Wrap text between \[ and \] in display math tags.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback(
            '{
              ^\\\\                         # line starts with a single backslash (double escaping)
              \[                            # followed by a square bracket
              (.+)                          # then the actual LaTeX code
              \\\\                          # followed by another backslash
              \]                            # and closing bracket
              \s*$                          # and maybe some whitespace before the end of the line
            }mx',
            [$this, '_callback'],
            $text
        );
    }

    /**
     * Build each maths block
     *
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $texblock   = $matches[1];
        $texblock   = trim($texblock);
        $block      = Kernel::get('OutputFormatBag')
            ->buildTag('maths_block', $texblock, []);
        return "\n\n".parent::hashBlock($block)."\n\n";
    }

    /**
     * Build each maths span
     *
     * @param   string   $texblock
     * @return  string
     */
    public function span($texblock)
    {
        $texblock   = trim($texblock);
        $block      = Kernel::get('OutputFormatBag')
            ->buildTag('maths_span', $texblock, []);
        return parent::hashPart($block);
    }
}
