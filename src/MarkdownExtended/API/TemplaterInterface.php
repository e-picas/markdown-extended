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

    /**
     * Get the template file path
     *
     * @return mixed
     */
    public function getTemplate();

    /**
     * Get the template content with loaded Markdown parsed content parts inserted
     *
     * @return mixed
     */
    public function __toString();

}

// Endfile
