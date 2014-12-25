<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended;

use \MarkdownExtended\Exception as MDE_Exception;

/**
 * Global Markdown Extended Helper
 * @package MarkdownExtended
 */
class Helper
{

// ----------------------------------
// DEBUG & INFO
// ----------------------------------

    /**
     * Debug function
     *
     * WARNING: first argument is not used (to allow `debug` from Gamut stacks)
     *
     * @param   mixed   $a
     * @param   mixed   $what
     * @param   bool    $exit
     * @return  string
     */
    public function debug($a = '', $what = null, $exit = true) 
    {
        echo '<pre>';
        if (!is_null($what)) {
            var_export($what);
        } else {
            $mde = MarkdownExtended::getInstance();
            var_export($mde::$registry);
        }
        echo '</pre>';
        if ($exit) exit(0);
    }
    
    /**
     * Get information string about the current Markdown Extended object
     *
     * @param   bool    $html
     * @return  string
     */
    public static function info($html = false)
    {
        return (sprintf(
            $html ? 
                '<strong>%1$s</strong> version %2$s (<a href="%3$s" target="_blank" title="See online">%3$s</a>)'
                :
                '%1$s version %2$s (%3$s)',
            MarkdownExtended::MDE_NAME, MarkdownExtended::MDE_VERSION, MarkdownExtended::MDE_SOURCES
        ));
    }

    /**
     * Get information string about the current Markdown Extended object
     *
     * @param   bool    $html
     * @param   bool    $version_only
     * @return  string
     */
    public static function smallInfo($html = false, $version_only = false)
    {
        if ($version_only) {
            return MarkdownExtended::MDE_VERSION;
        } else {
            return (sprintf(
                $html ?
                    '<strong>%1$s</strong> %2$s (<a href="%3$s" target="_blank" title="See online">%3$s</a>)'
                    :
                    '%1$s %2$s'.PHP_EOL.'<%3$s>'
                ,
                MarkdownExtended::MDE_NAME, MarkdownExtended::MDE_VERSION, MarkdownExtended::MDE_SOURCES
            ));
        }
    }

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
     * @return  string
     */
    public static function header2Label($text) 
    {
        // strip all Markdown characters
        $text = str_replace( 
            array("'", '"', "?", "*", "`", "[", "]", "(", ")", "{", "}", "+", "-", ".", "!", "\n", "\r", "\t"), 
            "", strtolower($text) );
        // strip the rest for visual signification
        $text = str_replace( array("#", " ", "__", "/", "\\"), "_", $text );
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
     *   With some optimizations by Milian Wolff.
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

    /**
     * Get a human readable file size
     *
     * @param   string  $file_path
     * @return  string
     */
    public static function getFileSize($file_path)
    {
        if (@file_exists($file_path)) {
            $size = @filesize($file_path);
            if (!empty($size)) {
                if ($size < 1024) {
                    return $size .' B';
                } elseif ($size < 1048576) {
                    return round($size / 1024, 2) .' KiB';
                } elseif ($size < 1073741824) {
                    return round($size / 1048576, 2) . ' MiB';
                } else {
                    return round($size / 1073741824, 2) . ' GiB';
                }
            }
        }
        return '';
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
// Resources finder
// --------------

    /**
     * Find a resource file and return its path
     *
     * @param   string  $file_name
     * @param   string  $type
     * @return  string
     */
    public static function find($file_name, $type = null)
    {
        $resources_dir = __DIR__ . DIRECTORY_SEPARATOR . 'Resources' . DIRECTORY_SEPARATOR;
        if (file_exists($file_name)) {
            return $file_name;
        }
        if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . $file_name)) {
            return __DIR__ . DIRECTORY_SEPARATOR . $file_name;
        }
        if (file_exists($resources_dir . $file_name)) {
            return $resources_dir . $file_name;
        }
        if (!empty($type)) {
            return self::find($type . DIRECTORY_SEPARATOR . $file_name);
        }
        return $file_name;
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

}

// Endfile
