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

// launch console API
if (php_sapi_name() === 'cli') { 
    @set_time_limit(0);
    \MarkdownExtended\MarkdownExtended::getInstance()
        ->get('CommandLine\Reminders')
        ->run();
} else {
    echo 'This file is for command line usage only!';
    exit(1);
}

// Endfile
