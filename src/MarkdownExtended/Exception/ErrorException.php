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

use \ErrorException as BaseException;

/**
 * Specific ErrorException with a default status code of 95
 */
class ErrorException
    extends BaseException
{

    public function __construct($message = '', $code = 0, $severity = 1, $filename = __FILE__, $lineno = __LINE__, BaseException $previous = null)
    {
        parent::__construct($message, ($code===0 ? 95 : $code), $severity, $filename, $lineno, $previous);
    }

}
