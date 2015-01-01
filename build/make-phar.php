#!/usr/bin/env php
<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// show errors at least initially
@ini_set('display_errors','1'); @error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);

// namespaces loader
require __DIR__.'/../src/bootstrap.php';

// phar compiler
use MarkdownExtended\Util\Compiler;

// silent errors
@error_reporting(-1);

// phar compilation
try {
    $compiler = new Compiler();
    $logs = $compiler->compile();
    echo "> ok, phar generated with files:".PHP_EOL;
    var_export($logs);
    echo PHP_EOL;
    exit(0);
} catch (\Exception $e) {
    echo 'Failed to compile phar: ['.get_class($e).'] '
        .$e->getMessage().' at '.$e->getFile().':'.$e->getLine();
    exit(1);
}

// Endfile