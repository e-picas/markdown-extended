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

use MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\Grammar\Lexer;
use \MarkdownExtended\Grammar\GamutLoader;
use \MarkdownExtended\Exception\DomainException;
use \MarkdownExtended\Exception\InvalidArgumentException;
use \MarkdownExtended\Exception\UnexpectedValueException;
use \MarkdownExtended\Util\ContentCollection;
use \MarkdownExtended\Util\Helper;
use \MarkdownExtended\Util\DomIdRegistry;
use \MarkdownExtended\Util\Registry;
use \DateTime;

/**
 * Global MarkdownExtended parser
 */
class Parser
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
        $this->getKernel()
            ->set('Parser',             $this)
            ->set('OutputFormatBag',    new OutputFormatBag)
            ->set('GamutLoader',        new GamutLoader)
            ->set('ContentCollection',  new ContentCollection)
        ;

        // load required format
        $this->getKernel()
            ->get('OutputFormatBag')
                ->load($this->getKernel()->getConfig('output_format'));
    }

    /**
     * Gets app's Kernel
     *
     * @return \MarkdownExtended\API\Kernel
     */
    public static function getKernel()
    {
        return Kernel::getInstance();
    }

    /**
     * Resets options to defaults
     *
     * @return $this
     *
     * @see \MarkdownExtended\MarkdownExtended::getDefaults()
     */
    public function resetOptions()
    {
        $this->getKernel()->set('config', new Registry(MarkdownExtended::getDefaults()));
        return $this;
    }

    /**
     * Defines custom options or configuration file
     *
     * @param string|array $options A set of options or a configuration file path to override defaults
     *
     * @return $this
     */
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
                $this->getKernel()->setConfig($var, $val);
            }
        }

        return $this;
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

        // force template if needed
        $tpl = $this->getKernel()->getConfig('template');
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
        $output = $this->getKernel()->getConfig('output');
        if (!empty($output) && $primary) {
            // return generated file path
            return $this->writeOutputFile($output, $content);
        }

        // return the content object
        return $content;
    }

    /**
     * Transforms a source file
     *
     * @param   string $path
     * @param   bool $primary
     * @return  \MarkdownExtended\API\ContentInterface|string
     *
     * @throws \MarkdownExtended\Exception\DomainException if the file can not be found or read
     */
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

        $source     = Helper::readFile($path);
        $content    = new Content($source, $this->getKernel()->get('config')->getAll());
        $content
            ->addMetadata('last_update', new DateTime('@'.filemtime($path)))
            ->addMetadata('file_name', $path)
        ;
        $this->getKernel()->addConfig('base_path', realpath(dirname($path)));
        $filename = $this->getKernel()->applyConfig('filepath_to_title', array($path));
        return $this->transform($content, $filename, $primary);
    }

    /**
     * Loads a configuration file
     *
     * @param string $path
     *
     * @return array|mixed
     *
     * @throws \MarkdownExtended\Exception\InvalidArgumentException if the file can not be found, is not readable or is of an unknown type
     */
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
                $options = json_decode(Helper::readFile($path), true);
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

    /**
     * Gets a valid content object
     *
     * @param $content
     *
     * @return \MarkdownExtended\API\ContentInterface
     */
    protected function getApiContent($content)
    {
        if (!is_object($content) || !$this->getKernel()->valid($content, Kernel::TYPE_CONTENT)) {
            $content = new Content($content, $this->getKernel()->get('config')->getAll());
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
        $this->getKernel()
            ->set(Kernel::TYPE_CONTENT, function () { return Kernel::get('ContentCollection')->current(); })
            ->set('Lexer',              new Lexer)
            ->set('DomId',              new DomIdRegistry)
        ;

        // actually parse content
        $this->getKernel()->get('Lexer')->parse($content);

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
            $this->getKernel()->validate($template, Kernel::TYPE_TEMPLATE)
        ) {
            $templater = new $template;
        } else {
            $templater = new Templater;
        }

        // register the templater
        $this->getKernel()->set(Kernel::TYPE_TEMPLATE, $templater);

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
            (!empty($name) ?
                pathinfo($name, PATHINFO_FILENAME) : Helper::header2Label($content->getTitle())
            )
        );

        // make a backup `option[force]!==true`
        $backup = (bool) $this->getKernel()->getConfig('force') !== true;

        // write output
        Helper::writeFile($path, (string) $content, $backup);

        // return created path
        return $path;
    }

    // register a new content in collection
    private function _registerContent(ContentInterface $content)
    {
        $content_collection = $this->getKernel()->get('ContentCollection');
        $content_collection->append($content);
        $index = $content_collection->key();
        $content_collection->next();
        if (!$content_collection->valid()) {
            $content_collection->seek($index);
        }
    }

    // hard debug of a content object
    private function _hardDebugContent(ContentInterface $content)
    {
        echo Helper::debug($content->getBody(), 'content body');
        echo Helper::debug($content->getNotes(), 'content notes', false);
        echo Helper::debug($content->getMetadata(), 'content metadata', false);
    }
}
