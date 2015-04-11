<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

/**
 * Interface to implement for filter objects
 *
 * @api
 */
interface GamutInterface
{

    /**
     * Must return a method name
     *
     * @return  string
     */
    public static function getDefaultMethod();
}
