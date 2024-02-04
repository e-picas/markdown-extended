<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\API\Kernel;
use MarkdownExtended\Grammar\Lexer;
use MarkdownExtended\Grammar\GamutLoader;

/**
 * Process Markdown notes: footnotes, glossary and bibliography notes
 *
 * @TODO: write the right reference for second call of the same note
 */
class Note extends Filter
{
    const FOOTNOTE_DEFAULT      = 0;

    const FOOTNOTE_GLOSSARY     = 1;

    const FOOTNOTE_BIBLIOGRAPHY = 2;

    const FOOTNOTE_PREFIX       = 'fn';

    const GLOSSARY_PREFIX       = 'fng';

    const BIBLIOGRAPHY_PREFIX   = 'fnb';

    const FOOTNOTE_NAME_DEFAULT         = 'footnote';

    const FOOTNOTE_NAME_GLOSSARY        = 'glossary';

    const FOOTNOTE_NAME_BIBLIOGRAPHY    = 'bibliography';

    /**
     * @var int  Give the current footnote, glossary or bibliography number.
     */
    public static $footnote_counter;

    /**
     * @var int  Give the total parsed notes number.
     */
    public static $notes_counter;

    /**
     * @var array  Ordered notes
     */
    public static $notes_ordered;

    /**
     * @var array  Written notes
     */
    public static $written_notes = [];

    /**
     * Prepare all required arrays
     */
    public function _setup()
    {
        Kernel::setConfig('footnotes', []);
        Kernel::setConfig('glossaries', []);
        Kernel::setConfig('bibliographies', []);
        self::$notes_ordered    = [];
        self::$written_notes    = [];
        self::$footnote_counter = 1;
        self::$notes_counter    = 0;
    }

    /**
     * Strips link definitions from text, stores the URLs and titles in hash references.
     *
     * @param   string  $text
     * @return  string
     */
    public function strip($text)
    {
        $less_than_tab = Kernel::getConfig('less_than_tab');

        // Link defs are in the form: [^id]: url "optional title"
        $text = preg_replace_callback(
            '{
            ^[ ]{0,'.$less_than_tab.'}\[\^(.+?)\][ ]?:  # note_id = $1
              [ ]*
              \n?                           # maybe *one* newline
            (                               # text = $2 (no blank lines allowed)
                (?:
                    .+                      # actual text
                |
                    \n                      # newlines but
                    (?!\[\^.+?\]:\s)        # negative lookahead for footnote marker.
                    (?!\n+[ ]{0,3}\S)       # ensure line is not blank and followed
                                            # by non-indented content
                )*
            )
            }xm',
            [$this, '_strip_callback'],
            $text
        );

        // Link defs are in the form: [#id]: url "optional title"
        $text = preg_replace_callback(
            '{
            ^[ ]{0,'.$less_than_tab.'}\[(\#.+?)\][ ]?:  # note_id = $1
              [ ]*
              \n?                           # maybe *one* newline
            (                               # text = $2 (no blank lines allowed)
                (?:
                    .+                      # actual text
                |
                    \n                      # newlines but
                    (?!\[\^.+?\]:\s)        # negative lookahead for footnote marker.
                    (?!\n+[ ]{0,3}\S)       # ensure line is not blank and followed
                                            # by non-indented content
                )*
            )
            }xm',
            [$this, '_strip_callback'],
            $text
        );

        return $text;
    }

