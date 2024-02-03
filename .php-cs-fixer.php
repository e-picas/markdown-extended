<?php
/**
 * Application code standards fixer
 *
 * See https://github.com/FriendsOfPHP/PHP-CS-Fixer
 *
 * To use this, run:
 *     $ php bin/php-cs-fixer
 *
 */

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src/')
    ->name('*.php')
    ->name('markdown-extended')
    ->name('mde-dev')
;

$config = new PhpCsFixer\Config();
$config
    ->setUsingCache(false)
    ->setRules([
        '@PSR2' => true,
        '@PSR12' => true,
        '@PHP80Migration' => true,
        '@PHP70Migration' => true,
        '@PHP54Migration' => true,
        'visibility_required' => [
            'elements' => ['method', 'property']
        ],
        'list_syntax' => false,
        'ternary_to_null_coalescing' => false,
        'heredoc_indentation' => false,
    ])
    ->setFinder($finder)
;

return $config;
