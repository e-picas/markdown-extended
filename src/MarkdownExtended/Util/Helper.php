<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use \MarkdownExtended\Exception\InvalidArgumentException;

class Helper
{

// --------------
// Strings
// --------------

    /**
     * Escape the code blocks contents to get HTML entities
     *
     * @param   string  $code
     * @return  string
     */
    public static function escapeCodeContent($code)
    {
        return htmlspecialchars($code, ENT_NOQUOTES);
    }

    /**
     * Replace any `%%` mask by a replacement
     *
     * @param   string  $text
     * @param   string  $replacement
     * @return  string
     */
    public static function fillPlaceholders($text, $replacement)
    {
        return str_replace('%%', $replacement, $text);
    }

    /**
     * Transform a header string to DOM valid label
     *
     * @param   string  $text
     * @param   string  $separator
     * @return  string
     */
    public static function header2Label($text, $separator = '-')
    {
        // strip all Markdown characters
        $text = str_replace(
            array("'", '"', "?", "*", "`", "[", "]", "(", ")", "{", "}", "+", "-", ".", "!", "\n", "\r", "\t"),
            "", strtolower($text) );
        // strip the rest for visual signification
        $text = str_replace( array("#", " ", "__", "/", "\\"), $separator, $text );
        // strip non-ascii characters
        return preg_replace("/[^\x9\xA\xD\x20-\x7F]/", "", $text);
    }

    /**
     *  Input: an email address, e.g. "foo@example.com"
     *
     *  Output: the email address as a mailto link, with each character
     *      of the address encoded as either a decimal or hex entity, in
     *      the hopes of foiling most address harvesting spam bots. E.g.:
     *
     *    <p><a href="&#109;&#x61;&#105;&#x6c;&#116;&#x6f;&#58;&#x66;o&#111;
     *        &#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;&#101;&#46;&#x63;&#111;
     *        &#x6d;">&#x66;o&#111;&#x40;&#101;&#x78;&#97;&#x6d;&#112;&#x6c;
     *        &#101;&#46;&#x63;&#111;&#x6d;</a></p>
     *
     *  Based by a filter by Matthew Wickline, posted to BBEdit-Talk.
     *  With some optimizations by Milian Wolff.
     *
     * @param   string  $addr   The email address to encode
     * @return  string  The encoded address
     */
    public static function encodeEmailAddress($addr)
    {
        $addr = "mailto:" . $addr;
        $chars = preg_split('/(?<!^)(?!$)/', $addr);
        $seed = (int)abs(crc32($addr) / strlen($addr)); // Deterministic seed.
        foreach ($chars as $key => $char) {
            $ord = ord($char);
            // Ignore non-ascii chars.
            if ($ord < 128) {
                $r = ($seed * (1 + $key)) % 100; // Pseudo-random function.
                // roughly 10% raw, 45% hex, 45% dec
                // '@' *must* be encoded. I insist.
                if ($r > 90 && $char != '@') /* do nothing */;
                else if ($r < 45) $chars[$key] = '&#x'.dechex($ord).';';
                else              $chars[$key] = '&#'.$ord.';';
            }
        }
        $addr = implode('', $chars);
        $text = implode('', array_slice($chars, 7)); // text without `mailto:`
        return array($addr, $text);
    }

    public static function getSafeString($source)
    {
        $str = $source;

        if (!is_string($source)) {

            if ($source instanceof \DateTime) {
                $str = $source->format(DATE_W3C);
            } elseif (is_array($source)) {
                $str = '';
                foreach ($source as $var=>$val) {
                    $str .= $var . ': ' . self::getSafeString($val) . PHP_EOL;
                }
            }

        }

        return $str;
    }

    public static function isSingleLine($str = '')
    {
        return (bool) (false === strpos($str, PHP_EOL));
    }

// --------------
// Regular expressions
// --------------

    /**
     * Get a ready-to-use regular expression from a string pattern
     *
     * @param   string  $mask       The string to construct the expression
     * @param   string  $delimiter  The delimiter to use for the expression (default is `#`)
     * @param   string  $options    The options to use for the expression (default is `i`)
     * @return  string
     */
    public static function buildRegex($mask, $delimiter = '#', $options = 'i')
    {
        $replacements = array(
            '.'=>'\\.',
            '*'=>'.*',
            $delimiter=>'\\'.$delimiter
        );
        return $delimiter
        .strtr($mask, $replacements)
        .$delimiter.$options;
    }

// --------------
// Classes & Vars name builders
// --------------

    /**
     * Transform a name in CamelCase
     *
     * @param   string  $name       The string to transform
     * @param   string  $replace    Replacement character
     * @param   bool    $capitalize_first_char  May the first letter be in upper case (default is `true`)
     * @return  string  The CamelCase version of `$name`
     */
    public static function toCamelCase($name, $replace = '_', $capitalize_first_char = true)
    {
        if (empty($name)) return $name;
        if ($capitalize_first_char) {
            $name[0] = strtoupper($name[0]);
        }
        static $toCamelCase_func;
        if (empty($toCamelCase_func)) {
            $toCamelCase_func = create_function('$c', 'return strtoupper($c[1]);');
        }
        return trim(preg_replace_callback('#'.$replace.'([a-z])#', $toCamelCase_func, $name), $replace);
    }

    /**
     * Transform a name from CamelCase to other
     *
     * @param   string  $name       The string to transform
     * @param   string  $replace    Replacement character
     * @param   bool    $lowerize_first_char  May the first letter be in lower case (default is `true`)
     * @return  string  The not_camel_case version of `$name`
     */
    public static function fromCamelCase($name, $replace = '_', $lowerize_first_char = true)
    {
        if (empty($name)) return $name;
        if ($lowerize_first_char) {
            $name[0] = strtolower($name[0]);
        }
        static $fromCamelCase_func;
        if (empty($fromCamelCase_func)) {
            $fromCamelCase_func = create_function('$c', 'return "'.$replace.'" . strtolower($c[1]);');
        }
        return trim(preg_replace_callback('/([A-Z])/', $fromCamelCase_func, $name), $replace);
    }

// --------------
// Files
// --------------

    // get a well-formatted path
    public static function getPath(array $parts)
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(function($p){
                return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $p);
            }, $parts)
        );
    }

    public static function backupFile($path)
    {
        $new_path = $path . '~' . date('y-m-d-H-i-s');
        if (file_exists($new_path)) {
            $i = 0;
            $original_new_path = $new_path;
            while (file_exists($new_path)) {
                $new_path = $original_new_path . $i;
                $i++;
            }
        }
        return copy($path, $new_path);
    }

// --------------
// Dev utilities
// --------------

    public static function debug($objs, $title = null, $html = true)
    {
        $str    = '';
        $nl     = PHP_EOL . ($html ? '<br />' : '');
        if (!empty($title)) {
            $str .= $nl . '### ' . $title . ':' . $nl;
        }
        $dump = var_export($objs, true);
        $replacements = array(
            PHP_EOL     => ' ',
            '  '        => ' ',
            ',  \''     => ', \'',
            ' => '      => '=>',
            ' ('        => '(',
            '( '        => '(',
            ' )'        => ')',
            ') '        => ')',
            ',) '       => ')',
        );
        $str .= str_replace(array_keys($replacements), array_values($replacements), $dump);
        return $str;
    }

}

// Endfile
