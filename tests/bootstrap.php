<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../src/SplClassLoader.php';
$classLoader = new SplClassLoader('MarkdownExtended', __DIR__.'/../src');
$classLoader->register();
$classLoader_tests = new SplClassLoader('testsMarkdownExtended', __DIR__.'/../tests');
$classLoader_tests->register();
