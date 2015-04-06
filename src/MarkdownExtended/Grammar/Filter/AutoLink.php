<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use \MarkdownExtended\Grammar\Filter;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\Lexer;

/**
 * Process Markdown automatic links
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class AutoLink
    extends Filter
{

    /**
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        // Email addresses: <address@domain.foo>
        $text = preg_replace_callback('{
            <
            (?:mailto:)?
            (
                (?:
                    [-!#$%&\'*+/=?^_`.{|}~\w\x80-\xFF]+
                |
                    ".*?"
                )
                \@
                (?:
                    [-a-z0-9\x80-\xFF]+(\.[-a-z0-9\x80-\xFF]+)*\.[a-z]+
                |
                    \[[\d.a-fA-F:]+\]   # IPv4 & IPv6
                )
            )
            >
            }xi',
            array($this, '_email_callback'), $text);

        $text = preg_replace_callback('{<([^\'">\s]+)>}i',
            array($this, '_url_callback'), $text);

        return $text;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _url_callback($matches)
    {
        $url = Lexer::runGamut('tools:EncodeAttribute', $matches[1]);
        Kernel::addConfig('urls', $url);

        $block = Kernel::get('OutputFormatBag')
            ->buildTag('link', $url, array(
                'href'  => $url
            ));

        return parent::hashPart($block);
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _email_callback($matches)
    {
        $address = $matches[1];
        Kernel::addConfig('urls', $address);
        $block = Kernel::get('OutputFormatBag')
            ->buildTag('link', $address, array(
                'email' => $address
            ));
        return parent::hashPart($block);
    }

}
