<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MarkdownExtended\Grammar;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\Grammar\AbstractGamut;
use \MarkdownExtended\API\GamutInterface;

/**
 * Abstract base class for Tools
 * @package MarkdownExtended\Grammar
 */
abstract class Tool
    extends AbstractGamut
    implements GamutInterface
{

    /**
     * Must return a method name
     *
     * @return string
     */
    public static function getDefaultMethod()
    {
        return 'run';
    }

    /**
     * Must process the tool on a text
     *
     * @param   string
     * @return  string
     */
    abstract public function run($text);

}

// Endfile