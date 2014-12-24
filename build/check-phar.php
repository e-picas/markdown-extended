#!/usr/bin/env php
<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
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