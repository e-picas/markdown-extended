<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\Grammar\Lexer;
use MarkdownExtended\Grammar\GamutLoader;

/**
 * Process Markdown automatic links
 */
class AutoLink extends Filter
{
    /**
     * {@inheritDoc}
     */
    public function transform($text)
    {
        // Email addresses: <address@domain.foo>
        $text = preg_replace_callback(
            '{
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
            [$this, '_email_callback'],
            $text
        );

        $text = preg_replace_callback(
            '{<([^\'">\s]+)>}i',
            [$this, '_url_callback'],
            $text
        );

        return $text;
    }

    /**
     * The callback applied for URL matches
     *
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _url_callback($matches)
    {
        $url = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $matches[1]);
        Kernel::addConfig('urls', $url);

        $block = Kernel::get('OutputFormatBag')
            ->buildTag('link', $url, [
                'href'  => $url,
            ]);

        return parent::hashPart($block);
    }

    /**
     * The callback applied for email addresses matches
     *
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _email_callback($matches)
    {
        $address = $matches[1];
        Kernel::addConfig('urls', $address);
        $block = Kernel::get('OutputFormatBag')
            ->buildTag('link', $address, [
                'email' => $address,
            ]);
        return parent::hashPart($block);
    }
}
