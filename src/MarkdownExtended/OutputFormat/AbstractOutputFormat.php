<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\OutputFormat;

use \MarkdownExtended\API\Kernel;
use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\Util\Helper;

/**
 * A basic output format class
 */
abstract class AbstractOutputFormat
{

    /**
     * @var array An array of items constructed like:
     *
     *      interface tag name => array(
     *          'tag' => HTML tag name,
     *          'closable' => bool, // is the tag closed without content (i.e. "<br />")
     *      )
     */
    protected $tags_map = array();

    /**
     * This will try to call a method `build{TagName}()` if it exists, then will try to use
     * the object `$tags_map` static to automatically find what to do, and then call the
     * default `getTagString()` method passing it the arguments.
     *
     * @param   string  $tag_name
     * @param   string  $content
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     *
     * @return  string
     */
    public function buildTag($tag_name, $content = null, array $attributes = array())
    {
        $_method = Helper::toCamelCase('build_'.$tag_name);

        if (isset($this->tags_map[$tag_name]) && isset($this->tags_map[$tag_name]['prefix'])) {
            $attributes['mde-prefix'] = $this->tags_map[$tag_name]['prefix'];
        }

        if (method_exists($this, $_method)) {
            return call_user_func_array(
                array($this, $_method),
                array($content, $attributes)
            );
        }

        $closable = false;
        if (isset($this->tags_map[$tag_name])) {
            $closable = isset($this->tags_map[$tag_name]['closable']) ?
                $this->tags_map[$tag_name]['closable'] : false;
            $tag_name = isset($this->tags_map[$tag_name]['tag']) ?
                $this->tags_map[$tag_name]['tag'] : $tag_name;
        }

        return call_user_func_array(
            array($this, 'getTagString'),
            array($content, $tag_name, $attributes, $closable)
        );
    }

    /**
     * @param   string  $content
     * @param   string  $tag_name
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    abstract public function getTagString($content, $tag_name, array $attributes = array());

    /**
     * Formats a data stack list as string
     *
     * @param   string $type
     * @param   array $data
     * @param   \MarkdownExtended\API\ContentInterface $content
     *
     * @return string
     */
    public function getDataToString($type, array $data, ContentInterface $content)
    {
        if (empty($data)) {
            return '';
        }
        $method = Helper::toCamelCase('get_'.$type.'_to_string');
        if (!method_exists($this, $method)) {
            $method = 'getDataListToString';
        }
        return call_user_func(array($this, $method), $data, $content);
    }

    /**
     * Gets a data list as string
     *
     * @param   array $list
     * @param   \MarkdownExtended\API\ContentInterface $content
     *
     * @return string
     */
    public function getDataListToString(array $list, ContentInterface $content)
    {
        if (empty($list)) {
            return '';
        }

        $data = array();
        foreach ($list as $var=>$val) {
            $data[] = $this->buildTag('list_item', $val);
        }
        return $this
            ->buildTag('block',
                $this->buildTag('unordered_list', implode(PHP_EOL, $data))
            );
    }

    /**
     * Gets the notes list as string
     *
     * @param   array $notes
     * @param   \MarkdownExtended\API\ContentInterface $content
     *
     * @return string
     */
    public function getNotesToString(array $notes, ContentInterface $content)
    {
        if (empty($notes)) {
            return '';
        }

        $data = array();
        foreach ($notes as $var=>$val) {
            $data[] = $this->buildTag(
                'list_item',
                $val['text'],
                array('id' => $val['note-id'])
            );
        }

        return $this
            ->buildTag('block',
                $this->buildTag('ordered_list', implode(PHP_EOL, $data)),
                array('class'=>'footnotes')
            );
    }

    /**
     * Gets the metadata list as string
     *
     * @param   array $metadata
     * @param   \MarkdownExtended\API\ContentInterface $content
     *
     * @return string
     */
    public function getMetadataToString(array $metadata, ContentInterface $content)
    {
        $specials   = Kernel::getConfig('special_metadata');
        $data       = array();
        foreach ($metadata as $var=>$val) {
            if (!in_array($var, $specials)) {
                $data[] = $this->buildTag('meta_data', null, array(
                    'name'      => $var,
                    'content'   => $val
                ));
            }
        }
        return implode(PHP_EOL, $data);
    }

    /**
     * Gets the table of contents list as string
     *
     * @param   array $toc
     * @param   \MarkdownExtended\API\ContentInterface $content
     *
     * @return string
     */
    public function getMenuToString(array $toc, ContentInterface $content)
    {
        if (empty($toc)) {
            return '';
        }
        return $this->_processMenuToString(
            array_values($toc),
            array('class'=>'table-of-contents')
        );
    }

    /**
     * Actually build a menu from an array of items
     *
     * @param array $items
     * @param array $params
     * @return string
     */
    protected function _processMenuToString(array $items, array $params = array())
    {
        $data = array();
        foreach ($items as $k=>$item) {
            /* @var $item \MarkdownExtended\Util\Menu\MenuItem */
            $text = $item->getContent() . (
                $item->hasChildren() ?
                    $this->_processMenuToString($item->getChildren()) : ''
            );
            $data[] = $this->buildTag(
                'list_item',
                $this->buildTag(
                    'link',
                    $text,
                    array('href'=>'#'.$item->getAttribute('id'))
                )
            );
        }
        return $this
            ->buildTag('block',
                $this->buildTag('unordered_list', implode(PHP_EOL, $data)),
                $params
            );
    }
}
