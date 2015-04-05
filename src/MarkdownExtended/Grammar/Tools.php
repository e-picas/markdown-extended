<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\AbstractGamut;
use \MarkdownExtended\API\GamutInterface;
use \MarkdownExtended\Exception\RuntimeException;
use \MarkdownExtended\Grammar\GamutLoader;

/**
 * Abstract base class for Tools
 * @package MarkdownExtended\Grammar
 */
class Tools
    extends AbstractGamut
    implements GamutInterface
{

    public static function getDefaultMethod()
    {
        throw new RuntimeException(
            sprintf(
                'You are required to use notation "%s:method" to call a tool as a gamut (no method defined)',
                GamutLoader::TOOLS_ALIAS
            )
        );
    }

    public function prepareOutputFormat($text)
    {
        $output = Kernel::get('OutputFormatBag');
        if (method_exists($output->getFormatter(), 'open')) {
            $text = $output->prepare($text);
        }
        return $text;
    }

    public function teardownOutputFormat($text)
    {
        $output = Kernel::get('OutputFormatBag');
        if (method_exists($output->getFormatter(), 'teardown')) {
            $text = $output->teardown($text);
        }
        return $text;
    }

    /**
     * Make sure $text ends with a couple of newlines
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function AppendEndingNewLines($text)
    {
        return $text."\n\n";
    }

    /**
     * Smart processing for ampersands and angle brackets that need to
     * be encoded. Valid character entities are left alone unless the
     * no-entities mode is set.
     *
     * @param   string  $text   The text to encode
     * @return  string          The encoded text
     */
    public function EncodeAmpAndAngle($text)
    {
        if (Kernel::getConfig('no_entities')) {
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

    /**
     * Encode text for a double-quoted HTML attribute. This function
     * is *not* suitable for attributes enclosed in single quotes.
     *
     * @param   string  $text   The attributes content
     * @return  string          The attributes content processed
     */
    public function EncodeAttribute($text)
    {
        $text = parent::runGamut('tools:EncodeAmpAndAngle', $text);
        $text = str_replace('"', '&quot;', $text);
        return $text;
    }

    /**
     * Extract attributes from string 'a="b"'
     *
     * @param   string  $attributes The attributes to parse
     * @return  string              The attributes processed
     */
    public function ExtractAttributes($attributes)
    {
        $attrs = array();
        $callback = function($matches) use ($attrs) {
            $attrs[$matches[1]] = $matches[3];
        };
        preg_replace_callback('{
            (\S+)=
            (["\']?)                  # $2: simple or double quote or nothing
            (?:
                ([^"|\']\S+|.*?[^"|\']) # anything but quotes
            )
            \\2                       # rematch $2
            }xsi', $callback, $attributes);
        return $attrs;
    }

    /**
     * Remove one level of line-leading tabs or spaces
     *
     * @param   string  $text   The text to be parsed
     * @return  string          The text parsed
     */
    function Outdent($text)
    {
        return preg_replace('/^(\t|[ ]{1,'.Kernel::getConfig('tab_width').'})/m', '', $text);
    }

    /**
     * Process paragraphs
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function RebuildParagraph($text)
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
                $value = Kernel::get('OutputFormatBag')
                    ->buildTag('paragraph', $value);
            }
            $grafs[$key] = $value;
        }

        // Join grafs in one text, then unhash HTML tags.
//      $text = implode("\n\n", $grafs);
        $text = implode('', $grafs);

        // Finish by removing any tag hashes still present in $text.
        $text = parent::runGamut('filter:HTML:unhash', $text, true);

        return $text;
    }

    /**
     * Remove UTF-8 BOM and marker character in input, if present.
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function RemoveUtf8Marker($text)
    {
        return preg_replace('{^\xEF\xBB\xBF|\x1A}', '', $text);
    }

    /**
     * Standardize line endings: DOS to Unix and Mac to Unix
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function StandardizeLineEnding($text)
    {
        return preg_replace('{\r\n?}', "\n", $text);
    }

    /**
     * Strip any lines consisting only of spaces and tabs.
     * This makes subsequent regex easier to write, because we can
     * match consecutive blank lines with /\n+/ instead of something
     * contorted like /[ ]*\n+/ .
     *
     * @param   string  $text   The text to parse
     * @return  string          The text parsed
     */
    public function StripSpacedLines($text)
    {
        return preg_replace('/^[ ]+$/m', '', $text);
    }

}

// Endfile