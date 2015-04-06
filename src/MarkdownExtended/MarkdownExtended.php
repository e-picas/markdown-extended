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

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\Lexer;
use \MarkdownExtended\Grammar\GamutLoader;
use \MarkdownExtended\Exception\DomainException;
use \MarkdownExtended\Exception\InvalidArgumentException;
use \MarkdownExtended\Exception\UnexpectedValueException;
use MarkdownExtended\Util\ContentCollection;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Util\DomIdRegistry;
use \MarkdownExtended\Util\Registry;
use \DateTime;

/**
 * PHP Markdown Extended
 *
 * This is the global *MarkdownExtended* class and process. It contains mostly
 * static methods that can be called from anywhere writing something like:
 *
 *     MarkdownExtended::my_method();
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
 * @package MarkdownExtended
 */
class MarkdownExtended
{

    const SHORTNAME = 'markdown-extended-php';
    const NAME      = 'Markdown Extended';
    const VERSION   = '0.0.0';
    const DATE      = '2015-04-01';
    const DESC      = 'Yet another PHP parser for the markdown (*extended*) syntax.';
    const LINK      = 'http://github.com/piwi/markdown-extended.git';
    const LICENSE   = 'BSD-3-Clause open source license <http://opensource.org/licenses/BSD-3-Clause>';
    const SOURCES   = 'Sources & updates: <http://github.com/piwi/markdown-extended.git>';
    const COPYRIGHT = 'Copyright (c) 2004-2006 John Gruber, 2005-2009 Fletcher T. Penney, 2004-2012 Michel Fortin, 2008-2013 Pierre Cassat & contributors.';

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
            'special_metadata'          => array('baseheaderlevel', 'quoteslanguage'),
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
            // - 'class name' to a custom `` object
            'template'                  => 'auto',
            'template_options'          => array(
                // Template mask for keywords regexp
                // i.e. "{% TOC %}" or "{%TOC%}" ("%%" will be a literal "%")
                'keywords_mask'         => "{%% ?%s ?%%}",
                'keywords'              => array(
                    'body'                  => 'BODY',
                    'notes'                 => 'NOTES',
                    'metadata'              => 'META',
                    'charset'               => 'CHARSET',
                    'title'                 => 'TITLE',
//                  'last_update'           => 'DATE'
                ),
                'inline_template'       => "{% META %}\n{% BODY %}\n{% NOTES %}",
            ),

            // ------------------
            // Filters gamuts options
            // ------------------

