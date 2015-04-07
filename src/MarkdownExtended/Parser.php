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
        // init the kernel
        $kernel = Kernel::getInstance();

        // init options
        $this
            ->resetOptions()
            ->setOptions($options)
        ;

        // init all dependencies
        $kernel
            ->set('Parser',                 $this)
            ->set('OutputFormatBag',        new OutputFormatBag)
            ->set('Grammar\GamutLoader',    new GamutLoader)
            ->set('ContentCollection',      new ContentCollection)
        ;

        // load required format
        $kernel->get('OutputFormatBag')
            ->load($kernel->getConfig('output_format'));
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
        Kernel::set('config', new Registry(MarkdownExtended::getDefaults()));
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
                Kernel::setConfig($var, $val);
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
        /* @var $kernel \MarkdownExtended\API\Kernel */
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
            $written = Helper::writeFile($path, (string) $content);

            // return generated file path
            return $path;
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
        $content    = new Content($source, Kernel::get('config')->getAll());
        $content
            ->addMetadata('last_update', new DateTime('@'.filemtime($path)))
            ->addMetadata('file_name', $path)
        ;
        Kernel::addConfig('base_path', realpath(dirname($path)));
        return $this->transform($content, $path, $primary);
    }

    /**
     * Loads a configuration file
     *
     * @param string $path
     *
     * @return array|mixed
     *
     * @throws \MarkdownExtended\Exception\InvalidArgumentException if the file can not be found or is not readable
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

}
