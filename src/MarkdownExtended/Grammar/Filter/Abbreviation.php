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
 * Process Markdown abbreviations
 *
 * Process abbreviations written like:
 *
 *      *[abbr]: definition
 *
 * You can pre-define a set of abbreviation descriptions in the config entry `predef_abbr`.
 * This must be define as an array of `term => description` values.
 *
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
        $predef_abbr = MarkdownExtended::getVar('predef_abbr');
        if (!empty($predef_abbr)) {
            foreach ($predef_abbr as $abbr_word => $abbr_desc) {
                if ($abbr_word_re) $abbr_word_re .= '|';
                $abbr_word_re .= preg_quote($abbr_word);
                $abbr_desciptions[$abbr_word] = trim($abbr_desc);
            }
        }
        MarkdownExtended::setVar('abbr_word_re', $abbr_word_re);
        MarkdownExtended::setVar('abbr_desciptions', $abbr_desciptions);
    }

    /**
     * Reset masks created by the `_setup()` method
     */
    public function _teardown()
    {
        MarkdownExtended::setVar('abbr_desciptions', array());
        MarkdownExtended::setVar('abbr_word_re', '');
    }

    /**
     * Find defined abbreviations in text and wrap them in <abbr> elements
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        if (MarkdownExtended::getConfig('abbr_word_re')) {
            // cannot use the /x modifier because abbr_word_re may
            // contain significant spaces:
            $text = preg_replace_callback('{'.
                '(?<![\w\x1A])'.
                '(?:'.MarkdownExtended::getConfig('abbr_word_re').')'.
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
        $abbr_desciptions = MarkdownExtended::getConfig('abbr_desciptions');
        if (isset($abbr_desciptions[$abbr])) {
            $attributes = array();
            $desc = trim($abbr_desciptions[$abbr]);
            if (!empty($desc)) {
                $attributes['title'] = parent::runGamut('tool:EncodeAttribute', $desc);
            }
            $abbr = MarkdownExtended::get('OutputFormatBag')
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
                MarkdownExtended::getConfig('less_than_tab')
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
        MarkdownExtended::addConfig('abbr_word_re',
            (MarkdownExtended::getConfig('abbr_word_re') ? '|' : '' ).preg_quote($matches[1])
        );
        MarkdownExtended::addConfig('abbr_desciptions', array($matches[1] => trim($matches[2])));
        return '';
    }

}

// Endfile