<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2013 Pierre Cassat
 *
 * original MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * original PHP Markdown & Extra
 * Copyright (c) 2004-2012 Michel Fortin  
 * <http://michelf.com/projects/php-markdown/>
 *
 * original Markdown
 * Copyright (c) 2004-2006 John Gruber  
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended,
    MarkdownExtended\Grammar\Filter,
    MarkdownExtended\Helper as MDE_Helper,
    MarkdownExtended\Exception as MDE_Exception;

/**
 * Process Markdown notes: footnotes, glossary and bibliography notes
 *
 * @todo write the right reference for second call of the same note
 */
class Note extends Filter
{
    
    /**
     * Give the current footnote, glossary or citation number.
     */
    static $footnote_counter;
        
    /**
     * Give the total parsed notes number.
     */
    static $notes_counter;
        
    /**
     * Ordered notes
     */
    static $notes_ordered;

    /**
     * Written notes
     */
    static $written_notes;

    /**
     * Prepare all required arrays
     */
    public function _setup()
    {
        MarkdownExtended::setVar('footnotes', array());
        MarkdownExtended::setVar('glossaries', array());
        MarkdownExtended::setVar('citations', array());
        self::$notes_ordered = array();
        self::$written_notes = array();
        self::$footnote_counter = 1;
        self::$notes_counter = 0;
    }

    /**
     * Strips link definitions from text, stores the URLs and titles in hash references.
     *
     * @param string $text
     * @return string
     */
    public function strip($text) 
    {
        $less_than_tab = MarkdownExtended::getConfig('less_than_tab');

        // Link defs are in the form: [^id]: url "optional title"
        $text = preg_replace_callback('{
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
            array($this, '_strip_callback'),
            $text);

        // Link defs are in the form: [#id]: url "optional title"
        $text = preg_replace_callback('{
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
            array($this, '_strip_callback'),
            $text);

        return $text;
    }

    /**
     * Build the footnote and strip it from content
     *
     * @param array $matches Results from the `transform()` function
     * @return string
     */
    protected function _strip_callback($matches) 
    {
        if (0 != preg_match('/^(<p>)?glossary:/i', $matches[2])) {
            MarkdownExtended::addVar('glossaries', array(
                (MarkdownExtended::getConfig('fng_id_prefix') . $matches[1]) =>
                    parent::runGamut('tool:Outdent', $matches[2])
            ));

        } elseif (0 != preg_match('/^\#(.*)?/i', $matches[1])) {
            MarkdownExtended::addVar('citations', array(
                (MarkdownExtended::getConfig('fnc_id_prefix') . substr($matches[1],1)) =>
                    parent::runGamut('tool:Outdent', $matches[2])
            ));

        } else {
            MarkdownExtended::addVar('footnotes', array(
                (MarkdownExtended::getConfig('fn_id_prefix') . $matches[1]) =>
                    parent::runGamut('tool:Outdent', $matches[2])
            ));
        }
        return '';
    }

    /**
     * Replace footnote references in $text [string][#id] and [^id] with a special text-token 
     * which will be replaced by the actual footnote marker in appendFootnotes.
     *
     * @param string $text
     * @return string
     */
    public function transform($text) 
    {
        if (MarkdownExtended::getVar('in_anchor')==false) {
            $text = preg_replace('{\[\^(.+?)\]}', "F\x1Afn:\\1\x1A:", $text);
            $text = preg_replace('{\[(.+?)\]\[\#(.+?)\]}', " [\\1, F\x1Afn:\\2\x1A:]", $text);
        }
        return $text;
    }

    /**
     * Append footnote list to text.
     *
     * @param string $text
     * @return string
     */
    public function append($text) 
    {
        $footnotes = MarkdownExtended::getVar('footnotes');
        $glossaries = MarkdownExtended::getVar('glossaries');
        $citations = MarkdownExtended::getVar('citations');

        // First loop for references
        if (!empty(self::$notes_ordered)) {
            $tmp_notes_ordered = self::$notes_ordered;
            $_counter=0;
            while (!empty($tmp_notes_ordered)) 
            {
                $note_id = key($tmp_notes_ordered);
                unset($tmp_notes_ordered[$note_id]);
                if (!array_key_exists($note_id, self::$written_notes))
                    self::$written_notes[$note_id] = $_counter++;
            }
        }
    
        $text = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
            array($this, '_append_callback'), $text);
    
