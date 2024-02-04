<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
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
