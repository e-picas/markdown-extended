<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar;

use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Exception\UnexpectedValueException;
use \MarkdownExtended\Content;

class Lexer
{

    public function __construct()
    {
        // init config
        Kernel::setConfig('nested_brackets_re',
            str_repeat('(?>[^\[\]]+|\[', Kernel::getConfig('nested_brackets_depth')).
            str_repeat('\])*', Kernel::getConfig('nested_brackets_depth'))
        );
        Kernel::setConfig('nested_parenthesis_re',
            str_repeat('(?>[^()\s]+|\(', Kernel::getConfig('nested_parenthesis_depth')).
            str_repeat('(?>\)))*', Kernel::getConfig('nested_parenthesis_depth'))
        );
        Kernel::setConfig('escaped_characters_re',
            '['.preg_quote(Kernel::getConfig('escaped_characters')).']'
        );
        Kernel::setConfig('less_than_tab',
            (Kernel::getConfig('tab_width') - 1)
        );

        // run initial gamuts stack
        $this->runGamuts('initial_gamut');
    }

// ----------------------------------
// PARSER
// ----------------------------------
    
    /**
     * Main function. Performs some pre-processing on the input text
     * and pass it through the document gamut.
     *
     * @param   \MarkdownExtended\Content   $content
     * @return  \MarkdownExtended\MarkdownExtended
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
     * Call to MarkdownExtended\Grammar\GamutLoader for an array of gamuts
     *
     * @param   array   $gamuts
     * @param   string  $text
     * @return  string
     * @throws  \MarkdownExtended\Exception\UnexpectedValueException if gamuts table not found
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

            return Kernel::get('Grammar\GamutLoader')
                ->runGamuts($gamuts, $text);
        }
        return $text;
    }

    /**
     * Setting up extra-specific variables.
     */
    protected function _setup() 
    {
        // clear global hashes
        $this->_clearHashes();

        // call all gamuts '_setup'
        $loader = Kernel::get('Grammar\GamutLoader');
        $loader->runGamutsMethod($loader->getAllGamutsReversed(), '_setup');
    }
    
    /**
     * Clearing extra-specific variables.
     */
    protected function _teardown() 
    {
        // clear global hashes
        $this->_clearHashes();

        // call all gamuts '_teardown'
        $loader = Kernel::get('Grammar\GamutLoader');
        $loader->runGamutsMethod($loader->getAllGamutsReversed(), '_teardown');
    }

    // clear global hashes
    private function _clearHashes()
    {
        Kernel::setConfig('html_hashes',        array());
        Kernel::setConfig('cross_references',   array());
        Kernel::setConfig('urls',               Kernel::getConfig('predefined_urls', array()));
        Kernel::setConfig('titles',             Kernel::getConfig('predefined_titles', array()));
        Kernel::setConfig('attributes',         Kernel::getConfig('predefined_attributes', array()));
        Kernel::setConfig('predefined_abbr',    Kernel::getConfig('predefined_abbr', array()));
    }

}

// Endfile
