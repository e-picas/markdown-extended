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
 * Interface to implement for `MarkdownExtended\Content` objects
 *
 * @package MarkdownExtended\API
 */
interface ContentInterface
{

    /**
     * @return  string  Must return the source string
     */
    public function getSource();

}

// Endfile