    /**
     * Build the footnote and strip it from content
     *
     * @param   array   $matches    Results from the `transform()` function
     * @return  string
     */
    protected function _strip_callback($matches)
    {
        if (0 !== preg_match('/^(<p>)?glossary:/i', $matches[2])) {
            Kernel::addConfig('glossaries', [
                (Kernel::getConfig('glossarynote_id_prefix') . $matches[1]) =>
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':Outdent', $matches[2]),
            ]);
        } elseif (0 !== preg_match('/^\#(.*)?/i', $matches[1])) {
            Kernel::addConfig('bibliographies', [
                (Kernel::getConfig('bibliographynote_id_prefix') . substr($matches[1], 1)) =>
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':Outdent', $matches[2]),
            ]);
        } else {
            Kernel::addConfig('footnotes', [
                (Kernel::getConfig('footnote_id_prefix') . $matches[1]) =>
                    Lexer::runGamut(GamutLoader::TOOL_ALIAS.':Outdent', $matches[2]),
            ]);
        }
        return '';
    }

    /**
     * Replace footnote references in $text [string][#id] and [^id] with a special text-token
     * which will be replaced by the actual footnote marker in appendFootnotes.
     *
     * {@inheritDoc}
     */
    public function transform($text)
    {
        if (Kernel::getConfig('in_anchor') === false) {
            $text = preg_replace('{\[\^(.+?)\]}', "F\x1Afn:\\1\x1A:", $text);
            $text = preg_replace('{\[(.+?)\]\[\#(.+?)\]}', " [\\1, F\x1Afn:\\2\x1A:]", $text);
        }
        return $text;
    }

    /**
     * Append footnote list to text.
     *
     * @param   string  $text
     * @return  string
     */
    public function append($text)
    {
        $footnotes      = Kernel::getConfig('footnotes');
        $glossaries     = Kernel::getConfig('glossaries');
        $bibliographies = Kernel::getConfig('bibliographies');

        // First loop for references
        if (!empty(self::$notes_ordered)) {
            $tmp_notes_ordered = self::$notes_ordered;
            $_counter = 0;
            while (!empty($tmp_notes_ordered)) {
                $note_id = key($tmp_notes_ordered);
                unset($tmp_notes_ordered[$note_id]);
                if (!array_key_exists($note_id, self::$written_notes)) {
                    self::$written_notes[$note_id] = $_counter++;
                }
            }
        }

        $text = preg_replace_callback(
            '{F\x1Afn:(.*?)\x1A:}',
            [$this, '_append_callback'],
            $text
        );

        while (!empty(self::$notes_ordered)) {
            reset(self::$notes_ordered);
            $note_id = key(self::$notes_ordered);
            unset(self::$notes_ordered[$note_id]);

            // footnotes
            if (isset($footnotes[$note_id])) {
                self::transformFootnote($note_id);

                // glossary
            } elseif (isset($glossaries[$note_id])) {
                self::transformGlossary($note_id);

                // bibliographies
            } elseif (isset($bibliographies[$note_id])) {
                self::transformBibliography($note_id);
            }
        }

        return $text;
    }

    /**
     * Append footnote list to Content.
     *
     * @param   string  $note_id
     * @return  void
     */
    public function transformFootnote($note_id)
    {
        $footnotes = Kernel::getConfig('footnotes');
        if (!empty($footnotes[$note_id])) {
            $this->_doTransformNote($note_id, $footnotes[$note_id], self::FOOTNOTE_DEFAULT);
        }
    }

    /**
     * Append glossary notes list to Content.
     *
     * @param   string  $note_id
     * @return  void
     */
    public function transformGlossary($note_id)
    {
        $glossaries = Kernel::getConfig('glossaries');
        if (!empty($glossaries[$note_id])) {
            $glossary = substr($glossaries[$note_id], strlen('glossary:'));
            $glossary = preg_replace_callback(
                '{
                    ^(.*?)                          # $1 = term
                    \s*
                    (?:\(([^\(\)]*)\)[^\n]*)?       # $2 = optional sort key
                    \n{1,}
                    (.*?)
                    }x',
                [$this, '_glossary_callback'],
                $glossary
            );
            $this->_doTransformNote($note_id, $glossary, self::FOOTNOTE_GLOSSARY);
        }
    }

    /**
     * Build the glossary entry
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _glossary_callback($matches)
    {
        $text = Kernel::get('OutputFormatBag')
            ->buildTag('span', trim($matches[1]), ['class' => 'glossary name']);
        if (isset($matches[3])) {
            $text .= Kernel::get('OutputFormatBag')
                ->buildTag('span', $matches[2], ['class' => 'glossary sort', 'style' => 'display:none']);
        }
        return $text . "\n\n" . (isset($matches[3]) ? $matches[3] : $matches[2]);
    }

    /**
     * Append bibliography notes list to Content.
     *
     * @param   string  $note_id
     * @return  void
     */
    public function transformBibliography($note_id)
    {
        $bibliographies = Kernel::getConfig('bibliographies');
        if (!empty($bibliographies[$note_id])) {
            $bibliography = $bibliographies[$note_id];
            $bibliography = preg_replace_callback(
                '{
                    ^\#(.*?)                        # $1 = term
                    \s*
                    (?:\(([^\(\)]*)\)[^\n]*)?       # $2 = optional sort key
                    \n{1,}
                    (.*?)
                    }x',
                [$this, '_bibliography_callback'],
                $bibliography
            );
            $this->_doTransformNote($note_id, $bibliography, self::FOOTNOTE_BIBLIOGRAPHY);
        }
    }

    /**
     * Build the bibliography entry
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _bibliography_callback($matches)
    {
        $text = Kernel::get('OutputFormatBag')
            ->buildTag('span', trim($matches[1]), ['class' => 'bibliography name']);
        return $text . "\n\n" . $matches[2];
    }

    /**
     * Do a footnote transformation
     *
     * @param   string  $note_id
     * @param   string  $note_content
     * @param   int     $type
     * @return  void
     */
    protected function _doTransformNote($note_id, $note_content, $type = self::FOOTNOTE_DEFAULT)
    {
        if (!empty($note_content)) {
            ++self::$notes_counter;
            $type_info              = $this->getTypeInfo($type);

            $note_content   .= "\n"; // Need to append newline before parsing.
            $note_content   = Lexer::runGamut('html_block_gamut', $note_content . "\n");
            $note_content   = preg_replace_callback(
                '{F\x1Afn:(.*?)\x1A:}',
                [$this, '_append_callback'],
                $note_content
            );

            $note_id        = Lexer::runGamut(GamutLoader::TOOL_ALIAS.':EncodeAttribute', $note_id);
            $backlink_id    = Kernel::get('DomId')
                                ->get($type_info['prefix'] . 'ref:' . $note_id);
            $footlink_id    = Kernel::get('DomId')
                                ->get($type_info['prefix'] . ':' . $note_id);

            $attributes             = [];
            $attributes['rev']      = $type_info['name'];
            $attributes['counter']  = self::$notes_counter;
            $attributes['href']     = '#' . $backlink_id;

            $note = [
                'count'     => self::$notes_counter,
                'type'      => $type_info['name'],
                'in-text-id' => $backlink_id,
                'note-id'   => $footlink_id,
                'text'      => Kernel::get('OutputFormatBag')
                                ->buildTag(
                                    $type_info['outputformat_methods']['item'],
                                    $note_content,
                                    $attributes
                                ),
            ];
            Kernel::get(Kernel::TYPE_CONTENT)
                ->addNote($note, $note_id);
        }
    }

    /**
     * Append footnote and glossary list to text.
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _append_callback($matches)
    {
        $note_id    = $matches[1];
        $note_num   = $note_ref     = null;

        // Create footnote marker only if it has a corresponding footnote *and*
        // the footnote hasn't been used by another marker.
        $node_id    = Kernel::getConfig('footnote_id_prefix') . $note_id;
        $footnotes  = Kernel::getConfig('footnotes');
        if (isset($footnotes[$node_id])) {
            $type_info      = $this->getTypeInfo(self::FOOTNOTE_DEFAULT);
            // Transfer footnote content to the ordered list.
            self::$notes_ordered[$node_id] = $footnotes[$node_id];
            $note_num       = array_key_exists($node_id, self::$written_notes) ?
                                self::$written_notes[$node_id] : self::$footnote_counter++;
            $note_ref       = $node_id;
        }

        // Create glossary marker only if it has a corresponding note *and*
        // the glossary hasn't been used by another marker.
        $glossary_node_id = Kernel::getConfig('glossarynote_id_prefix') . $note_id;
        $glossaries = Kernel::getConfig('glossaries');
        if (isset($glossaries[$glossary_node_id])) {
            $type_info      = $this->getTypeInfo(self::FOOTNOTE_GLOSSARY);
            // Transfer footnote content to the ordered list.
            self::$notes_ordered[$glossary_node_id] = $glossaries[$glossary_node_id];
            $note_num       = array_key_exists($note_id, self::$written_notes) ?
                                self::$written_notes[$note_id] : self::$footnote_counter++;
            $note_ref       = $glossary_node_id;
        }

        // Create bibliography marker only if it has a corresponding note *and*
        // the glossary hasn't been used by another marker.
        $bibliography_node_id = Kernel::getConfig('bibliographynote_id_prefix') . $note_id;
        $bibliographies = Kernel::getConfig('bibliographies');
        if (isset($bibliographies[$bibliography_node_id])) {
            $type_info      = $this->getTypeInfo(self::FOOTNOTE_BIBLIOGRAPHY);
            // Transfer footnote content to the ordered list.
            self::$notes_ordered[$bibliography_node_id] = $bibliographies[$bibliography_node_id];
            $note_num       = array_key_exists($note_id, self::$written_notes) ?
                                self::$written_notes[$note_id] : self::$footnote_counter++;
            $note_ref       = $bibliography_node_id;
        }

        if (isset($type_info) && !empty($note_id) && !empty($note_num) && !empty($note_ref)) {
            $backlink_id        = Kernel::get('DomId')->get($type_info['prefix'] . 'ref:' . $note_ref);
            $footlink_id        = Kernel::get('DomId')->get($type_info['prefix'] . ':' . $note_ref);

            $attributes             = [];
            $attributes['rel']      = $type_info['name'];
            $attributes['href']     = '#' . $footlink_id;
            $attributes['counter']  = $note_num;
            $attributes['backlink_id'] = $backlink_id;

            return Kernel::get('OutputFormatBag')
                ->buildTag(
                    $type_info['outputformat_methods']['link'],
                    $note_num,
                    $attributes
                );
        }

        return '[^' . $matches[1] . ']';
    }

    public static function getTypeInfo($type = self::FOOTNOTE_DEFAULT)
    {
        $data = [];
        switch ($type) {
            case self::FOOTNOTE_DEFAULT:
                $data = [
                    'name'                  => self::FOOTNOTE_NAME_DEFAULT,
                    'prefix'                => self::FOOTNOTE_PREFIX,
                    'outputformat_methods'  => [
                        'item'              => 'footnote_standard_item',
                        'link'              => 'footnote_standard_link',
                    ],
                ];
                break;
            case self::FOOTNOTE_GLOSSARY:
                $data = [
                    'name'                  => self::FOOTNOTE_NAME_GLOSSARY,
                    'prefix'                => self::GLOSSARY_PREFIX,
                    'outputformat_methods'  => [
                        'item'              => 'footnote_glossary_item',
                        'link'              => 'footnote_glossary_link',
                    ],
                ];
                break;
            case self::FOOTNOTE_BIBLIOGRAPHY:
                $data = [
                    'name'                  => self::FOOTNOTE_NAME_BIBLIOGRAPHY,
                    'prefix'                => self::BIBLIOGRAPHY_PREFIX,
                    'outputformat_methods'  => [
                        'item'              => 'footnote_bibliography_item',
                        'link'              => 'footnote_bibliography_link',
                    ],
                ];
                break;
        }
        return $data;
    }
}
