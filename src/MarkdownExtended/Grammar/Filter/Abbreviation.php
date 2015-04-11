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
 * Process Markdown abbreviations
 *
 * Process abbreviations written like:
 *
 *      *[abbr]: definition
 *
 * You can pre-define a set of abbreviation descriptions in the config entry `predefined_abbr`.
 * This must be define as an array of `term => description` values.
 *
 * @link http://aboutmde.org/#E5
 */
class Abbreviation
    extends Filter
{

    /**
     * Prepare masks for predefined abbreviations and descriptions
     */
    public function _setup()
    {
        $abbr_word_re='';
        $abbr_desciptions = array();
        $predef_abbr = Kernel::getConfig('predefined_abbr');
        if (!empty($predef_abbr)) {
            foreach ($predef_abbr as $abbr_word => $abbr_desc) {
                if ($abbr_word_re) {
                    $abbr_word_re .= '|';
                }
                $abbr_word_re .= preg_quote($abbr_word);
                $abbr_desciptions[$abbr_word] = trim($abbr_desc);
            }
        }
        Kernel::setConfig('abbr_word_re', $abbr_word_re);
        Kernel::setConfig('abbr_desciptions', $abbr_desciptions);
    }

    /**
     * Reset masks created by the `_setup()` method
     */
    public function _teardown()
    {
        Kernel::setConfig('abbr_desciptions', array());
        Kernel::setConfig('abbr_word_re', '');
    }

    /**
     * Find defined abbreviations in text and wrap them in <abbr> elements
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        if (Kernel::getConfig('abbr_word_re')) {
            // cannot use the /x modifier because abbr_word_re may
            // contain significant spaces:
            $text = preg_replace_callback('{'.
                '(?<![\w\x1A])'.
                '(?:'.Kernel::getConfig('abbr_word_re').')'.
                '(?![\w\x1A])'.
                '}',
                array($this, '_callback'), $text);
        }
        return $text;
    }

    /**
     * Process each abbreviation
     *
     * @param   array   $matches    One set of results form the `transform()` function
     * @return  string
     */
    protected function _callback($matches)
    {
        $abbr = $matches[0];
        $abbr_desciptions = Kernel::getConfig('abbr_desciptions');
        if (isset($abbr_desciptions[$abbr])) {
            $attributes = array();
            $desc = trim($abbr_desciptions[$abbr]);
            if (!empty($desc)) {
                $attributes['title'] = Lexer::runGamut('tools:EncodeAttribute', $desc);
            }
            $abbr = Kernel::get('OutputFormatBag')
                ->buildTag('abbreviation', $abbr, $attributes);
            return parent::hashBlock($abbr);
        } else {
            return $abbr;
        }
    }

    /**
     * Strips abbreviations from text, stores titles in hash references.
     *
     * @param   string  $text
     * @return  string
     */
    public function strip($text)
    {
        return preg_replace_callback('{
                ^[ ]{0,'.
                Kernel::getConfig('less_than_tab')
                .'}\*\[(.+?)\][ ]?: # abbr_id = $1
                (.*)                # text = $2 (no blank lines allowed)
            }xm',
            array($this, '_strip_callback'),
            $text);
    }

    /**
     * Strips abbreviations from text, stores titles in hash references.
     *
     * @param   array   $matches    Results from the `strip()` function
     * @return  string              The replacement text
     */
    protected function _strip_callback($matches)
    {
        Kernel::addConfig('abbr_word_re',
            (Kernel::getConfig('abbr_word_re') ? '|' : '').preg_quote($matches[1])
        );
        Kernel::addConfig('abbr_desciptions', array($matches[1] => trim($matches[2])));
        return '';
    }
}
