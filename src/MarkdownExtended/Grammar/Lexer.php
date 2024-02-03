<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar;

use MarkdownExtended\API\Kernel;
use MarkdownExtended\Exception\UnexpectedValueException;
use MarkdownExtended\Content;

/**
 * The lexer dispatches the content to the gamuts loader
 */
class Lexer
{
    /**
     * Sets up some regex masks based on current configuration
     */
    public function __construct()
    {
        // init config
        Kernel::setConfig(
            'nested_brackets_re',
            str_repeat('(?>[^\[\]]+|\[', Kernel::getConfig('nested_brackets_depth')).
            str_repeat('\])*', Kernel::getConfig('nested_brackets_depth'))
        );
        Kernel::setConfig(
            'nested_parenthesis_re',
            str_repeat('(?>[^()\s]+|\(', Kernel::getConfig('nested_parenthesis_depth')).
            str_repeat('(?>\)))*', Kernel::getConfig('nested_parenthesis_depth'))
        );
        Kernel::setConfig(
            'escaped_characters_re',
            '['.preg_quote(Kernel::getConfig('escaped_characters')).']'
        );
        Kernel::setConfig(
            'less_than_tab',
            (Kernel::getConfig('tab_width') - 1)
        );

        // run initial gamuts stack
        $this->runGamuts('initial_gamut');
    }

    // ----------------------------------
    // PARSER
    // ----------------------------------

    /**
     * Performs some pre-processing on the input text
     * and pass it through the document gamut.
     *
     * @param   \MarkdownExtended\Content   $content
     *
     * @return  \MarkdownExtended\Content
     */
    public function parse(Content $content)
    {
        $this->_setup();
        $text = $content->getSource();

        // first run transform gamut methods
        $text = $this->runGamuts('transform_gamut', $text);

        // then run document gamut methods
        $text = $this->runGamuts('document_gamut', $text);

        $content->setBody($text . "\n");
        $this->_teardown();

        return $content;
    }

    // ----------------------------------
    // GAMUTS
    // ----------------------------------

    /**
     * Run a gamut stack from a filter or tool
     *
     * @param   string  $gamut  The name of a single Gamut or a Gamuts stack
     * @param   string  $text
     * @param   bool    $forced Forces to run the gamut event if it is disabled
     *
     * @return  string
     */
    public static function runGamut($gamut, $text, $forced = false)
    {
        $loader = Kernel::get('GamutLoader');
        return ($loader->isGamutEnabled($gamut) || $forced ? $loader->runGamut($gamut, $text) : $text);
    }

    /**
     * Call to MarkdownExtended\Grammar\GamutLoader for an array of gamuts
     *
     * @param   array   $gamuts
     * @param   string  $text
     *
     * @return  string
     *
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if `$gamuts` seems malformed or can not be found
     */
    public function runGamuts($gamuts, $text = null)
    {
        if (!empty($gamuts)) {
            if (!is_string($gamuts) && !is_array($gamuts)) {
                throw new UnexpectedValueException(
                    sprintf('A gamut table must be a string or an array (got "%s")', gettype($gamuts))
                );
            }

            if (is_string($gamuts)) {
                $gamuts = Kernel::getConfig($gamuts);
                if (empty($gamuts) || !is_array($gamuts)) {
                    throw new UnexpectedValueException(
                        sprintf('Called gamut table "%s" can\'t be found', $gamuts)
                    );
                }
            }

            return Kernel::get('GamutLoader')
                ->runGamuts($gamuts, $text);
        }
        return $text;
    }

    /**
     * Setting up extra-specific variables
     *
     * This will call any `_setup()` method of all enabled filters.
     */
    protected function _setup()
    {
        // clear global hashes
        $this->_clearHashes();

        // call all gamuts '_setup'
        $loader = Kernel::get('GamutLoader');
        $loader->runGamutsMethod($loader->getAllGamutsReversed(), '_setup');
    }

    /**
     * Clearing extra-specific variables
     *
     * This will call any `_teardown()` method of all enabled filters.
     */
    protected function _teardown()
    {
        // clear global hashes
        $this->_clearHashes();

        // call all gamuts '_teardown'
        $loader = Kernel::get('GamutLoader');
        $loader->runGamutsMethod($loader->getAllGamutsReversed(), '_teardown');
    }

    // clear global hashes
    private function _clearHashes()
    {
        Kernel::setConfig('html_hashes', []);
        Kernel::setConfig('cross_references', []);
        Kernel::setConfig('urls', Kernel::getConfig('predefined_urls', []));
        Kernel::setConfig('titles', Kernel::getConfig('predefined_titles', []));
        Kernel::setConfig('attributes', Kernel::getConfig('predefined_attributes', []));
        Kernel::setConfig('predefined_abbr', Kernel::getConfig('predefined_abbr', []));
    }
}
