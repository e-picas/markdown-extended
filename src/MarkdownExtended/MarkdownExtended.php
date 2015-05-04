<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended;

/**
 * PHP Markdown Extended
 *
 * LICENSE
 *
 * Mardown
 * Copyright © 2004-2006, John Gruber
 * http://daringfireball.net/
 * All rights reserved.
 *
 * MultiMarkdown
 * Copyright © 2005-2009 Fletcher T. Penney
 * http://fletcherpenney.net/
 * All rights reserved.
 *
 * PHP Markdown & Extra
 * Copyright © 2004-2012 Michel Fortin
 * http://michelf.com/projects/php-markdown/
 * All rights reserved.
 *
 * Markdown Extended
 * Copyright © 2008-2013 Pierre Cassat & contributors
 * http://e-piwi.fr/
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification, are permitted
 * provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice, this list of conditions
 *   and the following disclaimer.
 *
 * - Redistributions in binary form must reproduce the above copyright notice, this list of conditions
 *   and the following disclaimer in the documentation and/or other materials provided with the distribution.
 *
 * - Neither the names “Markdown”, "Markdown Extra", "Multi Markdown", "Markdown Extended" nor the names of
 *   their contributors may be used to endorse or promote products derived from this software without specific
 *   prior written permission.
 *
 * This software is provided by the copyright holders and contributors “as is” and any express or
 * implied warranties, including, but not limited to, the implied warranties of merchantability and
 * fitness for a particular purpose are disclaimed. In no event shall the copyright owner or contributors
 * be liable for any direct, indirect, incidental, special, exemplary, or consequential damages
 * (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits;
 * or business interruption) however caused and on any theory of liability, whether in contract,
 * strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
 * this software, even if advised of the possibility of such damage.
 *
 */
