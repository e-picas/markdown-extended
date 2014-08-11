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
 * Process Markdown fenced code blocks
 *
 * Fenced code blocks may be written like:
 *
 *      ~~~~(language)
 *      my content ...
 *      ~~~~
 */
class FencedCodeBlock
    extends Filter
{

    /**
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback('{
                (?:\n|\A)               # 1: Opening marker
                (
                    ~{3,}|`{3,}         # Marker: three tildes or backticks or more.
                )
                (\w+)?                  # 2: Language
                [ ]* \n                 # Whitespace and newline following marker.
                (                       # 3: Content
                    (?>
                        (?!\1 [ ]* \n)  # Not a closing marker.
                        .*\n+
                    )+
                )
                \1 [ ]* \n              # Closing marker
            }xm',
            array($this, '_callback'), $text);
    }

    /**
     * Process the fenced code blocks
     *
     * @param   array   $matches    Results form the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $language  = $matches[2];
        $codeblock = MDE_Helper::escapeCodeContent($matches[3]);
        $codeblock = preg_replace_callback('/^\n+/', array($this, '_newlines'), $codeblock);

        $attributes = array();
        if (!empty($language)) {
            $attribute = MarkdownExtended::getConfig('fcb_language_attribute');
            $attributes[$attribute] = MDE_Helper::fillPlaceholders(
                MarkdownExtended::getConfig('fcb_attribute_value_mask'), $language);
        }
        $codeblock = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('preformated', $codeblock, $attributes);
        return "\n\n" . parent::hashBlock($codeblock) . "\n\n";
    }

    /**
     * Process the fenced code blocks new lines
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _newlines($matches)
    {
        return str_repeat(MarkdownExtended::get('OutputFormatBag')->buildTag('new_line'), strlen($matches[0]));
    }


}

// Endfile