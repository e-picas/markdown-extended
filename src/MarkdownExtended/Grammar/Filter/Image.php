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
 * Process Markdown images
 */
class Image
    extends Filter
{

    /**
     * Turn Markdown image shortcuts into <img> tags.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        // First, handle reference-style labeled images: ![alt text][id]
        $text = preg_replace_callback('{
            (                                       # wrap whole match in $1
              !\[
                ('.MarkdownExtended::getConfig('nested_brackets_re').') # alt text = $2
              \]

              [ ]?                                  # one optional space
              (?:\n[ ]*)?                           # one optional newline followed by spaces

              \[
                (.*?)                               # id = $3
              \]

            )
            }xs',
            array($this, '_reference_callback'), $text);

        // Next, handle inline images:  ![alt text](url "optional title")
        // Don't forget: encode * and _
        $text = preg_replace_callback('{
            (                                         # wrap whole match in $1
              !\[
                ('.MarkdownExtended::getConfig('nested_brackets_re').') # alt text = $2
              \]
              \s?                                     # One optional whitespace character
              \(                                      # literal paren
                [ \n]*
                (?:
                    <(\S*)>                           # src url = $3
                |
                    ('.MarkdownExtended::getConfig('nested_url_parenthesis_re').')  # src url = $4
                )
                [ \n]*
                (                                     # $5
                  ([\'"])                             # quote char = $6
                  (.*?)                               # title = $7
                  \6                                  # matching quote
                  [ \n]*
                )?                                    # title is optional
              \)
            )
            }xs',
            array($this, '_inline_callback'), $text);

        return $text;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _reference_callback($matches)
    {
        $whole_match = $matches[1];
        $alt_text    = $matches[2];
        $link_id     = strtolower($matches[3]);

        if ($link_id == "") {
            $link_id = strtolower($alt_text); // for shortcut links like ![this][].
        }

        $urls = MarkdownExtended::getVar('urls');
        $titles = MarkdownExtended::getVar('titles');
        $predef_attributes = MarkdownExtended::getVar('attributes');
        $alt_text = parent::runGamut('tool:EncodeAttribute', $alt_text);
        if (isset($urls[$link_id])) {
            $attributes = array();
            $attributes['alt'] = $alt_text;
            $attributes['src'] = parent::runGamut('tool:EncodeAttribute', $urls[$link_id]);
            if (!empty($titles[$link_id])) {
                $attributes['title'] = parent::runGamut('tool:EncodeAttribute', $titles[$link_id]);
            }
            if (!empty($predef_attributes[$link_id])) {
                $attributes = array_merge(
                    parent::runGamut('tool:ExtractAttributes', $predef_attributes[$link_id]),
                    $attributes);
            }
            $block = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('image', null, $attributes);
            $result = parent::hashPart($block);
        } else {
            // If there's no such link ID, leave intact
            $result = $whole_match;
        }

        return $result;
    }

    /**
     * @param   array   $matches    A set of results of the `transform` function
     * @return  string
     */
    protected function _inline_callback($matches)
    {
        $whole_match    = $matches[1];
        $alt_text       = $matches[2];
        $url            = $matches[3] == '' ? $matches[4] : $matches[3];
        $title          =& $matches[7];

        $attributes = array();
        $attributes['alt'] = parent::runGamut('tool:EncodeAttribute', $alt_text);
        $attributes['src'] = parent::runGamut('tool:EncodeAttribute', $url);
        if (!empty($title)) {
            $attributes['title'] = parent::runGamut('tool:EncodeAttribute', $title);
        }
        $block = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('image', null, $attributes);
        return parent::hashPart($block);
    }

}

// Endfile