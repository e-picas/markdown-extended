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

$finder = Symfony\CS\Finder\DefaultFinder::create();
$finder
    ->in(__DIR__)
    ->name('*.php')
    ->name('markdown-extended')
    ->name('mde-dev')
;

$config = Symfony\CS\Config\Config::create();
$config
    ->setUsingCache(false)
    ->fixers([
        '@PHP74Migration' => true,
        '@PSR12' => true,
    ])
    ->finder($finder)
;

return $config;
