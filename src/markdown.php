<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// get a well-formatted path
$bootstrapGetPath = function(array $parts) {
    return implode(DIRECTORY_SEPARATOR,
        array_map(function($p){ return str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $p); }, $parts));
};

// namespaces loader if needed
if (!defined('MDE_BASE_PATH')) {
    if (file_exists($bootstrapper = $bootstrapGetPath(array(__DIR__, 'bootstrap.php')))) {
        require_once $bootstrapper;

    } else {
        trigger_error(
            sprintf('MarkdownExtended bootstrapper not found (searching "%s")!', $bootstrapper),
            E_USER_ERROR
        );
    }
}

// standard markdown functions for compatibility

/**
 * Transform an input text by the MarkdownExtended
 *
 * @param   string  $text
 * @param   mixed   $options
 * @return  \MarkdownExtended\API\ContentInterface
 */
function MarkdownExtended($text, $options = null)
{
    return \MarkdownExtended\MarkdownExtended::parseString($text, $options);
}

/**
 * Transform an input file name source by the MarkdownExtended
 *
 * @param   string  $file_name
 * @param   mixed   $options
 * @return  \MarkdownExtended\API\ContentInterface
 */
function MarkdownExtendedFromSource($file_name, $options = null)
{
    return \MarkdownExtended\MarkdownExtended::parseSource($file_name, $options);
}

if (!function_exists('Markdown')) {
    /**
     * Transform an input text by the MarkdownExtended
     *
     * @param   string  $text
     * @param   mixed   $options
     * @return  \MarkdownExtended\API\ContentInterface
     */
    function Markdown($text, $options = null)
    {
        return MarkdownExtended($text, $options);
    }
} else {
    trigger_error(
        'The "Markdown" function is already defined and can not be overwritten. '
        . 'To use the MarkdownExtended parser, you must use function "MarkdownExtended()".',
        E_USER_NOTICE
    );
}

if (!function_exists('MarkdownFromSource')) {
    /**
     * Transform an input file name source by the MarkdownExtended
     *
     * @param   string  $file_name
     * @param   mixed   $options
     * @return  \MarkdownExtended\API\ContentInterface
     */
    function MarkdownFromSource($file_name, $options = null)
    {
        return MarkdownExtendedFromSource($file_name, $options);
    }
} else {
    trigger_error(
        'The "MarkdownFromSource" function is already defined and can not be overwritten. '
        . 'To use the MarkdownExtended parser, you must use function "MarkdownExtendedFromSource()".',
        E_USER_NOTICE
    );
}
