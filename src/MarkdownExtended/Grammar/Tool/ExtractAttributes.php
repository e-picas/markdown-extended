<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace MarkdownExtended\Grammar\Tool;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Tool;

/**
 * Class ExtractAttributes
 * @package MarkdownExtended\Grammar\Tool
 */
class ExtractAttributes
    extends Tool
{

    /**
     * Extract attributes from string 'a="b"'
     *
     * @param   string  $attributes The attributes to parse
     * @return  string              The attributes processed
     */
    public function run($attributes)
    {
        $this->img_attrs = array();
        $text = preg_replace_callback('{
            (\S+)=
            (["\']?)                  # $2: simple or double quote or nothing
            (?:
                ([^"|\']\S+|.*?[^"|\']) # anything but quotes
            )
            \\2                       # rematch $2
            }xsi', array($this, '_callback'), $attributes);
        return $this->img_attrs;
    }

    /**
     * @param   array   $matches
     */
    protected function _callback($matches)
    {
        $this->img_attrs[$matches[1]] = $matches[3];
    }

}

// Endfile