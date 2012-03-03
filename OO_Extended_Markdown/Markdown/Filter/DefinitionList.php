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
class Markdown_Filter_DefinitionList extends Markdown_Filter
{

	/**
	 * Form HTML definition lists.
	 *
	 * @param string $text The text to parse
	 * @return string The text parsed
	 * @see _doDefLists_callback()
	 */
	public function transform($text) 
	{
		$less_than_tab = Markdown_Extended::getConfig('tab_width') - 1;

		// Re-usable pattern to match any entire dl list:
		$whole_list_re = '(?>
			(								                    # $1 = whole list
			  (								                  # $2
				[ ]{0,'.$less_than_tab.'}
				((?>.*\S.*\n)+)				            # $3 = defined term
				\n?
				[ ]{0,'.$less_than_tab.'}:[ ]+    # colon starting definition
			  )
			  (?s:.+?)
			  (								                  # $4
				  \z
				|
				  \n{2,}
				  (?=\S)
				  (?!						                 # Negative lookahead for another term
					[ ]{0,'.$less_than_tab.'}
					(?: \S.*\n )+?			           # defined term
					\n?
					[ ]{0,'.$less_than_tab.'}:[ ]+ # colon starting definition
				  )
				  (?!						                 # Negative lookahead for another definition
					[ ]{0,'.$less_than_tab.'}:[ ]+ # colon starting definition
				  )
			  )
			)
		)'; // mx

		return preg_replace_callback('{
				(?>\A\n?|(?<=\n\n))
				'.$whole_list_re.'
			}mx',
			array(&$this, '_callback'), $text);
	}

	/**
	 * Turn double returns into triple returns, so that we can make a
	 * paragraph for the last item in a list, if necessary
	 *
	 * @param array $matches The results form the doDefLists()` `preg_replace_callback()` command
	 * @return function Pass its result to the `hashBlock()` function
	 * @see hashBlock()
	 * @see doDefLists()
	 * @see processDefListItems()
	 */
	protected function _callback($matches) 
	{
		// Re-usable patterns to match list item bullets and number markers:
		$list = $matches[1];
		$result = trim(self::transformItems($list));
		$result = "<dl>\n" . $result . "\n</dl>";
		return parent::hashBlock($result) . "\n\n";
	}


	/**
	 * Process the contents of a single definition list, splitting it
	 * into individual term and definition list items.
	 *
	 * @param string $list_str The result string form the _doDefLists_callback()` function
	 * @return string Parsed list string
	 * @see _doDefLists_callback()
	 * @see _processDefListItems_callback_dt()
	 * @see _processDefListItems_callback_dd()
	 */
	public function transformItems($list_str) 
	{
		$less_than_tab = Markdown_Extended::getConfig('tab_width') - 1;
		
		// trim trailing blank lines:
		$list_str = preg_replace("/\n{2,}\\z/", "\n", $list_str);

		// Process definition terms.
		$list_str = preg_replace_callback('{
			(?>\A\n?|\n\n+)					    # leading line
			(								            # definition terms = $1
				[ ]{0,'.$less_than_tab.'}	# leading whitespace
				(?![:][ ]|[ ])				    # negative lookahead for a definition 
											            # mark (colon) or more whitespace.
				(?> \S.* \n)+?				    # actual term (not whitespace).	
			)			
			(?=\n?[ ]{0,3}:[ ])				  # lookahead for following line feed 
											            # with a definition mark.
			}xm',
			array(&$this, '_item_callback_dt'), $list_str);

		// Process actual definitions.
		$list_str = preg_replace_callback('{
			\n(\n+)?						        # leading line = $1
			(								            # marker space = $2
				[ ]{0,'.$less_than_tab.'}	# whitespace before colon
				[:][ ]+						        # definition mark (colon)
			)
			((?s:.+?))					 	      # definition text = $3
			(?= \n+ 						        # stop at next definition mark,
				(?:							          # next term or end of text
					[ ]{0,'.$less_than_tab.'} [:][ ]	|
					<dt> | \z
				)						
			)					
			}xm',
			array(&$this, '_item_callback_dd'), $list_str);

		return $list_str;
	}

	/**
	 * Process the dt contents.
	 *
	 * @param array $matches The results form the `processDefListItems()` function
	 * @return string Parsed dt string
	 * @see processDefListItems()
	 * @see span_gamut()
	 */
	protected function _item_callback_dt($matches) 
	{
		$terms = explode("\n", trim($matches[1]));
		$text = '';
		foreach ($terms as $term) {
			$term = parent::runGamut('span_gamut', trim($term));
			$text .= "\n<dt>" . $term . "</dt>";
		}
		return $text . "\n";
	}

	/**
	 * Process the dd contents.
	 *
	 * @param array $matches The results form the `processDefListItems()` function
	 * @return string Parsed dd string
	 * @see processDefListItems()
	 * @see span_gamut()
	 */
	protected function _item_callback_dd($matches) 
	{
		$leading_line	= $matches[1];
		$marker_space	= $matches[2];
		$def			    = $matches[3];

		if ($leading_line || preg_match('/\n{2,}/', $def)) {
			// Replace marker with the appropriate whitespace indentation
			$def = str_repeat(' ', strlen($marker_space)) . $def;
			$def = parent::runGamut('html_block_gamut', parent::runGamut('tool:outdent', $def . "\n\n"));
			$def = "\n". $def ."\n";
		}
		else {
			$def = rtrim($def);
			$def = parent::runGamut('span_gamut', parent::runGamut('tool:outdent', $def));
		}

		return "\n<dd>" . $def . "</dd>\n";
	}

}

// Endfile