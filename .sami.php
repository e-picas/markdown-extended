<?php
/**
 * Application documentation builder
 *
 * See https://github.com/fabpot/Sami
 *
 * To build doc, run:
 *     $ php bin/sami.php render .sami.php
 *
 * To update it, run:
 *     $ php bin/sami.php update .sami.php
 *
 */

use Sami\Sami;
use Symfony\Component\Finder\Finder;

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->exclude('MarkdownExtendedDev')
    ->notName('bootstrap*.php')
    ->in(__DIR__.'/src')
;

$options = array(
    'title'                => 'Markdown Extended',
    'build_dir'            => __DIR__.'/phpdoc',
    'cache_dir'            => __DIR__.'/../tmp/cache/markdown-extended',
    'default_opened_level' => 1,
);

return new Sami($iterator, $options);

// Endfile
