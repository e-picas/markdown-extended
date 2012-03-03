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
class Markdown_Filter_Span extends Markdown_Filter
{
	
	/**
	 * Take the string $str and parse it into tokens, hashing embeded HTML,
	 * escaped characters and handling code spans.
	 *
	 * @param string $str The text to be parsed
	 * @return string The text parsed
	 * @see handleSpanToken()
	 */
	function transform($str) 
	{
		$output = '';
		
		$span_re = '{
				(
					\\\\'.Markdown_Extended::getConfig('escape_chars_re').'
				|
					(?<![`\\\\])
					`+						          # code span marker
			'.( Markdown_Extended::getConfig('no_markup') ? '' : '
				|
					<!--    .*?     -->		  # comment
				|
					<\?.*?\?> | <%.*?%>		  # processing instruction
				|
					<[/!$]?[-a-zA-Z0-9:_]+	# regular tags
					(?>
						\s
						(?>[^"\'>]+|"[^"]*"|\'[^\']*\')*
					)?
					>
			').'
				)
				}xs';

		while (1) {

			// Each loop iteration seach for either the next tag, the next 
			// openning code span marker, or the next escaped character. 
			// Each token is then passed to handleSpanToken.
			$parts = preg_split($span_re, $str, 2, PREG_SPLIT_DELIM_CAPTURE);
			
			// Create token from text preceding tag.
			if ($parts[0] != "") {
				$output .= $parts[0];
			}
			
			// Check if we reach the end.
			if (isset($parts[1])) {
				$output .= self::handleSpanToken($parts[1], $parts[2]);
				$str = $parts[2];
			}
			else {
				break;
			}
		}
		
		return $output;
	}
	
	/**
	 * Handle $token provided by parseSpan by determining its nature and 
	 * returning the corresponding value that should replace it.
	 *
	 * @param string $token The token string to use
	 * @param string $str The text to be parsed (by reference)
	 * @return string The text parsed
	 * @see hashPart()
	 */
	function handleSpanToken($token, &$str) 
	{
		switch ($token{0}) {
			case "\\":
				return parent::hashPart("&#". ord($token{1}). ";");
			case "`":
				// Search for end marker in remaining text.
				if (preg_match('/^(.*?[^`])'.preg_quote($token).'(?!`)(.*)$/sm', 
					$str, $matches))
				{
					$str = $matches[2];
					$codespan = parent::runGamut('filter:CodeBlock:span', $matches[1]);
					return parent::hashPart($codespan);
				}
				return $token; // return as text since no ending marker found.
			default:
				return parent::hashPart($token);
		}
	}

}

// Endfile