<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// the global bootstrapper
require_once __DIR__.'/../src/bootstrap.php';

// register the testsMarkdownExtended namespace
spl_autoload_register(function($name) {
    mde_autoloader($name, 'testsMarkdownExtended', __DIR__);
});
