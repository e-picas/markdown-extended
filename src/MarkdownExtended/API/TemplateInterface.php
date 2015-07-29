<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\API;

/**
 * Interface to implement for all template objects
 */
interface TemplateInterface
{
    /**
     * Insert a content in a template
     *
     * @param   \MarkdownExtended\API\ContentInterface  $content
     *
     * @return  string
     */
    public function parse(ContentInterface $content);
}
