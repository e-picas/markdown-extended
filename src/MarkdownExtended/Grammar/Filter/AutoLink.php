<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Helper as MDE_Helper;
use MarkdownExtended\Exception as MDE_Exception;

/**
 * Process Markdown automatic links
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
        $text = preg_replace_callback('{<((https?|ftp|dict):[^\'">\s]+)>}i',
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