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
 * Class RebuildParagraph
 * @package MarkdownExtended\Grammar\Tool
 */
class RebuildParagraph
    extends Tool
{

    /**
     * Process paragraphs
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function run($text)
    {
        // Strip leading and trailing lines:
        $text = preg_replace('/\A\n+|\n+\z/', '', $text);

        $grafs = preg_split('/\n{2,}/', $text, -1, PREG_SPLIT_NO_EMPTY);

        // Wrap <p> tags and unhashify HTML blocks
        foreach ($grafs as $key => $value) {
            $value = trim(parent::runGamut('span_gamut', $value));

            // Check if this should be enclosed in a paragraph.
            // Clean tag hashes & block tag hashes are left alone.
            $is_p = !preg_match('/^B\x1A[0-9]+B|^C\x1A[0-9]+C$/', $value);

            if ($is_p) {
                $value = MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('paragraph', $value);
            }
            $grafs[$key] = $value;
        }

        // Join grafs in one text, then unhash HTML tags.
//      $text = implode("\n\n", $grafs);
        $text = implode('', $grafs);

        // Finish by removing any tag hashes still present in $text.
        $text = parent::runGamut('filter:HTML:unhash', $text);

        return $text;
    }

}

// Endfile