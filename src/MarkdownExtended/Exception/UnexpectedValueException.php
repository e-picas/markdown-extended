<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Exception;

use \UnexpectedValueException as BaseException;

/**
 * Specific UnexpectedValueException with a default status code of 92
 */
class UnexpectedValueException
    extends BaseException
{

    public function __construct($message = '', $code = 0, BaseException $previous = null)
    {
        parent::__construct($message, ($code===0 ? 92 : $code), $previous);
    }

}
