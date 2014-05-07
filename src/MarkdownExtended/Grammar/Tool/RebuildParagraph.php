<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Tool;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

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