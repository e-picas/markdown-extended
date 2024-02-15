<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Util;

use MarkdownExtended\API\Kernel;
use MarkdownExtended\Exception\FileSystemException;

/**
 * Helper class with static methods only
 */
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
        return !is_null($text) ? str_replace('%%', $replacement, $text) : $text;
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
            ["'", '"', "?", "*", "`", "[", "]", "(", ")", "{", "}", "+", "-", ".", "!", "\n", "\r", "\t"],
            "",
            strtolower($text)
        );
        // strip the rest for visual signification
        $text = str_replace(["#", " ", "__", "/", "\\"], $separator, $text);
        // strip non-ascii characters
        return preg_replace("/[^\x9\xA\xD\x20-\x7F]/", "", $text);
    }

    /**
     * Transform a string to a human readable one
     *
     * @param   string $string The string to transform
     * @return  string The transformed version of `$string`
     */
    public static function humanReadable($string = '')
    {
        return trim(str_replace(['_', '.', '/'], ' ', $string));
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
     *  Based on a filter by Matthew Wickline, posted to BBEdit-Talk.
     *  With some optimizations by Milian Wolff.
     *
     * @param   string  $addr   The email address to encode
     * @return  string  The encoded address
     */
    public static function encodeEmailAddress($addr)
    {
        $addr   = "mailto:" . $addr;
        $chars  = preg_split('/(?<!^)(?!$)/', $addr);
        $seed   = (int)abs(crc32($addr) / strlen($addr)); // Deterministic seed.
        foreach ($chars as $key => $char) {
            $ord = ord($char);
            // Ignore non-ascii chars.
            if ($ord < 128) {
                $rand = ($seed * (1 + $key)) % 100; // Pseudo-random function.
                // roughly 10% raw, 45% hex, 45% dec
                // '@' *must* be encoded. I insist.
                if ($rand > 90 && $char != '@') {
                    /* do nothing */;
                } elseif ($rand < 45) {
                    $chars[$key] = '&#x'.dechex($ord).';';
                } else {
                    $chars[$key] = '&#'.$ord.';';
                }
            }
        }
        $addr = implode('', $chars);
        $text = implode('', array_slice($chars, 7)); // text without `mailto:`
        return [$addr, $text];
    }

    /**
     * Gets a "safe string" from a `\DateTime` or an array
     *
     * @param mixed $source
     * @return string
     */
    public static function getSafeString($source)
    {
        $str = $source;

        if (!is_string($source)) {
            if ($source instanceof \DateTime) {
                $str = Kernel::applyConfig('date_to_string', [$source]);
            } elseif (is_array($source)) {
                $str = '';
                foreach ($source as $var => $val) {
                    $str .= $var . ': ' . self::getSafeString($val) . PHP_EOL;
                }
            }
        }

        return $str;
    }

    /**
     * Tests if a content seems to be single line
     *
     * @param string $str
     * @return bool
     */
    public static function isSingleLine($str = '')
    {
        return (bool) (false === strpos($str, PHP_EOL));
    }

    /**
     * Extract all contents of a specific HTML tag from a content
     *
     * @param string $string The HTML string to search in
     * @param string $tagname The tagname to extract
     *
     * @return array
     */
    public static function getTextBetweenTags($string, $tagname)
    {
        $d = new \DOMDocument();
        $d->loadHTML($string);
        $return = [];
        foreach($d->getElementsByTagName($tagname) as $item) {
            $return[] = $item->textContent;
        }
        return $return;
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
        $replacements = [
            '.' => '\\.',
            '*' => '.*',
            $delimiter => '\\'.$delimiter,
        ];
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
        return self::_camelcasize(
            $capitalize_first_char ? ucfirst($name) : $name,
            $replace,
            $replace.'([a-z])',
            function ($matches) {
                return ucfirst($matches[1]);
            }
        );
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
        return self::_camelcasize(
            $lowerize_first_char ? strtolower(substr($name, 0, 1)) . substr($name, 1) : $name,
            $replace,
            '([A-Z])',
            function ($matches) use ($replace) {
                return $replace . strtolower($matches[1]);
            }
        );
    }

    /**
     * Actually camel-casize
     *
     * @param string $text The source text to transform
     * @param string $replace The replacement string to put in the mask
     * @param string $mask The mask to match substrings to transform
     * @param callable $callback The callback function
     *
     * @return string
     */
    protected static function _camelcasize($text, $replace, $mask, $callback)
    {
        if (empty($text)) {
            return $text;
        }
        return trim(
            preg_replace_callback(
                self::buildRegex($mask),
                $callback,
                $text
            ),
            $replace
        );
    }

    // --------------
    // Files
    // --------------

    /**
     * Gets a wel-formatted path with environment-compliant directory separator
     *
     * @param array|string $parts
     * @return string
     */
    public static function getPath($parts)
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_map(
                function ($p) {
                    return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
                },
                is_array($parts) ? $parts : [$parts]
            )
        );
    }

    /**
     * Reads a file and returns its content
     *
     * @param string $path
     * @param int $flag
     *
     * @return string
     *
     * @throws \MarkdownExtended\Exception\FileSystemException if the file can not be found or read
     */
    public static function readFile($path, $flag = FILE_USE_INCLUDE_PATH)
    {
        if (!file_exists($path)) {
            throw new FileSystemException(
                sprintf('File "%s" not found', $path)
            );
        }
        $source = file_get_contents($path, $flag);
        if (false === $source) {
            global $php_errormsg;
            throw new FileSystemException(
                !empty($php_errormsg) ? $php_errormsg :
                    sprintf('An error occurred while trying to read file "%s"', $path)
            );
        }
        return $source;
    }

    /**
     * Writes a file and returns content length
     *
     * @param string $path
     * @param string|array $content
     * @param bool $backup
     * @param null $flag
     *
     * @return int
     *
     * @throws \MarkdownExtended\Exception\FileSystemException if the file path can not be created or the file can not be written
     */
    public static function writeFile($path, $content, $backup = true, $flag = null)
    {
        global $php_errormsg;
        if (is_null($flag)) {
            $flag = FILE_USE_INCLUDE_PATH | LOCK_EX;
        }
        if (file_exists($path) && $backup) {
            self::backupFile($path);
        }
        if (!file_exists(dirname($path))) {
            $dir = mkdir(dirname($path));
            if (false === $dir) {
                throw new FileSystemException(
                    !empty($php_errormsg) ? $php_errormsg :
                        sprintf('An error occurred while trying to create directory "%s"', dirname($path))
                );
            }
        }
        $written = file_put_contents($path, $content, $flag);
        if (false === $written) {
            throw new FileSystemException(
                !empty($php_errormsg) ? $php_errormsg :
                    sprintf('An error occurred while trying to write data in file "%s"', $path)
            );
        }
        return $written;
    }

    /**
     * Make a backup of a file
     *
     * @param string $path
     * @return bool
     */
    public static function backupFile($path)
    {
        $new_path = $path . '~' . date('y-m-d-H-i-s');
        if (file_exists($new_path)) {
            $counter = 0;
            $original_new_path = $new_path;
            while (file_exists($new_path)) {
                $new_path = $original_new_path . $counter;
                $counter++;
            }
        }
        return copy($path, $new_path);
    }

    // --------------
    // Dev utilities
    // --------------

    /**
     * Dump quite anything with an optional title
     *
     * @param mixed $objs
     * @param null|string $title
     * @param bool $html
     *
     * @return string
     */
    public static function debug($objs, $title = null, $html = true)
    {
        $str    = '';
        $newl   = PHP_EOL . ($html ? '<br />' : '');
        if (!empty($title)) {
            $str .= $newl . '### ' . $title . ':' . $newl;
        }
        $dump = var_export($objs, true);
        $replacements = [
            PHP_EOL     => ' ',
            '  '        => ' ',
            ',  \''     => ', \'',
            ' => '      => '=>',
            ' ('        => '(',
            '( '        => '(',
            ' )'        => ')',
            ') '        => ')',
            ',) '       => ')',
        ];
        $str .= str_replace(array_keys($replacements), array_values($replacements), $dump);
        return $str;
    }
}
