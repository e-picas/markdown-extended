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
 * Process Markdown horizontal rules
 */
class HorizontalRule extends Filter
{
	
	/**
	 * @param string $text
	 * @return string
	 */
	public function transform($text) 
	{
		return preg_replace(
			'{
				^[ ]{0,3}	    # Leading space
				([-*_])		    # $1: First marker
				(?>			    # Repeated marker group
					[ ]{0,2}	# Zero, one, or two spaces.
					\1			# Marker character
				){2,}		    # Group repeated at least twice
				[ ]*		    # Tailing spaces
				$			    # End of line.
			}mx',
			"\n".parent::hashBlock(MarkdownExtended::get('OutputFormatBag')->buildTag('horizontal_rule'))."\n", 
			$text);
	}

}

// Endfile