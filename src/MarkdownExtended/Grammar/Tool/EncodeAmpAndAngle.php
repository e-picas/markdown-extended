<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Tool;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

/**
 * Class EncodeAmpAndAngle
 * @package MarkdownExtended\Grammar\Tool
 */
class EncodeAmpAndAngle
    extends Tool
{

    /**
     * Smart processing for ampersands and angle brackets that need to
     * be encoded. Valid character entities are left alone unless the
     * no-entities mode is set.
     *
     * @param   string  $text   The text to encode
     * @return  string          The encoded text
     */
    public function run($text)
    {
        if (MarkdownExtended::getConfig('no_entities')) {
            $text = str_replace('&', '&amp;', $text);
        } else {
            // Ampersand-encoding based entirely on Nat Irons's Amputator
            // MT plugin: <http://bumppo.net/projects/amputator/>
            $text = preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/', '&amp;', $text);
        }
        // Encode remaining >'s
        $text = str_replace('>', '&gt;', $text);
        // Encode remaining <'s
        $text = str_replace('<', '&lt;', $text);
        return $text;
    }

}

// Endfile