class MarkdownExtended
    extends Parser
{

    const SHORTNAME = 'markdown-extended-php';
    const NAME      = 'Markdown Extended';
    const VERSION   = '0.1.0-dev';
    const DATE      = '2015-04-16';
    const DESC      = 'Yet another PHP parser for the markdown (*extended*) syntax.';
    const LINK      = 'http://github.com/piwi/markdown-extended.git';
    const LICENSE   = 'BSD-3-Clause open source license <http://opensource.org/licenses/BSD-3-Clause>';
    const SOURCES   = 'Sources & updates: <http://github.com/piwi/markdown-extended.git>';
    const COPYRIGHT = 'Copyright (c) 2004-2006 John Gruber, 2005-2009 Fletcher T. Penney, 2004-2012 Michel Fortin, 2008-2013 Pierre Cassat & contributors.';

    /**
     * Gets app's information
     *
     * @param bool $shorten
     * @return array|string
     */
    public static function getAppInfo($shorten = false)
    {
        if ($shorten) {
            return self::SHORTNAME . '@' . self::VERSION;
        }
        return array(
            self::NAME . ' - ' . self::SHORTNAME . '@' . self::VERSION,
            self::DESC,
            self::COPYRIGHT,
            self::LICENSE,
            self::SOURCES
        );
    }

    /**
     * App's default settings
     *
     * @return array
     */
    public static function getDefaults()
    {
        return array(
            // ------------------
            // Global parsing options
            // ------------------

            // Table of hash values for escaped characters:
            'escaped_characters'        => "0`*_{}[]()<>#+-.!:|\\",
            // Define the width of a tab (4 spaces by default)
            'tab_width'                 => 4,
            // Regex to match balanced brackets.
            // Needed to insert a maximum bracked depth while converting to PHP.
            'nested_brackets_depth'     => 6,
            // Regex to match balanced parenthesis.
            // Needed to insert a maximum bracked depth while converting to PHP.
            'nested_parenthesis_depth'  => 4,
            // Change to `true` to disallow markup or entities.
            'no_markup'                 => false,
            'no_entities'               => false,
            // Special metadata used during parsing
            'special_metadata'          => array(
                'baseheaderlevel',
                'quoteslanguage',
                'last_update',
                'file_name'
            ),
            // Block inclusion tag
            'block_inclusion_mask'      => '<!-- @([^ @]+)@ -->',
            // Define an array of base path for block inclusions ; defaults to cwd.
            'base_path'                 => array(getcwd()),
            // Optional id attribute prefix for footnote links and backlinks.
            'footnote_id_prefix'        => '',
            // Optional id attribute prefix for glossary footnote links and backlinks.
            'glossarynote_id_prefix'    => '',
            // Optional id attribute prefix for citation footnote links and backlinks.
            'bibliographynote_id_prefix'=> '',

            // ------------------
            // Callback options
            // ------------------

            // transform a DateTime object to string
            'date_to_string'            => function (\DateTime $date) {
                return $date->format(DATE_W3C);
            },
            // get a content's title from concerned file path
            'filepath_to_title'         => function ($path) {
                return \MarkdownExtended\Util\Helper::humanReadable(pathinfo($path,  PATHINFO_FILENAME));
            },

            // ------------------
            // Output format options
            // ------------------

            // the default output format
            'output_format'             => 'html',

            'output_format_options'     => array(

                'html'                  => array(
                    // Change to ">" for HTML output
                    'html_empty_element_suffix' => ' />',

                    // Optional title attribute for inpage anchors links that do not have one
                    'anchor_title_mask'         => 'Reach inpage section %%',
                    // Optional title attribute for links that do not have one
                    'link_title_mask'           => 'See online %%',
                    // Optional title attribute for mailto links that do not have one
                    'mailto_title_mask'         => 'Contact %%',

                    // Optional attribute to define for fenced code blocks with language type
                    'codeblock_language_attribute' => 'class',
                    // Attribute's value construction for fenced code blocks with language type
                    'codeblock_attribute_mask'  => 'language-%%',

                    // Optional title attribute for footnote links and backlinks.
                    'fn_link_title_mask'        => 'See footnote %%',
                    'fn_backlink_title_mask'    => 'Return to content',

                    // Optional class attribute for footnote links and backlinks.
                    'fn_link_class'             => 'footnote',
                    'fn_backlink_class'         => 'reverse_footnote',

                    // Optional title attribute for glossary footnote links and backlinks.
                    'fng_link_title_mask'       => 'See glossary entry %%',
                    'fng_backlink_title_mask'   => 'Return to content',

                    // Optional class attribute for glossary footnote links and backlinks.
                    'fng_link_class'            => 'footnote_glossary',
                    'fng_backlink_class'        => 'reverse_footnote_glossary',

                    // Optional title attribute for bibliography footnote links and backlinks.
                    'fnb_link_title_mask'       => 'See bibliography reference %%',
                    'fnb_backlink_title_mask'   => 'Return to content',

                    // Optional class attribute for bibliography footnote links and backlinks.
                    'fnb_link_class'            => 'footnote_bibliography',
                    'fnb_backlink_class'        => 'reverse_footnote_bibliography',

                    // select math type to apply in '' or mathjax
                    'math_type'                 => 'mathjax',

                    // the default template to use if needed
                    'default_template'          => 'html5',
                ),

            ),

            // ------------------
            // Initial entries options
            // ------------------

            // Predefined urls, titles and abbreviations for reference links and images.
            'predefined_urls'               => array(),
            'predefined_titles'             => array(),
            'predefined_attributes'         => array(),
            'predefined_abbr'               => array(),

            // ------------------
            // Templating options
            // ------------------

            // load the result in a template?
            // - bool to force default template or note
            // - 'auto' to let the parser choose the best option
            // - 'file path' to a specific template file
            // - 'class name' to a custom `TemplateInterface` object
            'template'                  => 'auto',
            'template_options'          => array(
                // Template mask for keywords regexp
                // i.e. "{% TOC %}" or "{%TOC%}"
                // "%%" will be a literal "%"
                'keywords_mask'         => "{%% ?%s ?%%}",
                'keywords'              => array(
                    'body'                  => 'BODY',
                    'notes'                 => 'NOTES',
                    'metadata'              => 'META',
                    'charset'               => 'CHARSET',
                    'title'                 => 'TITLE',
                    'menu'                  => 'TOC',
                ),
                'inline_template'       => "{% META %}\n{% BODY %}\n{% NOTES %}",
            ),

            // ------------------
            // Filters gamuts options
            // ------------------

            // full gamuts stacks
            // each sub-item is constructed like "gamut_alias or class name : method or class name : method name"
            'initial_gamut' => array(
                'filter:Detab:init'          => '5',
                'filter:Emphasis:prepare'    => '10',
            ),
            'transform_gamut' => array(
                'tools:RemoveUtf8Marker'     => '5',
                'tools:StandardizeLineEnding'=> '10',
                'tools:AppendEndingNewLines' => '15',
                'filter:Detab'               => '20',
                'filter:HTML'                => '25',
                'tools:StripSpacedLines'     => '30',
            ),
            'document_gamut' => array(
                'tools:prepareOutputFormat' => '0',
                'filter:MetaData:strip'     => '1',
                'filter:FencedCodeBlock'    => '5',
                'filter:Note:strip'         => '10',
                'filter:LinkDefinition:strip' => '20',
                'filter:Abbreviation:strip' => '25',
                'block_gamut'               => '30',
                'filter:MetaData:append'    => '35',
                'filter:Note:append'        => '40',
                'filter:BlockInclusion'     => '50',
                'tools:teardownOutputFormat' => '70',
            ),
            'span_gamut' => array(
                'filter:Span'               => '-30',
                'filter:Note'               => '5',
                'filter:Image'              => '10',
                'filter:Anchor'             => '20',
                'filter:AutoLink'           => '30',
                'tools:EncodeAmpAndAngle'   => '40',
                'filter:Emphasis'           => '50',
                'filter:HardBreak'          => '60',
                'filter:Abbreviation'       => '70',
            ),
            'block_gamut' => array(
                'filter:FencedCodeBlock'    => '5',
                'filter:Header'             => '10',
                'filter:Table'              => '15',
                'filter:HorizontalRule'     => '20',
                'filter:ListItem'           => '40',
                'filter:DefinitionList'     => '45',
                'filter:CodeBlock'          => '50',
                'filter:BlockQuote'         => '60',
                'filter:Maths'              => '70',
                'tools:RebuildParagraph'    => '100',
            ),
            'html_block_gamut' => array(
                'filter:HTML'               => '10',
                'block_gamut'               => '20',
            ),
        );
    }

    /**
     * Parse a markdown content or file
     *
     * @param   string  $content    A raw content or a file path to parse
     * @param   null    $options    A set of options to override defaults
     *
     * @return  \MarkdownExtended\Content
     */
    public static function parse($content, $options = null)
    {
        $mde = new self($options);
        return (false === strpos($content, PHP_EOL) && file_exists($content)) ?
            $mde->transformSource($content) : $mde->transform($content);
    }

    /**
     * Parse a markdown content
     *
     * @param   string  $content    A raw content to parse
     * @param   null    $options    A set of options to override defaults
     *
     * @return  \MarkdownExtended\Content
     */
    public static function parseString($content, $options = null)
    {
        $mde = new self($options);
        return $mde->transform($content);
    }

    /**
     * Parse a markdown file
     *
     * @param   string  $path       A file path to parse
     * @param   null    $options    A set of options to override defaults
     *
     * @return  \MarkdownExtended\Content
     */
    public static function parseSource($path, $options = null)
    {
        $mde = new self($options);
        return $mde->transformSource($path);
    }
}