        if (!empty(self::$notes_ordered)) {
            $notes_content = '';
            while (!empty(self::$notes_ordered)) {
                $note = reset(self::$notes_ordered);
                $note_id = key(self::$notes_ordered);
                unset(self::$notes_ordered[$note_id]);
                if (isset($footnotes[$note_id])) {
                    // footnotes
                    $notes_content .= self::transformFootnote($note_id);
                } elseif (isset($glossaries[$note_id])) {
                    // glossary
                    $notes_content .= self::transformGlossary($note_id);
                } elseif (isset($citations[$note_id])) {
                    // citations
                    $notes_content .= self::transformCitation($note_id);
                }
            }
            $notes_text = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('ordered_list', $notes_content);
            $notes_text = "\n\n" . MarkdownExtended::get('OutputFormatBag')
                ->buildTag('block', $notes_text, array('class'=>'footnotes')) . "\n\n";
            MarkdownExtended::getContent()
                ->setNotesHtml($notes_text);
        }
        return $text;
    }

    /**
     * Append footnote list to text.
     *
     * @param string $note_id
     * @return string
     */
    public function transformFootnote($note_id) 
    {
        $text='';
        $footnotes = MarkdownExtended::getVar('footnotes');
        if (!empty($footnotes[$note_id])) {
            $text = $this->_doTransformNote($note_id, $footnotes[$note_id]);
        }
        return $text;
    }

    /**
     * Append glossary notes list to text.
     *
     * @param string $note_id
     * @return string
     */
    public function transformGlossary($note_id) 
    {
        $text='';
        $glossaries = MarkdownExtended::getVar('glossaries');
        if (!empty($glossaries[$note_id])) {
            $glossary = substr($glossaries[$note_id], strlen('glossary:'));
            $glossary = preg_replace_callback('{
                    ^(.*?)                          # $1 = term
                    \s*
                    (?:\(([^\(\)]*)\)[^\n]*)?       # $2 = optional sort key
                    \n{1,}
                    (.*?)
                    }x',
                    array($this, '_glossary_callback'), $glossary);
            $text = $this->_doTransformNote($note_id, $glossary, 'glossary', 'fng');
        }
        return $text;
    }

    /**
     * Append bibliography notes list to text.
     *
     * @param string $note_id
     * @return string
     */
    public function transformCitation($note_id) 
    {
        $text='';
        $citations = MarkdownExtended::getVar('citations');
        if (!empty($citations[$note_id])) {
            $citation = $citations[$note_id];
            $citation = preg_replace_callback('{
                    ^\#(.*?)                        # $1 = term
                    \s*
                    (?:\(([^\(\)]*)\)[^\n]*)?       # $2 = optional sort key
                    \n{1,}
                    (.*?)
                    }x',
                    array($this, '_citation_callback'), $citation);
            $text = $this->_doTransformNote($note_id, $citation, 'citation', 'fnc', 'bibliography');
        }
        return $text;
    }

    /**
     * Do a footnote transformation
     *
     * @param string $note_id
     * @param string $note_content
     * @param string $type
     * @param string $prefix
     * @param string $rev
     *
     * @return string
     */
    protected function _doTransformNote($note_id, $note_content, $type = 'footnote', $prefix = 'fn', $rev = null) 
    {
        $text='';
        if (!empty($note_content)) {
            ++self::$notes_counter;
            $attributes = array();
            $attributes['rev'] = !is_null($rev) ? $rev : $type;
            if (MarkdownExtended::getConfig($prefix . '_backlink_class') != '') {
                $attributes['class'] = 
                    MDE_Helper::fillPlaceholders(
                        parent::runGamut('tool:EncodeAttribute', MarkdownExtended::getConfig($prefix . '_backlink_class')),
                        self::$notes_counter
                    );
            }
            if (MarkdownExtended::getConfig($prefix . '_backlink_title') != '') {
                $attributes['title'] =
                    MDE_Helper::fillPlaceholders(
                        parent::runGamut('tool:EncodeAttribute', MarkdownExtended::getConfig($prefix . '_backlink_title')),
                        self::$notes_counter
                    );
            }
            
            $note_content .= "\n"; // Need to append newline before parsing.
            $note_content = parent::runGamut('html_block_gamut', "$note_content\n");                
            $note_content = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
                    array($this, '_append_callback'), $note_content);

            $note_id = parent::runGamut('tool:EncodeAttribute', $note_id);
                
            // Add backlink to last paragraph; create new paragraph if needed.
            $backlink_id = MarkdownExtended::getContent()
                ->getDomId($prefix . 'ref:' . $note_id);
            $attributes['href'] = '#' . $backlink_id;
            $backlink = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('link', '&#8617;', $attributes);
            if (preg_match('{</p>$}', $note_content)) {
                $note_content = substr($note_content, 0, -4) . "&#160;$backlink" . substr($note_content, -4);
            } else {
                $note_content .= "\n\n" . MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('paragraph', $backlink);
            }
                
            $footlink_id = MarkdownExtended::getContent()
                ->getDomId($prefix . ':' . $note_id);
            $note = array(
                'type'=>$type,
                'in-text-id'=>$backlink_id,
                'note-id'=>$footlink_id,
                'text'=>$note_content
            );
            $_meth = 'add'.ucfirst($type);
            MarkdownExtended::getContent()
                ->{$_meth}($note, $note_id)
                ->addNote($note, $note_id);
            $text = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('list_item', $note_content, array('id' => $footlink_id)) . "\n\n";
        }
        return $text;
    }

    /**
     * Build the glossary entry
     *
     * @param array $matches
     * @return string
     */
    protected function _glossary_callback($matches)
    {
        $text = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('span', trim($matches[1]), array('class' => 'glossary name'));
        if (isset($matches[3])) {
            $text .= MarkdownExtended::get('OutputFormatBag')
                ->buildTag('span', $matches[2], array('class' => 'glossary sort', 'style'=>'display:none'));
        }
        return $text . "\n\n" . (isset($matches[3]) ? $matches[3] : $matches[2]);
    }

    /**
     * Build the citation entry
     *
     * @param array $matches
     * @return string
     */
    protected function _citation_callback($matches)
    {
        $text = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('span', trim($matches[1]), array('class' => 'bibliography name'));
        return $text . "\n\n" . $matches[2];
    }

    /**
     * Append footnote and glossary list to text.
     *
     * @param array $matches
     * @return string
     */
    protected function _append_callback($matches) 
    {
        $note_id = $matches[1];
        $note_num = $note_ref = null;
        $note_rev = $note_prefix = null;

        // Create footnote marker only if it has a corresponding footnote *and*
        // the footnote hasn't been used by another marker.
        $node_id = MarkdownExtended::getConfig('fn_id_prefix') . $note_id;
        $footnotes = MarkdownExtended::getVar('footnotes');
        if (isset($footnotes[$node_id])) {
            // Transfer footnote content to the ordered list.
            self::$notes_ordered[$node_id] = $footnotes[$node_id];
            $note_num = array_key_exists($node_id, self::$written_notes) ?
                self::$written_notes[$node_id] : self::$footnote_counter++;
            $note_ref = $node_id;
            $note_rev = 'footnote';
            $note_prefix = 'fn';
        }
        
        // Create glossary marker only if it has a corresponding note *and*
        // the glossary hasn't been used by another marker.
        $glossary_node_id = MarkdownExtended::getConfig('fng_id_prefix') . $note_id;
        $glossaries = MarkdownExtended::getVar('glossaries');
        if (isset($glossaries[$glossary_node_id])) {
            // Transfer footnote content to the ordered list.
            self::$notes_ordered[$glossary_node_id] = $glossaries[$glossary_node_id];
            $note_num = array_key_exists($matches[1], self::$written_notes) ?
                self::$written_notes[$matches[1]] : self::$footnote_counter++;
            $note_ref = $glossary_node_id;
            $note_rev = 'glossary';
            $note_prefix = 'fng';
        }

        // Create citation marker only if it has a corresponding note *and*
        // the glossary hasn't been used by another marker.
        $citation_node_id = MarkdownExtended::getConfig('fnc_id_prefix') . $note_id;
        $citations = MarkdownExtended::getVar('citations');
        if (isset($citations[$citation_node_id])) {
            // Transfer footnote content to the ordered list.
            self::$notes_ordered[$citation_node_id] = $citations[$citation_node_id];
            $note_num = array_key_exists($matches[1], self::$written_notes) ?
                self::$written_notes[$matches[1]] : self::$footnote_counter++;
            $note_ref = $citation_node_id;
            $note_rev = 'bibliography';
            $note_prefix = 'fnc';
        }

        if (!empty($note_id) && !empty($note_num) && !empty($note_ref)) {
            $attributes = array();
            $attributes['rel'] = $note_rev;
            if (MarkdownExtended::getConfig($note_prefix . '_link_class') != '') {
                $attributes['class'] =
                    MDE_Helper::fillPlaceholders(
                        parent::runGamut('tool:EncodeAttribute', MarkdownExtended::getConfig($note_prefix . '_link_class')),
                        $note_num);
            }
            if (MarkdownExtended::getConfig($note_prefix . '_link_title') != '') {
                $attributes['title'] = 
                    MDE_Helper::fillPlaceholders(
                        parent::runGamut('tool:EncodeAttribute', MarkdownExtended::getConfig($note_prefix . '_link_title')),
                        $note_num);
            }
            $note_id = parent::runGamut('tool:EncodeAttribute', $note_id);
            $backlink_id = MarkdownExtended::getContent()->getDomId($note_prefix . 'ref:' . $note_ref);
            $footlink_id = MarkdownExtended::getContent()->getDomId($note_prefix . ':' . $note_ref);
            $attributes['href'] = '#' . $footlink_id;
            $link = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('link', $note_num, $attributes);
            return MarkdownExtended::get('OutputFormatBag')
                ->buildTag('sup', $link, array('id'=>$backlink_id));
        }

        return "[^".$matches[1]."]";
    }
        
}

// Endfile
