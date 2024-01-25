<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// get a well-formatted path
$bootstrapGetPath = function ($parts) {
    return implode(DIRECTORY_SEPARATOR, array_map(
        function ($p) {
            return str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $p);
        },
        is_array($parts) ? $parts : [$parts]
    ));
};

// the global bootstrapper
if (file_exists($bootstrapper = $bootstrapGetPath([
    dirname(__DIR__), 'src', 'bootstrap.php',
]))) {
    require_once $bootstrapper;
} else {
    trigger_error(
        sprintf('MarkdownExtended bootstrapper not found (searching "%s")', $bootstrapper),
        E_USER_ERROR
    );
}

// register the MarkdownExtendedTests namespace
spl_autoload_register(function ($name) {
    mde_autoloader($name, 'MarkdownExtendedTests', __DIR__);
});
