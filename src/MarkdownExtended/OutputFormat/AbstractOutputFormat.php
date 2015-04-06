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
use \MarkdownExtended\MarkdownExtended;
use \MarkdownExtended\API\ContentInterface;
use \MarkdownExtended\Util\Helper;

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
     * Run a gamut stack from a filter or tool
     *
     * @param   string  $gamut  The name of a single Gamut or a Gamuts stack
     * @param   string  $text
     * @param   bool    $forced Forces to run the gamut event if it is disabled
     * @return  string
     */
    public function runGamut($gamut, $text, $forced = false)
    {
        $loader = Kernel::get('Grammar\GamutLoader');
        return ($loader->isGamutEnabled($gamut) || $forced ? $loader->runGamut($gamut, $text) : $text);
    }

    /**
     * This will try to call a method `build{TagName}()` if it exists, then will try to use
     * the object `$tags_map` static to automatically find what to do, and then call the
     * default `getTagString()` method passing it the arguments.
     *
     * @param   string  $tag_name
     * @param   string  $content
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    public function buildTag($tag_name, $content = null, array $attributes = array())
    {
        $_method = 'build'.Helper::toCamelCase($tag_name);

        if (isset($this->tags_map[$tag_name]) && isset($this->tags_map[$tag_name]['prefix'])) {
            $attributes['mde-prefix'] = $this->tags_map[$tag_name]['prefix'];
        }

        if (method_exists($this, $_method)) {
            return call_user_func_array(
                array($this, $_method),
                array($content, $attributes)
            );
        } elseif (isset($this->tags_map[$tag_name])) {
            $new_tag_name = isset($this->tags_map[$tag_name]['tag']) ?
                $this->tags_map[$tag_name]['tag'] : $tag_name;
            $closable = isset($this->tags_map[$tag_name]['closable']) ?
                $this->tags_map[$tag_name]['closable'] : false;
            return call_user_func_array(
                array($this, 'getTagString'),
                array($content, $new_tag_name, $attributes, $closable)
            );
        } else {
            return call_user_func_array(
                array($this, 'getTagString'),
                array($content, $tag_name, $attributes)
            );
        }
    }

    /**
     * @param   string  $content
     * @param   string  $tag_name
     * @param   array   $attributes     An array of attributes constructed like "variable=>value" pairs
     * @return  string
     */
    abstract public function getTagString($content, $tag_name, array $attributes = array());

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
//            ) . "\n\n";
        }

        return $this
            ->buildTag('block',
                $this->buildTag('ordered_list', implode(PHP_EOL, $data)),
                array('class'=>'footnotes')
            );
    }

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

}

// Endfile