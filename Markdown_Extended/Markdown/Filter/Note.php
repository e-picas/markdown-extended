<?php
/**
 * PHP Extended Markdown
 * Copyright (c) 2004-2012 Pierre Cassat
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

/**
 *
 */
class Markdown_Filter_Note extends Markdown_Filter
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

	public function _setup()
	{
		Markdown_Extended::setVar('footnotes', array());
		Markdown_Extended::setVar('glossaries', array());
		Markdown_Extended::setVar('citations', array());
		self::$notes_ordered = array();
		self::$written_notes = array();
		self::$footnote_counter = 1;
		self::$notes_counter = 0;
	}

	public function _teardown()
	{
		self::_setup();
	}

	/**
	 * Strips link definitions from text, stores the URLs and titles in hash references.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _stripFootnotes_callback()
	 */
	public function strip($text) 
	{
		$less_than_tab = Markdown_Extended::getConfig('tab_width') - 1;

		// Link defs are in the form: [^id]: url "optional title"
		$text = preg_replace_callback('{
			^[ ]{0,'.$less_than_tab.'}\[\^(.+?)\][ ]?:	# note_id = $1
			  [ ]*
			  \n?					        # maybe *one* newline
			(						          # text = $2 (no blank lines allowed)
				(?:					
					.+				        # actual text
				|
					\n				        # newlines but 
					(?!\[\^.+?\]:\s)  # negative lookahead for footnote marker.
					(?!\n+[ ]{0,3}\S) # ensure line is not blank and followed 
									          # by non-indented content
				)*
			)		
			}xm',
			array(&$this, '_strip_callback'),
			$text);

		// Link defs are in the form: [#id]: url "optional title"
		$text = preg_replace_callback('{
			^[ ]{0,'.$less_than_tab.'}\[(\#.+?)\][ ]?:	# note_id = $1
			  [ ]*
			  \n?					        # maybe *one* newline
			(						          # text = $2 (no blank lines allowed)
				(?:					
					.+				        # actual text
				|
					\n				        # newlines but 
					(?!\[\^.+?\]:\s)  # negative lookahead for footnote marker.
					(?!\n+[ ]{0,3}\S) # ensure line is not blank and followed 
									          # by non-indented content
				)*
			)		
			}xm',
			array(&$this, '_strip_callback'),
			$text);

		return $text;
	}

	/**
	 * Build the footnote and strip it from content
	 *
	 * @param array $matches Results from the `stripFootnotes()` function
	 * @return string The text parsed
	 * @see outdent()
	 */
	protected function _strip_callback($matches) 
	{
		if (0 != preg_match('/^(<p>)?glossary:/i', $matches[2])) {
			Markdown_Extended::addVar('glossaries', array(
				(Markdown_Extended::getConfig('fng_id_prefix') . $matches[1]) => parent::runGamut('tool:outdent', $matches[2])
			));

		} elseif (0 != preg_match('/^\#(.*)?/i', $matches[1])) {
			Markdown_Extended::addVar('citations', array(
				(Markdown_Extended::getConfig('fnc_id_prefix') . substr($matches[1],1)) => parent::runGamut('tool:outdent', $matches[2])
			));
		} else {
			Markdown_Extended::addVar('footnotes', array(
				(Markdown_Extended::getConfig('fn_id_prefix') . $matches[1]) => parent::runGamut('tool:outdent', $matches[2])
			));
		}
		return ''; // String that will replace the block
	}

	/**
	 * Replace footnote references in $text [^id] with a special text-token 
	 * which will be replaced by the actual footnote marker in appendFootnotes.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 */
	public function transform($text) 
	{
		if (Markdown_Extended::getVar('in_anchor')==false) {
			$text = preg_replace('{\[\^(.+?)\]}', "F\x1Afn:\\1\x1A:", $text);
			$text = preg_replace('{\[\#(.+?)\]}', "F\x1Afn:\\1\x1A:", $text);
		}
		return $text;
	}

	/**
	 * Append footnote list to text.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _appendFootnotes_callback()
	 * @see encodeAttribute()
	 * @see html_block_gamut()
	 */
	public function append($text) 
	{
		$footnotes = Markdown_Extended::getVar('footnotes');
		$glossaries = Markdown_Extended::getVar('glossaries');
		$citations = Markdown_Extended::getVar('citations');

		// First loop for references
		if (!empty(self::$notes_ordered)) 
		{
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
			array(&$this, '_append_callback'), $text);
	
		if (!empty(self::$notes_ordered)) 
		{
			$text .= "\n\n" . "<div class=\"footnotes\">\n"
				. "<hr". Markdown_Extended::getConfig('empty_element_suffix') ."\n" . "<ol>\n\n";

			while (!empty(self::$notes_ordered)) 
			{
				$note = reset(self::$notes_ordered);
				$note_id = key(self::$notes_ordered);
				unset(self::$notes_ordered[$note_id]);

				// footnotes
				if (isset($footnotes[$note_id]))
					$text .= self::transformFootnote( $note_id );

				// glossary
				elseif (isset($glossaries[$note_id]))
					$text .= self::transformGlossary( $note_id );

				// citations
				elseif (isset($citations[$note_id]))
					$text .= self::transformCitation( $note_id );
			}

			$text .= "</ol>\n" . "</div>";
		}
		return $text;
	}

	/**
	 * Append footnote list to text.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _appendFootnotes_callback()
	 * @see encodeAttribute()
	 * @see html_block_gamut()
	 */
	public function transformFootnote($note_id) 
	{
		$text='';
		$footnotes = Markdown_Extended::getVar('footnotes');
		if (!empty($footnotes[$note_id])) 
		{
			$footnote = $footnotes[$note_id];
			$attr = " rev=\"footnote\"";
			if (Markdown_Extended::getConfig('fn_backlink_class') != "")
				$attr .= " class=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fn_backlink_class') )."\"";
			if (Markdown_Extended::getConfig('fn_backlink_title') != "")
				$attr .= " title=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fn_backlink_title') )."\"";
			
			$footnote .= "\n"; // Need to append newline before parsing.
			$footnote = parent::runGamut('html_block_gamut', "$footnote\n");				
			$footnote = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
					array(&$this, '_append_callback'), $footnote);
				
			$attr = str_replace("%%", ++self::$notes_counter, $attr);
			self::$written_notes[$note_id] = self::$notes_counter;
			$note_id = parent::runGamut('tool:EncodeAttribute', $note_id);
				
			// Add backlink to last paragraph; create new paragraph if needed.
			$backlink = "<a href=\"#fnref:$note_id\"$attr>&#8617;</a>";
			if (preg_match('{</p>$}', $footnote)) {
				$footnote = substr($footnote, 0, -4) . "&#160;$backlink</p>";
			} else {
				$footnote .= "\n\n<p>$backlink</p>";
			}
				
			$text = "<li id=\"fn:$note_id\">\n" . $footnote . "\n" . "</li>\n\n";
		}
		return $text;
	}

	/**
	 * Append glossary notes list to text.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _appendGlossaries_callback()
	 * @see encodeAttribute()
	 * @see html_block_gamut()
	 */
	public function transformGlossary($note_id) 
	{
		$text='';
		$glossaries = Markdown_Extended::getVar('glossaries');
		if (!empty($glossaries[$note_id])) 
		{
			$glossary = substr( $glossaries[$note_id], strlen('glossary:') );				
			$attr = " rev=\"glossary\"";
			if (Markdown_Extended::getConfig('fng_backlink_class') != "")
				$attr .= " class=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fng_backlink_class') )."\"";
			if (Markdown_Extended::getConfig('fng_backlink_title') != "")
				$attr .= " title=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fng_backlink_title') )."\"";
			
			$glossary = preg_replace_callback('{
					^(.*?)				              # $1 = term
					\s*
					(?:\(([^\(\)]*)\)[^\n]*)?		# $2 = optional sort key
					\n{1,}
					(.*?)
					}x',
					array(&$this, '_glossary_callback'), $glossary);
			$glossary .= "\n"; // Need to append newline before parsing.
			$glossary = parent::runGamut('html_block_gamut', "$glossary\n");				
			$glossary = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
					array(&$this, '_append_callback'), $glossary);
				
			$attr = str_replace("%%", ++self::$notes_counter, $attr);
			self::$written_notes[$note_id] = self::$notes_counter;
			$note_id = parent::runGamut('tool:EncodeAttribute', $note_id);
				
			// Add backlink to last paragraph; create new paragraph if needed.
			$backlink = "<a href=\"#fngref:$note_id\"$attr>&#8617;</a>";
			if (preg_match('{</p>$}', $glossary)) {
				$glossary = substr($glossary, 0, -4) . "&#160;$backlink</p>";
			} else {
				$glossary .= "\n\n<p>$backlink</p>";
			}
				
			$text = "<li id=\"fng:$note_id\">\n" . $glossary . "\n" . "</li>\n\n";
		}
		return $text;
	}

	/**
	 * Append bibliography notes list to text.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _appendGlossaries_callback()
	 * @see encodeAttribute()
	 * @see html_block_gamut()
	 */
	public function transformCitation($note_id) 
	{
		$text='';
		$citations = Markdown_Extended::getVar('citations');
		if (!empty($citations[$note_id])) 
		{
			$citation = $citations[$note_id];
			$attr = " rev=\"bibliography\"";
			if (Markdown_Extended::getConfig('fnc_backlink_class') != "")
				$attr .= " class=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fnc_backlink_class') )."\"";
			if (Markdown_Extended::getConfig('fnc_backlink_title') != "")
				$attr .= " title=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fnc_backlink_title') )."\"";
			
			$citation = preg_replace_callback('{
					^\#(.*?)				              # $1 = term
					\s*
					(?:\(([^\(\)]*)\)[^\n]*)?		# $2 = optional sort key
					\n{1,}
					(.*?)
					}x',
					array(&$this, '_citation_callback'), $citation);
			$citation .= "\n"; // Need to append newline before parsing.
			$citation = parent::runGamut('html_block_gamut', "$citation\n");				
			$citation = preg_replace_callback('{F\x1Afn:(.*?)\x1A:}', 
					array(&$this, '_append_callback'), $citation);
				
			$attr = str_replace("%%", ++self::$notes_counter, $attr);
			self::$written_notes[$note_id] = self::$notes_counter;
			$note_id = parent::runGamut('tool:EncodeAttribute', $note_id);
				
			// Add backlink to last paragraph; create new paragraph if needed.
			$backlink = "<a href=\"#fncref:$note_id\"$attr>&#8617;</a>";
			if (preg_match('{</p>$}', $citation)) {
				$citation = substr($citation, 0, -4) . "&#160;$backlink</p>";
			} else {
				$citation .= "\n\n<p>$backlink</p>";
			}
				
			$text = "<li id=\"fnc:$note_id\">\n" . $citation . "\n" . "</li>\n\n";
		}
		return $text;
	}

	/**
	 * Build the glossary entry
	 *
	 * @param array $matches Results form the `appendGlossaries` function
	 * @return string The text parsed
	 */
	protected function _glossary_callback($matches)
	{
		return 
			"<span class=\"glossary name\">".trim($matches[1])."</span>"
			.(isset($matches[3]) ? "<span class=\"glossary sort\" style=\"display:none\">$matches[2]</span>" : '')
			."\n\n"
			.(isset($matches[3]) ? $matches[3] : $matches[2]);
	}

	/**
	 * Build the citation entry
	 *
	 * @param array $matches Results form the `appendGlossaries` function
	 * @return string The text parsed
	 */
	protected function _citation_callback($matches)
	{
		return 
			"<span class=\"bibliography name\">".trim($matches[1])."</span>"."\n\n".$matches[2];
	}

	/**
	 * Append footnote and glossary list to text.
	 *
	 * @param array $matches Results form the `appendFootnotes()` or `appendGlossaries` functions
	 * @return string The text parsed
	 * @see encodeAttribute()
	 */
	protected function _append_callback($matches) 
	{
		$footnotes = Markdown_Extended::getVar('footnotes');
		$glossaries = Markdown_Extended::getVar('glossaries');
		$citations = Markdown_Extended::getVar('citations');

		// Create footnote marker only if it has a corresponding footnote *and*
		// the footnote hasn't been used by another marker.
		$node_id = Markdown_Extended::getConfig('fn_id_prefix') . $matches[1];
		if (isset($footnotes[$node_id])) {
			// Transfer footnote content to the ordered list.
			self::$notes_ordered[$node_id] = $footnotes[$node_id];
			
			$num = array_key_exists($node_id, self::$written_notes) ?
				self::$written_notes[$node_id] : self::$footnote_counter++;
			$attr = " rel=\"footnote\"";
			if (Markdown_Extended::getConfig('fn_link_class') != "")
				$attr .= " class=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fn_link_class') )."\"";
			if (Markdown_Extended::getConfig('fn_link_title') != "")
				$attr .= " title=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fn_link_title') )."\"";
			$attr = str_replace("%%", $num, $attr);
			$node_id = parent::runGamut('tool:EncodeAttribute', $node_id);
			
			return
				"<sup id=\"fnref:$node_id\">".
				"<a href=\"#fn:$node_id\"$attr>$num</a>".
				"</sup>";
		}
		
		// Create glossary marker only if it has a corresponding note *and*
		// the glossary hasn't been used by another marker.
		$glossary_node_id = Markdown_Extended::getConfig('fng_id_prefix') . $matches[1];
		if (isset($glossaries[$glossary_node_id])) {
			// Transfer footnote content to the ordered list.
			self::$notes_ordered[$glossary_node_id] = $glossaries[$glossary_node_id];
			
			$num = array_key_exists($matches[1], self::$written_notes) ?
				self::$written_notes[$matches[1]] : self::$footnote_counter++;
			$attr = " rel=\"glossary\"";
			if (Markdown_Extended::getConfig('fng_link_class') != "")
				$attr .= " class=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fng_link_class') )."\"";
			if (Markdown_Extended::getConfig('fng_link_title') != "")
				$attr .= " title=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fng_link_title') )."\"";
			$attr = str_replace("%%", $num, $attr);
			$glossary_node_id = parent::runGamut('tool:EncodeAttribute', $glossary_node_id);
			
			return
				"<sup id=\"fngref:$glossary_node_id\">".
				"<a href=\"#fng:$glossary_node_id\"$attr>$num</a>".
				"</sup>";
		}

		// Create citation marker only if it has a corresponding note *and*
		// the glossary hasn't been used by another marker.
		$citation_node_id = Markdown_Extended::getConfig('fnc_id_prefix') . $matches[1];
		if (isset($citations[$citation_node_id])) {
			// Transfer footnote content to the ordered list.
			self::$notes_ordered[$citation_node_id] = $citations[$citation_node_id];
			
			$num = array_key_exists($matches[1], self::$written_notes) ?
				self::$written_notes[$matches[1]] : self::$footnote_counter++;
			$attr = " rel=\"bibliography\"";
			if (Markdown_Extended::getConfig('fnc_link_class') != "")
				$attr .= " class=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fnc_link_class') )."\"";
			if (Markdown_Extended::getConfig('fnc_link_title') != "")
				$attr .= " title=\"".parent::runGamut('tool:EncodeAttribute', Markdown_Extended::getConfig('fnc_link_title') )."\"";
			$attr = str_replace("%%", $num, $attr);
			$citation_node_id = parent::runGamut('tool:EncodeAttribute', $citation_node_id);
			
			return
				"<sup id=\"fncref:$citation_node_id\">".
				"<a href=\"#fnc:$citation_node_id\"$attr>$num</a>".
				"</sup>";
		}

		return "[^".$matches[1]."]";
	}
		
}

// Endfile