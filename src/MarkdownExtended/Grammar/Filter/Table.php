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

use \MarkdownExtended\MarkdownExtended,
    \MarkdownExtended\Grammar\Filter;

class Table extends Filter
{
	
	/**
	 * Form HTML tables.
	 * 
	 * Find tables with leading pipe:
	 * 
	 *    | Header 1 | Header 2
	 *    | -------- | --------
	 *    | Cell 1   | Cell 2
	 *    | Cell 3   | Cell 4
	 * 
	 * Or without:
	 * 
	 *    Header 1 | Header 2
	 *    -------- | --------
	 *    Cell 1   | Cell 2
	 *    Cell 3   | Cell 4
	 * 
	 * @param string $text Text to parse
	 * @return string Text with table parsed
	 * @see _doTable_leadingPipe_callback()
	 * @see _DoTable_callback()
	 */
	public function transform($text) 
	{
		$less_than_tab = MarkdownExtended::getConfig('tab_width') - 1;

		// Find tables with leading pipe.
		$text = preg_replace_callback('
			{
				^							                # Start of a line
				(                             # A caption between brackets (optional)
					[ ]{0,'.$less_than_tab.'}
					\[.*?\][ \t]*\n
				)?
				[ ]{0,'.$less_than_tab.'}	    # Allowed whitespace.
				(
					(?>
						[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
						[|]							          # Optional leading pipe (present)
						.* [|] .* \n
					)*
				) 				                    # $1: Header rows (at least one pipe)

				[ ]{0,'.$less_than_tab.'}	    # Allowed whitespace.
				[|] ([ ]*[-:]+[-| :]*) \n	    # $2: Header underline
				
				(       							        # $3: Cells
					(?>
						[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
						[|] .* \n                 # Row content
					)*
				)
				(?=\n|\Z)					            # Stop at final double newline.
			}xm',
			array($this, '_callback'), $text);
		
		// Find tables without leading pipe.
		$text = preg_replace_callback('
			{
				^							                # Start of a line
				(                             # A caption between brackets (optional)
					[ ]{0,'.$less_than_tab.'}
					\[.*?\][ \t]*\n
				)?
				[ ]{0,'.$less_than_tab.'}	    # Allowed whitespace.
				(
					(?>
						[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
						\S .* [|] .* \n
					)*
				) 				                    # $1: Header rows (at least one pipe)
				
				^[ ]{0,'.$less_than_tab.'}	  # Allowed whitespace at the beginning
				([-:]+[ ]*[|][-| :]*) \n	    # $2: Header underline
				
				(       							        # $3: Cells
					(?>
						[ ]{0,'.$less_than_tab.'}	# Allowed whitespace.
						 .* [|] .* \n		          # Row content
					)*
				)
				(?=\n|\Z)					            # Stop at final double newline.
			}xm',
			array($this, '_callback'), $text);

		return $text;
	}

	/**
	 * Form HTML tables: removes leading pipe for each row
	 * 
	 * @param array $matches Results from the `doTables()` function
	 * @return function Pass its result to the `_doTable_callback()` function
	 * @see doTable()
	 * @see _DoTable_callback()
	 */
	protected function _leadingPipe_callback($matches) 
	{
		$head		    = $matches[1];
		$underline	    = $matches[2];
		$content	    = $matches[3];
		$content	    = preg_replace('/^ *[|]/m', '', $content);
		return self::_callback(array($matches[0], $head, $underline, $content));
	}

	/**
	 * Form HTML tables: parses table contents
	 * 
	 * @param array $matches Results from the `doTables()` function
	 * @return function Pass its result to the `hashBlock()` function
	 * @see doTable()
	 * @see hashBlock()
	 * @see span_gamut()
	 * @see parseSpan()
	 */
	protected function _callback($matches) 
	{
//self::doDebug($matches);
		// The head string may have a begin slash
		$caption    = count($matches)>3 ? $matches[1] : null;
		$head		    = count($matches)>3 ? preg_replace('/^ *[|]/m', '', $matches[2]) : preg_replace('/^ *[|]/m', '', $matches[1]);
		$underline	= count($matches)>3 ? $matches[3] : $matches[2];
		$content	  = count($matches)>3 ? preg_replace('/^ *[|]/m', '', $matches[4]) : preg_replace('/^ *[|]/m', '', $matches[3]);

		// Remove any tailing pipes for each line.
		$underline	= preg_replace('/[|] *$/m', '', $underline);
		$content	  = preg_replace('/[|] *$/m', '', $content);
		
		// Reading alignement from header underline.
		$separators	= preg_split('/ *[|] */', $underline);
		foreach ($separators as $n => $s) {
			if (preg_match('/^ *-+: *$/', $s))
				$attr[$n] = ' style="text-align:right;"';
			else if (preg_match('/^ *:-+: *$/', $s))
				$attr[$n] = ' style="text-align:center;"';
			else if (preg_match('/^ *:-+ *$/', $s))
				$attr[$n] = ' style="text-align:left;"';
			else
				$attr[$n] = '';
		}
		
		// Split content by row.
		$headers = explode("\n", trim($head, "\n"));

		$text = "<table>\n";
		if (!empty($caption)) {
			$table_id = parent::runGamut('tool:Header2Label', $caption );
			$text .= preg_replace('/\[(.*)\]/', "<caption id=\"$table_id\">\$1</caption>\n", parent::runGamut('span_gamut', $caption) );
		}

		$text .= "<thead>\n";
		foreach ($headers as $_header) {
			// Parsing span elements, including code spans, character escapes, 
			// and inline HTML tags, so that pipes inside those gets ignored.
			$_header	= parent::runGamut('filter:Span', $_header);

			// Split row by cell.
			$_header    = preg_replace('/[|] *$/m', '', $_header);
			$_headers	= preg_split('/[|]/', $_header);
			$col_count	= count($_headers);

			// Write column headers.
			$text .= "<tr>\n";
			// we first loop for colspans
			$headspans = array();
			foreach ($_headers as $_i => $_cell) {
				if ($_cell=='') {
					if ($_i==0) $headspans[1]=2;
					else {
						if (isset($headspans[$_i-1])) $headspans[$_i-1]++;
						else $headspans[$_i-1]=2;
					}
				}
			}
			foreach ($_headers as $n => $__header) {
				if ($__header!='')
					$text .= "  <th".(isset($headspans[$n]) ? " colspan=\"$headspans[$n]\"" : '')."$attr[$n]>"
						.parent::runGamut('span_gamut', trim($__header))."</th>\n";
			}
			$text .= "</tr>\n";
		}
		$text .= "</thead>\n";
		
		// Split content by row.
		$rows = explode("\n", trim($content, "\n"));
		
		$text .= "<tbody>\n";
		foreach ($rows as $row) {
			// Parsing span elements, including code spans, character escapes, 
			// and inline HTML tags, so that pipes inside those gets ignored.
			$row = parent::runGamut('filter:Span', $row);
			
			// Split row by cell.
			$row_cells = preg_split('/ *[|] */', $row, $col_count);
			$row_cells = array_pad($row_cells, $col_count, '');
			
			$text .= "<tr>\n";
			// we first loop for colspans
			$colspans = array();
			foreach ($row_cells as $_i => $_cell) {
				if ($_cell=='') {
					if ($_i==0) $colspans[1]=2;
					else {
						if (isset($colspans[$_i-1])) $colspans[$_i-1]++;
						else $colspans[$_i-1]=2;
					}
				}
			}
			foreach ($row_cells as $n => $cell) {
				if ($cell!='')
					$text .= "  <td".(isset($colspans[$n]) ? " colspan=\"$colspans[$n]\"" : '')."$attr[$n]>"
						.parent::runGamut('span_gamut', trim($cell))."</td>\n";
			}
			$text .= "</tr>\n";
		}
		$text .= "</tbody>\n</table>";
		
		return parent::hashBlock($text) . "\n";
	}

}

// Endfile