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

$tmp_phar = 'tmp/phar-extract';
$phar_path = 'markdown-extended.phar';
if (file_exists($phar_path)) {
    if (file_exists($tmp_phar)) {
        exec("rm -rf $tmp_phar");
    }
    exec("mkdir -p $tmp_phar");
    $phar = new Phar($phar_path);
    $phar->extractTo($tmp_phar);
    echo "> ok, phar extracted to '$tmp_phar'".PHP_EOL;
    exit(0);
} else {
    echo "> no phar found!".PHP_EOL;
    exit(1);
}

// Endfile