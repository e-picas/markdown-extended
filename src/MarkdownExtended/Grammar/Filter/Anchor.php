<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2004-2013 Pierre Cassat
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
 * Process Markdown links
 *
 * Process the links written like:
 *
 * -    reference-style links: `[link text] [id]`
 * -    inline-style links: `[link text](url "optional title")`
 * -    reference-style shortcuts: `[link text]` with a reference
 *
 * Each link attributes will be completed if needed adding it a `title` constructed using
 * the `link_mask_title` config entry, filled with the link URL.
 */
class Anchor extends Filter
{

    /**
     * Set up the `in_anchor` config flag on `false`
     */	
	public function _setup()
	{
		MarkdownExtended::setVar('in_anchor', false);
	}
	
	/**
	 * Turn Markdown link shortcuts into XHTML <a> tags.
	 *
	 * @param string $text
	 * @return string
	 */
	public function transform($text) 
	{
		if (MarkdownExtended::getVar('in_anchor')==true) return $text;
		MarkdownExtended::setVar('in_anchor', true);
		
		// First, handle reference-style links: [link text] [id]
		$text = preg_replace_callback('{
			(					                    # wrap whole match in $1
			  \[
				('.MarkdownExtended::getConfig('nested_brackets_re').')	# link text = $2
			  \]

			  [ ]?				                    # one optional space
			  (?:\n[ ]*)?		                    # one optional newline followed by spaces

			  \[
				(.*?)		                        # id = $3
			  \]
			)
			}xs',
			array($this, '_reference_callback'), $text);

		// Next, inline-style links: [link text](url "optional title")
		$text = preg_replace_callback('{
			(				                                # wrap whole match in $1
			  \[
				('.MarkdownExtended::getConfig('nested_brackets_re').') # link text = $2
			  \]
			  \(			                                # literal paren
				[ \n]*
				(?:
					<(.+?)>	                                # href = $3
				|
					('.MarkdownExtended::getConfig('nested_url_parenthesis_re').') # href = $4
				)
				[ \n]*
				(			                                # $5
				  ([\'"])	                                # quote char = $6
				  (.*?)		                                # Title = $7
				  \6		                                # matching quote
				  [ \n]*	                                # ignore any spaces/tabs between closing quote and )
				)?			                                # title is optional
			  \)
			)
			}xs',
			array($this, '_inline_callback'), $text);

		// Last, handle reference-style shortcuts: [link text]
		// These must come last in case you've also got [link text][1]
		// or [link text](/foo)
		$text = preg_replace_callback('{
			(				    # wrap whole match in $1
			  \[
				([^\[\]]+)		# link text = $2; can\'t contain [ or ]
			  \]
			)
			}xs',
			array($this, '_reference_callback'), $text);

		MarkdownExtended::setVar('in_anchor', false);
		return $text;
	}

	/**
	 * @param array $matches A set of results of the `transform` function
	 * @return string
	 */
	protected function _reference_callback($matches) 
	{
		$whole_match =  $matches[1];
		$link_text   =  $matches[2];
		$link_id     =& $matches[3];

		// for shortcut links like [this][] or [this]
		if (empty($link_id)) {
			$link_id = $link_text;
		}
		
		// lower-case and turn embedded newlines into spaces
		$link_id = preg_replace('{[ ]?\n}', ' ', strtolower($link_id));

		$urls = MarkdownExtended::getVar('urls');
		$titles = MarkdownExtended::getVar('titles');
		$predef_attributes = MarkdownExtended::getVar('attributes');
		if (isset($urls[$link_id])) {
		    $attributes = array();
			$attributes['href'] = parent::runGamut('tool:EncodeAttribute', $urls[$link_id]);
			if (!empty($titles[$link_id])) {
				$attributes['title'] = parent::runGamut('tool:EncodeAttribute', $titles[$link_id]);
			}
			if (!empty($predef_attributes[$link_id])) {
				$attributes = array_merge(
				    parent::runGamut('tool:ExtractAttributes', $predef_attributes[$link_id]),
				    $attributes
				);
			}
			$this->_validateLinkAttributes($attributes);
            $block = MarkdownExtended::get('OutputFormatBag')
                ->buildTag('link', parent::runGamut('span_gamut', $link_text), $attributes);
            $result = parent::hashPart($block);
		} else {
			$result = $whole_match;
		}
		return $result;
	}

	/**
	 * @param array $matches A set of results of the `transform` function
	 * @return string
	 */
	protected function _inline_callback($matches) 
	{
		$whole_match	=  $matches[1];
		$link_text		=  $matches[2];
		$url		    =  $matches[3] == '' ? $matches[4] : $matches[3];
		$title			=& $matches[7];

        $attributes = array();
		$attributes['href'] = parent::runGamut('tool:EncodeAttribute', $url);
		if (!empty($title)) {
			$attributes['title'] = parent::runGamut('tool:EncodeAttribute', $title);
		}
		$this->_validateLinkAttributes($attributes);        
        $block = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('link', parent::runGamut('span_gamut', $link_text), $attributes);
        return parent::hashPart($block);
	}

    /**
     * Be sure to have a full attributes set (add a title if needed)
     *
     * @param array $attributes Passed by reference
     */
    protected function _validateLinkAttributes(array &$attributes)
    {
        if (empty($attributes['title']) && MarkdownExtended::getConfig('link_mask_title')) {
            $attributes['title'] = MDE_Helper::fillPlaceholders(
                MarkdownExtended::getConfig('link_mask_title'),
                !empty($attributes['href']) ? $attributes['href'] : ''
            );
        }
    }

}

// Endfile