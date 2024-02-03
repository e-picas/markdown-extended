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
use MarkdownExtended\Grammar\Lexer;
use MarkdownExtended\Util\Helper;
use MarkdownExtended\API\Kernel;

/**
 * Process Markdown headers
 *
 * Setext-style headers:
 *
 *    Header 1  {#header1}
 *    ========
 *
 *    Header 2  {#header2}
 *    --------
 *
 * ATX-style headers:
 *
 *  # Header 1        {#header1}
 *  ## Header 2       {#header2}
 *  ## Header 2 with closing hashes ##  {#header3}
 *  ...
 *  ###### Header 6   {#header2}
 *
 */
class Header extends Filter
{
    /**
     * Redefined to add id attribute support.
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        // Setext-style headers:
        $text = preg_replace_callback(
            '{
                (^.+?)                                  # $1: Header text
                (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})?        # $2: Id attribute
                [ ]*\n(=+|-+)[ ]*\n+                    # $3: Header footer
            }mx',
            [$this, '_setext_callback'],
            $text
        );

        // atx-style headers:
        $text = preg_replace_callback(
            '{
                ^(\#{1,6})                              # $1 = string of #\'s
                [ ]*
                (.+?)                                   # $2 = Header text
                [ ]*
                \#*                                     # optional closing #\'s (not counted)
                (?:[ ]+\{\#([-_:a-zA-Z0-9]+)\})?        # id attribute
                [ ]*
                \n+
            }xm',
            [$this, '_atx_callback'],
            $text
        );

        return $text;
    }

    /**
     * Process setext-style headers
     *
     * @param   array   $matches    The results from the `transform()` function
     * @return  string
     */
    protected function _setext_callback($matches)
    {
        if ($matches[3] == '-' && preg_match('{^- }', $matches[1])) {
            return $matches[0];
        }
        $level = ($matches[3][0] == '=' ? 1 : 2)  + $this->_getRebasedHeaderLevel();
        return "\n".str_pad('#', $level, '#').' '.$matches[1].' '
            .(!empty($matches[2]) ? '{#'.$matches[2].'}' : '')."\n";
    }

    /**
     * Process ATX-style headers
     *
     * @param   array   $matches    The results from the `transform()` function
     * @return  string
     */
    protected function _atx_callback($matches)
    {
        $level = strlen($matches[1]) + $this->_getRebasedHeaderLevel();
        $domid  = !empty($matches[3]) ?
            $matches[3]
            :
            Helper::header2Label($matches[2]);
        $domid  = Kernel::get('DomId')->set($domid);
        $title = Lexer::runGamut('span_gamut', $matches[2]);
        Kernel::addConfig('menu', ['level' => $level, 'text' => parent::unhash($title)], $domid);
        $block = Kernel::get('OutputFormatBag')
            ->buildTag('title', $title, [
                'level' => $level,
                'id' => $domid,
            ]);

        $this->_setContentTitle($title);

        return "\n" . parent::hashBlock($block) . "\n\n";
    }

    /**
     * Rebase a header level according to the `baseheaderlevel` config value
     */
    protected function _getRebasedHeaderLevel()
    {
        $base_level = Kernel::getConfig('baseheaderlevel');
        return !empty($base_level) ? $base_level - 1 : 0;
    }

    /**
     * Set the page content if it is not set yet
     */
    protected function _setContentTitle($string)
    {
        $old = Kernel::get(Kernel::TYPE_CONTENT)->getTitle();
        if (empty($old)) {
            $meta = Kernel::get(Kernel::TYPE_CONTENT)->getMetadata();
            $meta['title'] = $string;
            Kernel::get(Kernel::TYPE_CONTENT)->setMetadata($meta);
        }
    }
}
