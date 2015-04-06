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

interface TemplateInterface
{

    /**
     * @param   \MarkdownExtended\API\ContentInterface  $content
     *
     * @return  string
     */
    public function parse(ContentInterface $content);

}

// Endfile
