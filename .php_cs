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

$finder = Symfony\CS\Finder\DefaultFinder::create()
    ->in(__DIR__)
    ->name('*.php')
    ->name('markdown-extended')
    ->name('mde-dev')
;

return Symfony\CS\Config\Config::create()
    ->level(Symfony\CS\FixerInterface::PSR2_LEVEL)
    ->finder($finder)
    ;
