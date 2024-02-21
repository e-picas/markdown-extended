<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended;

use MarkdownExtended\API\ContentInterface;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\API\Optionable;
use MarkdownExtended\Grammar\Lexer;
use MarkdownExtended\Grammar\GamutLoader;
use MarkdownExtended\Exception\InvalidArgumentException;
use MarkdownExtended\Exception\FileSystemException;
use MarkdownExtended\Util\ContentCollection;
use MarkdownExtended\Util\Helper;
use MarkdownExtended\Util\DomIdRegistry;
use MarkdownExtended\Util\Registry;
use DateTime;

/**
 * Global MarkdownExtended parser
 */
class Parser extends Optionable
{
    /**
     * Constructs a new parser with optional custom options or configuration file
     *
     * @param null|string|array $options A set of options or a configuration file path to override defaults
     */
    public function __construct($options = null)
    {
        // init options
        $this
            ->resetOptions()
            ->setOptions($options)
        ;

        // init all dependencies
        Kernel::getInstance()
            ->set('Parser', $this)
            ->set('OutputFormatBag', new OutputFormatBag())
            ->set('GamutLoader', new GamutLoader())
            ->set('ContentCollection', new ContentCollection())
        ;

        // load required format
        Kernel::getInstance()
            ->get('OutputFormatBag')
                ->load(Kernel::getInstance()->getConfig('output_format'));
    }

    /**
     * Transforms a string
     *
     * @param   string|\MarkdownExtended\API\ContentInterface $content
     * @param   null $name
     * @param   bool $primary
     *
     * @return \MarkdownExtended\API\ContentInterface|string
     */
    public function transform($content, $name = null, $primary = true)
    {
        $content = $this->getApiContent($content);
        if (!is_null($name)) {
            $content->setTitle($name);
        }

        // actually parse content
        $content = $this->parseContent($content);

        // guess the title if it is NOT empty
        // @TODO - Try to make it better extracting directly from the MD source
        // something strange is here: \MarkdownExtended\Grammar\Filter\Header::_setContentTitle()
        if (strtolower(Kernel::getInstance()->getConfig('output_format')) == 'html') {
            $titles = Helper::getTextBetweenTags($content->getBody(), 'h1');
            if (isset($titles[0])) {
                $content->setTitle($titles[0]);
            }
        }

        // force template if needed
        $tpl = Kernel::getInstance()->getConfig('template');
        if (!is_null($tpl) && $tpl === 'auto') {
            //            $tpl = !(Helper::isSingleLine($content->getBody()));
            $meta = $content->getMetadataFormatted();
            $tpl = !(empty($meta));
        }

        // load it in a template ?
        if (!empty($tpl) && $primary) {
            $this->parseTemplate($tpl, $content);
        } else {
            $this->constructContent($content);
        }

        //        $this->_hardDebugContent($content);
        // write the output in a file?
        $output = Kernel::getInstance()->getConfig('output');
        if (!empty($output) && $primary) {
            // return generated file path
            return $this->writeOutputFile($output, $content);
        }

        // return the content object
        return $content;
    }

    /**
     * Alias of `self::transform()`
     *
     * @param   string|\MarkdownExtended\API\ContentInterface $content
     * @param   null $name
     * @param   bool $primary
     *
     * @return \MarkdownExtended\API\ContentInterface|string
     */
    public function transformString($content, $name = null, $primary = true)
    {
        return $this->transform($content, $name, $primary);
    }

    /**
     * Transforms a source file
     *
     * @param   string $path
     * @param   bool $primary
     * @return  \MarkdownExtended\API\ContentInterface|string
     *
     * @throws \MarkdownExtended\Exception\FileSystemException if the file can not be found or read
     */
    public function transformSource($path, $primary = true)
    {
        if (!file_exists($path)) {
            throw new FileSystemException(
                sprintf('Source file "%s" not found', $path)
            );
        }
        if (!is_readable($path)) {
            throw new FileSystemException(
                sprintf('Source file "%s" is not readable', $path)
            );
        }

        $source     = Helper::readFile($path);
        $content    = new Content($source, Kernel::getInstance()->get('config')->getAll());
        $content
            ->addMetadata('last_update', new DateTime('@'.filemtime($path)))
            ->addMetadata('file_name', $path)
        ;
        Kernel::getInstance()->addConfig('base_path', realpath(dirname($path)));
        $filename = Kernel::getInstance()->applyConfig('filepath_to_title', [$path]);
        return $this->transform($content, $filename, $primary);
    }

