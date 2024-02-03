<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// PHP 5.6+
if (version_compare(PHP_VERSION, '5.6', '<')) {
    trigger_error(
        sprintf('The "MarkdownExtended" application requires PHP version 5.6 minimum (current running version is %s)', PHP_VERSION),
        E_USER_ERROR
    );
}

// PHP 8: suppress deprecated warnings
if (version_compare(PHP_VERSION, '8', '>=')) {
    error_reporting(error_reporting() & ~E_DEPRECATED);
}

// set a default timezone to avoid PHP5 warnings
$dtmz = @date_default_timezone_get();
@date_default_timezone_set($dtmz ?: 'UTC');

// get a well-formatted path
$bootstrapGetPath = function ($parts) {
    return implode(DIRECTORY_SEPARATOR, array_map(
        function ($p) {
            return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
        },
        is_array($parts) ? $parts : [$parts]
    ));
};

// MDE_BASE_PATH = PHAR or local base path
if (!defined('MDE_BASE_PATH')) {
    define(
        'MDE_BASE_PATH',
        (defined('MDE_PHAR') && MDE_PHAR === true) ? 'phar://mde.phar/' : dirname(__DIR__).DIRECTORY_SEPARATOR
    );
}

// namespaces autoloader
if (!function_exists('mde_autoloader')) {
    function mde_autoloader($className, $namespace = 'MarkdownExtended', $base_path = null)
    {
        $extension = '.php';
        $namespace_separator = '\\';
        $className = trim($className, $namespace_separator);
        $base_path = is_null($base_path) ? MDE_BASE_PATH.'src' : rtrim($base_path, DIRECTORY_SEPARATOR);
        if (substr($className, 0, strlen($namespace)) === $namespace) {
            $class_file = str_replace([$namespace_separator, '_'], DIRECTORY_SEPARATOR, $className).$extension;
            if (file_exists($try1 = $base_path.DIRECTORY_SEPARATOR.$class_file)) {
                require $try1;
            }
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
                if (file_exists($try2 = $path.DIRECTORY_SEPARATOR.$class_file)) {
                    require $try2;
                }
            }
        }
    }
}

// register the MarkdownExtended namespace loader
spl_autoload_register('mde_autoloader');

// try the project's Composer autoloader
if (file_exists($a = $bootstrapGetPath([
    dirname(dirname(dirname(__DIR__))), 'autoload.php',
]))) {
    require_once $a;

    // else try local Composer autoloader
} elseif (file_exists($b = $bootstrapGetPath([
    dirname(__DIR__), 'vendor', 'autoload.php',
]))) {
    require_once $b;
}
