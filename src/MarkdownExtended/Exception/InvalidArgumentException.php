<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Exception;

use InvalidArgumentException as BaseException;

/**
 * Specific InvalidArgumentException with a default status code of 90
 */
class InvalidArgumentException extends BaseException
{
    public function __construct($message = '', $code = 0, BaseException $previous = null)
    {
        parent::__construct($message, ($code === 0 ? 90 : $code), $previous);
    }
}