    /**
     * Gets a valid content object
     *
     * @param $content
     *
     * @return \MarkdownExtended\API\ContentInterface
     */
    protected function getApiContent($content)
    {
        if (!is_object($content) || !Kernel::getInstance()->valid($content, Kernel::TYPE_CONTENT)) {
            $content = new Content($content, Kernel::getInstance()->get('config')->getAll());
        }
        return $content;
    }

    /**
     * Actually do content's parsing
     *
     * @param \MarkdownExtended\API\ContentInterface $content
     *
     * @return \MarkdownExtended\API\ContentInterface
     */
    protected function parseContent(ContentInterface $content)
    {
        $this->_registerContent($content);
        Kernel::getInstance()
            ->set(Kernel::TYPE_CONTENT, function () {
                return Kernel::get('ContentCollection')->current();
            })
            ->set('Lexer', new Lexer())
            ->set('DomId', new DomIdRegistry())
        ;

        // actually parse content
        Kernel::getInstance()->get('Lexer')->parse($content);

        return $content;
    }

    /**
     * Constructs a content's content
     *
     * This will load a simple not-paragraphed content if the original
     * body seems to be a single line and a concatenation of the
     * metadata + body + notes otherwise.
     *
     * @param \MarkdownExtended\API\ContentInterface $content
     *
     * @return \MarkdownExtended\API\ContentInterface
     */
    protected function constructContent(ContentInterface $content)
    {
        $body = $content->getBody();
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
        return $content;
    }

    /**
     * Inserts a content in a template
     *
     * @param string|\MarkdownExtended\API\TemplateInterface $template
     * @param \MarkdownExtended\API\ContentInterface $content
     *
     * @return \MarkdownExtended\API\ContentInterface
     */
    protected function parseTemplate($template, ContentInterface $content)
    {
        // create a new templater if needed
        if (
            (
                (is_string($template) && class_exists($template)) ||
                is_object($template)
            ) &&
            Kernel::getInstance()->validate($template, Kernel::TYPE_TEMPLATE)
        ) {
            $templater = new $template();
        } else {
            $templater = new Templater();
        }

        // register the templater
        Kernel::getInstance()->set(Kernel::TYPE_TEMPLATE, $templater);

        // parse content into template
        $content->setContent(
            $templater->parse($content, $template)
        );

        return $content;
    }

    /**
     * Writes a content in a local path and returns written length
     *
     * @param string $path
     * @param \MarkdownExtended\API\ContentInterface $content
     *
     * @return int
     */
    protected function writeOutputFile($path, ContentInterface $content)
    {
        // construct output file name
        $name = $content->getMetadata('file_name');
        $path = Helper::fillPlaceholders(
            $path,
            (
                !empty($name) ?
                pathinfo($name, PATHINFO_FILENAME) : Helper::header2Label($content->getTitle())
            )
        );

        // make a backup if `option[force]!==true`
        $backup = (bool) Kernel::getInstance()->getConfig('force') !== true;

        // write output
        Helper::writeFile($path, (string) $content, $backup);

        // return created path
        return $path;
    }

    /**
     * Register a new content in collection
     *
     * @access private
     */
    private function _registerContent(ContentInterface $content)
    {
        $collection = Kernel::getInstance()->get('ContentCollection');
        $collection->append($content);
        $index      = $collection->key();
        $collection->next();
        if (!$collection->valid()) {
            $collection->seek($index);
        }
    }

    /**
     * Hard debug of a content object
     *
     * @access private
     */
    private function _hardDebugContent(ContentInterface $content)
    {
        echo Helper::debug($content->getBody(), 'content body');
        echo Helper::debug($content->getNotes(), 'content notes', false);
        echo Helper::debug($content->getMetadata(), 'content metadata', false);
    }
}
