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

class CodeBlock extends Filter
{

	/**
	 *	Process Markdown `<pre><code>` blocks.
	 *
	 * @param string $text Text to parse
	 * @return string The text parsed
	 * @see _doCodeBlocks_callback()
	 */
	public function transform($text) 
	{
		return preg_replace_callback('{
				(?:\n\n|\A\n?)
				(	                                      # $1 = the code block -- one or more lines, starting with a space/tab
				  (?>
					[ ]{'.MarkdownExtended::getConfig('tab_width').'}             # Lines must start with a tab or a tab-width of spaces
					.*\n+
				  )+
				)
				((?=^[ ]{0,'.MarkdownExtended::getConfig('tab_width').'}\S)|\Z)	# Lookahead for non-space at line-start, or end of doc
			}xm',
			array($this, '_callback'), $text);
	}

	/**
	 * Build `<pre><code>` blocks.
	 *
	 * @param array $matches A set of results of the `doCodeBlocks()` function
	 * @return string Text parsed
	 * @see hashBlock()
	 */
	protected function _callback($matches) 
	{
		$codeblock = $matches[1];

		$codeblock = parent::runGamut('tool:outdent', $codeblock);
		$codeblock = htmlspecialchars($codeblock, ENT_NOQUOTES);

		# trim leading newlines and trailing newlines
		$codeblock = preg_replace('/\A\n+|\n+\z/', '', $codeblock);

		$codeblock = "<pre><code>$codeblock\n</code></pre>";
		return "\n\n".parent::hashBlock($codeblock)."\n\n";
	}

	/**
	 * Create a code span markup for $code. Called from handleSpanToken.
	 *
	 * @param string $text Text to parse
	 * @return string The text parsed
	 * @see hashPart()
	 */
	public function span($code) 
	{
		$code = htmlspecialchars(trim($code), ENT_NOQUOTES);
		return parent::hashPart("<code>$code</code>");
	}

}

// Endfile