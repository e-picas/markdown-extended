<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Show errors at least initially
 *
 * `E_ALL` => for hard dev
 * `E_ALL & ~E_STRICT` => for hard dev in PHP5.4 avoiding strict warnings
 * `E_ALL & ~E_NOTICE & ~E_STRICT` => classic setting
 */
@ini_set('display_errors', '1'); @error_reporting(E_ALL);
//@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_STRICT);
//@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// Set a default timezone to avoid PHP5 warnings
$dtmz = @date_default_timezone_get();
date_default_timezone_set($dtmz ?: 'Europe/Paris');

// get a well-formatted path
$bootstrapGetPath = function ($parts) {
    return implode(DIRECTORY_SEPARATOR, array_map(
        function ($p) {
            return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
        },
        is_array($parts) ? $parts : [$parts]
    ));
};

// parse composer.json
$manifest = $bootstrapGetPath([
    dirname(__DIR__), 'composer.json',
]);
if (file_exists($manifest)) {
    $package = json_decode(file_get_contents($manifest), true);
} else {
    trigger_error(
        sprintf('MarkdownExtended manifest not found (searching "%s")', $manifest),
        E_USER_ERROR
    );
}

// namespaces loader
$bootstrapper = $bootstrapGetPath([
    dirname(__DIR__), 'src', 'bootstrap.php',
]);
if (file_exists($bootstrapper)) {
    require_once $bootstrapper;
} else {
    trigger_error(
        sprintf('MarkdownExtended bootstrapper not found (searching "%s")', $bootstrapper),
        E_USER_ERROR
    );
}

// Custom classes
/*//
if (file_exists($d = __DIR__.'/../src/SplClassLoader.php')) {
    require_once $d;
    $classLoader = new SplClassLoader('MDE_Overrides', __DIR__.'/src');
    $classLoader->register();
}
//*/

// -----------------------------------
// Page Content
// -----------------------------------

// MDE options
$parse_options = [
    'output_format_options.html.codeblock_language_attribute'   => 'data-language',
    'output_format_options.html.codeblock_attribute_mask'       => '%%',
];

// arguments settings
$doc_uri    = isset($_GET['doc'])  ? $_GET['doc'] : null;
$page       = isset($_GET['page']) ? $_GET['page'] : 'home';
$notab      = isset($_GET['notab']) ? (bool) $_GET['notab'] : false;

// contents settings
$errors     = [];
$contents   = [];
$contents_dir = $bootstrapGetPath([
    __DIR__, 'contents',
]) . DIRECTORY_SEPARATOR;

// documentation
$documentations = [];
$doc_dir = $bootstrapGetPath([
    dirname(__DIR__), 'doc',
]);
foreach (scandir($doc_dir) as $f) {
    if (!in_array($f, ['.', '..']) && !is_dir($f)) {
        $documentations[] = '../doc/' . $f;
    }
}

// demonstrations
$demonstrations = [
    'MD_syntax.md',
    'Lorem-Ipsum.md',
    '../CONTRIBUTING.md',
    '../README.md',
    '../mde-manifest.md',
];

// process
if (!is_null($doc_uri)) {
    $doc = realpath(__DIR__ . DIRECTORY_SEPARATOR . $doc_uri);
    if (file_exists($doc)) {
        $contents = include $contents_dir . 'mde-content.php';
    } else {
        $errors[] = printf('Document "%s" not found', $doc);
    }
} else {
    if (!empty($page)) {
        if (file_exists($page . '.php')) {
            $page = $page . '.php';
        } elseif (file_exists($contents_dir . $page . '.php')) {
            $page = $contents_dir . $page . '.php';
        } else {
            unset($page);
            $errors[] = printf('Page "%s" not found', $page);
        }
    }
    if (!empty($page)) {
        $contents = include $page;
    }
}

// layout
include $contents_dir . 'layout.php';
