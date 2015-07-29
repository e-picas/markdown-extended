<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Exception;

use \RuntimeException as BaseException;

/**
 * Specific RuntimeException with a default status code of 93
 */
class RuntimeException
    extends BaseException
{
    public function __construct($message = '', $code = 0, BaseException $previous = null)
    {
        parent::__construct($message, ($code===0 ? 93 : $code), $previous);
    }
}
