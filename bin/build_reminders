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
