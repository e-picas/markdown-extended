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
 * Process Markdown mathematics
 *
 * taken from <http://github.com/drdrang/php-markdown-extra-math>
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class Maths
    extends Filter
{

    /**
     * Wrap text between \[ and \] in display math tags.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        return preg_replace_callback('{
              ^\\\\                         # line starts with a single backslash (double escaping)
              \[                            # followed by a square bracket
              (.+)                          # then the actual LaTeX code
              \\\\                          # followed by another backslash
              \]                            # and closing bracket
              \s*$                          # and maybe some whitespace before the end of the line
            }mx',
            array($this, '_callback'), $text);
    }

    /**
     * Build each maths block
     *
     * @param   array   $matches    A set of results of the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $texblock   = $matches[1];
        $texblock   = trim($texblock);
        $math_type  = MarkdownExtended::getConfig('math_type');
        if ($math_type == "mathjax") {
            $texblock = MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('span', '['.$texblock.']', array(
                        'class'=>"MathJax_Preview",
                    ))
                .MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('script', $texblock, array(
                        'type'=>"math/tex; mode=display",
                    ))
                ;
        } else {
            $texblock = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('div', $texblock, array(
                    'class'=>"math",
                ))
            ;
        }
        return "\n\n".parent::hashBlock($texblock)."\n\n";
    }

    /**
     * Build each maths span
     *
     * @param   string   $texblock
     * @return  string
     */
    public function span($texblock)
    {
        $texblock   = trim($texblock);
        $math_type  = MarkdownExtended::getConfig('math_type');
        if ($math_type == "mathjax") {
            $texblock = MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('span', '['.$texblock.']', array(
                        'class'=>"MathJax_Preview",
                    ))
                .MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('script', $texblock, array(
                        'type'=>"math/tex",
                    ))
            ;
        } else {
            $texblock = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('span', $texblock, array(
                    'class'=>"math",
                ))
            ;
        }
        return parent::hashPart($texblock);
    }

}

// Endfile