            // full gamuts stacks
            // each sub-item is constructed like "gamut_alias or class name : method or class name : method name"
            'initial_gamut' => array (
                'filter:Detab:init'          => '5',
                'filter:Emphasis:prepare'    => '10',
            ),
            'transform_gamut' => array (
                'tools:RemoveUtf8Marker'     => '5',
                'tools:StandardizeLineEnding'=> '10',
                'tools:AppendEndingNewLines' => '15',
                'filter:Detab'               => '20',
                'filter:HTML'                => '25',
                'tools:StripSpacedLines'     => '30',
            ),
            'document_gamut' => array (
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
            'span_gamut' => array (
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
            'block_gamut' => array (
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
            'html_block_gamut' => array (
                'filter:HTML'               => '10',
                'block_gamut'               => '20',
            ),
        );
    }

// ---------------------
// Static usage
// ---------------------

    public static function parse($content, $options = null)
    {
        $mde = new self($options);
        return (false === strpos($content, PHP_EOL) && file_exists($content)) ?
            $mde->transformSource($content) : $mde->transform($content);
    }

    public static function parseString($content, $options = null)
    {
        $mde = new self($options);
        return $mde->transform($content);
    }

    public static function parseSource($path, $options = null)
    {
        $mde = new self($options);
        return $mde->transformSource($path);
    }

// ---------------------
// Procedural usage
// ---------------------

    public function __construct($options = null)
    {
        // init the kernel
        $kernel = Kernel::getInstance();

        // init options
        $this
            ->resetOptions()
            ->setOptions($options)
        ;

        // init all dependencies
        $kernel
            ->set('MarkdownExtended',       $this)
            ->set('OutputFormatBag',        new OutputFormatBag)
            ->set('Grammar\GamutLoader',    new GamutLoader)
            ->set('ContentCollection',      new ContentCollection)
        ;

        // load required format
        $kernel->get('OutputFormatBag')
            ->load($kernel->getConfig('output_format'));
    }

    // init options as "config" dependency
    public function resetOptions()
    {
        Kernel::set('config', new Registry($this->getDefaults()));
        return $this;
    }

    public function setOptions($options)
    {
        if (is_string($options)) {
            return $this->setOptions(array('config_file'=>$options));
        }

        if (isset($options['config_file']) && !empty($options['config_file'])) {
            $path = $options['config_file'];
            unset($options['config_file']);

            if (!file_exists($path)) {
                $local_path = Kernel::getResourcePath($path, Kernel::RESOURCE_CONFIG);
                if (empty($local_path) || !file_exists($local_path)) {
                    throw new UnexpectedValueException(
                        sprintf('Configuration file "%s" not found', $path)
                    );
                }
                $path = $local_path;
            }

            $path_options = $this->loadConfigFile($path);
            unset($path_options['config_file']);
            $this->setOptions($path_options);
            $options['loaded_config_file'] = $path;
        }

        if (is_array($options) && !empty($options)) {
            foreach ($options as $var=>$val) {
                Kernel::setConfig($var, $val);
            }
        }

        return $this;
    }

    /**
     * @param   string|\MarkdownExtended\Content $content
     * @return  \MarkdownExtended\Content
     */
    public function transform($content, $name = null, $primary = true)
    {
        $kernel = Kernel::getInstance();

        if (!is_object($content) || !Kernel::valid($content, Kernel::TYPE_CONTENT)) {
            $content = new Content($content, $kernel->get('config')->getAll());
        }
        if (!is_null($name)) {
            $content->setTitle($name);
        }

        $content_collection = Kernel::get('ContentCollection');
        $content_collection->append($content);
        $index = $content_collection->key();
        $content_collection->next();
        if (!$content_collection->valid()) {
            $content_collection->seek($index);
        }
        $kernel
            ->set(Kernel::TYPE_CONTENT, function(){ return Kernel::get('ContentCollection')->current(); })
            ->set('Lexer',              new Lexer)
            ->set('DomId',              new DomIdRegistry)
        ;

        // actually parse content
        $kernel->get('Lexer')->parse($content);
        $body   = $content->getBody();
        $notes  = $content->getNotes();
        $meta   = $content->getMetadata();
/*//
var_export($body);
var_export($notes);
var_export($meta);
exit(PHP_EOL.'-- EXIT --'.PHP_EOL);
//*/

        // force template if needed
        $tpl = $kernel->getConfig('template');
        if (!is_null($tpl) && $tpl === 'auto') {
            $tpl = !(Helper::isSingleLine($body));
        }
        if (!$primary) {
            $tpl = false;
        }

        // load it in a template ?
        if (!empty($tpl) && false !== $tpl) {
            if (
                (
                    (is_string($tpl) && class_exists($tpl)) ||
                    is_object($tpl)
                ) &&
                Kernel::validate($tpl, Kernel::TYPE_TEMPLATE)
            ) {
                $templater = new $tpl;
            } else {
                $templater = new Templater;
            }
            $kernel->set(Kernel::TYPE_TEMPLATE, $templater);
            $content->setContent(
                $templater->parse($content, $tpl)
            );

        } else {

            // if source is a single line
            if (Helper::isSingleLine($body)) {
                $content->setContent(
                    preg_replace('#<[/]?p>#i', '', $body)
                );

            } else {
                $content->setContent(
                    (!empty($meta) ? $content->getMetadataFormatted() . PHP_EOL : '') .
                    $body .
                    (!empty($notes) ? PHP_EOL . $content->getNotesFormatted() : '')
                );
            }
        }

        // write the output in a file?
        $output = $kernel->getConfig('output');
        if (!empty($output) && $primary) {
            $name = $content->getMetadata('file_name');
            $path = Helper::fillPlaceholders(
                $output,
                (!empty($name) ?
                    pathinfo($name, PATHINFO_FILENAME) : Helper::header2Label($content->getTitle())
                )
            );
            if (file_exists($path) && $kernel->getConfig('force') !== true) {
                Helper::backupFile($path);
            }
            if (!file_exists(dirname($path))) {
                mkdir(dirname($path));
            }
            $written = file_put_contents($path, (string) $content, LOCK_EX);

            // return generated file path
            return $path;
        }

        // return the content object
        return $content;
    }

    public function transformSource($path, $primary = true)
    {
        if (!file_exists($path)) {
            throw new DomainException(
                sprintf('Source file "%s" not found', $path)
            );
        }
        if (!is_readable($path)) {
            throw new DomainException(
                sprintf('Source file "%s" is not readable', $path)
            );
        }

        $source     = file_get_contents($path, FILE_USE_INCLUDE_PATH);
        $content    = new Content($source, Kernel::get('config')->getAll());
        $content
            ->addMetadata('last_update', new DateTime('@'.filemtime($path)))
            ->addMetadata('file_name', $path)
        ;
        Kernel::addConfig('base_path', realpath(dirname($path)));
        return $this->transform($content, $path, $primary);
    }

    protected function loadConfigFile($path)
    {
        if (!file_exists($path)) {
            throw new InvalidArgumentException(
                sprintf('Configuration file "%s" not found', $path)
            );
        }
        if (!is_readable($path)) {
            throw new InvalidArgumentException(
                sprintf('Configuration file "%s" is not readable', $path)
            );
        }

        $ext = pathinfo($path, PATHINFO_EXTENSION);
        switch (strtolower($ext)) {
            case 'ini':
                $options = parse_ini_file($path, true);
                break;
            case 'json':
                $options = json_decode(file_get_contents($path), true);
                break;
            case 'php':
                $options = include $path;
                break;
            default:
                throw new InvalidArgumentException(
                    sprintf('Unknown configuration file type "%s"', $ext)
                );
        }

        return $options;
    }

}
