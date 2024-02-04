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
use MarkdownExtended\API\Kernel;

/**
 * Process Markdown horizontal rules
 */
class HorizontalRule extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function transform($text)
    {
        return preg_replace(
            '{
                ^[ ]{0,3}       # Leading space
                ([-*_])         # $1: First marker
                (?>             # Repeated marker group
                    [ ]{0,2}    # Zero, one, or two spaces.
                    \1          # Marker character
                ){2,}           # Group repeated at least twice
                [ ]*            # Tailing spaces
                $               # End of line.
            }mx',
            "\n" . parent::hashBlock(Kernel::get('OutputFormatBag')->buildTag('horizontal_rule')) . "\n",
            $text
        );
    }
}
