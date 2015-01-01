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
 * Interface TemplaterInterface
 *
 * @package MarkdownExtended\API
 */
interface TemplaterInterface
{

    public function getTemplate();
    public function __toString();

}

// Endfile
