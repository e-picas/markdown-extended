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

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Helper as MDE_Helper;
use MarkdownExtended\Exception as MDE_Exception;

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
        $text = preg_replace_callback('{<([^\'">\s]+)>}i',
            array($this, '_url_callback'), $text);

        // Email addresses: <address@domain.foo>
        return preg_replace_callback('{
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
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _url_callback($matches)
    {
        $url = parent::runGamut('tool:EncodeAttribute', $matches[1]);
        MarkdownExtended::getContent()->addUrl($url);
        $block = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('link', $url, array(
                'href' => $url,
                'title' => MDE_Helper::fillPlaceholders(
                    MarkdownExtended::getConfig('link_mask_title'), $url)
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
        list($address_link, $address_text) = MDE_Helper::encodeEmailAddress($address);
        MarkdownExtended::getContent()->addUrl($address_text);
        $block = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('link', $address_text, array(
                'email' => $address,
                'href' => $address_link,
                'title' => MDE_Helper::fillPlaceholders(
                    MarkdownExtended::getConfig('mailto_mask_title'), $address_text)
            ));
        return parent::hashPart($block);
    }

}

// Endfile