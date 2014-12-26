<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Helper as MDE_Helper;
use MarkdownExtended\Exception as MDE_Exception;

/**
 * Process Markdown code blocks
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class CodeBlock
    extends Filter
{

    /**
     *  Process Markdown `<pre><code>` blocks.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback('{
                (?:\n\n|\A\n?)
                (                                                     # $1 = the code block -- one or more lines, starting with a space/tab
                  (?>
                    [ ]{'.MarkdownExtended::getConfig('tab_width').'} # Lines must start with a tab or a tab-width of spaces
                    .*\n+
                  )+
                )
                ((?=^[ ]{0,'.MarkdownExtended::getConfig('tab_width').'}\S)|\Z) # Lookahead for non-space at line-start, or end of doc
            }xm',
            array($this, '_callback'), $text);
    }

    /**
     * Build `<pre><code>` blocks.
     *
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $codeblock = parent::runGamut('tool:Outdent', $matches[1]);
        $codeblock = MDE_Helper::escapeCodeContent($codeblock);
        # trim leading newlines and trailing newlines
        $codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);
        $codeblock = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('preformated', $codeblock);
        return "\n\n".parent::hashBlock($codeblock)."\n\n";
    }

    /**
     * Create a code span markup for $code. Called from handleSpanToken.
     *
     * @param   string  $code
     * @return  string
     */
    public function span($code)
    {
        $codeblock = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('code', MDE_Helper::escapeCodeContent(trim($code)));
        return parent::hashPart($codeblock);
    }

}

// Endfile
