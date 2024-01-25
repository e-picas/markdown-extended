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
    ->in(__DIR__)
    ->name('*.php')
    ->name('markdown-extended')
    ->name('mde-dev')
;

$config = new PhpCsFixer\Config();
$config
    ->setUsingCache(false)
    ->setRules([
        '@PHP74Migration' => true,
        '@PSR12' => true,
    ])
    ->setFinder($finder)
;

return $config;
