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
 * Process the inclusion of third-party Markdown files
 *
 * Search any tag in the content written using the `block_inclusion` config entry mask
 * and replace it by the parsing result if its content.
 *
 * The default inclusion mask is "<!-- @file_name.md@ -->"
 */
class BlockInclusion extends Filter
{

	/**
	 * Find defined inclusion blocks
	 *
	 * @param string $text
	 * @return string
	 */
	public function transform($text) 
	{
		$mask = MarkdownExtended::getConfig('block_inclusion');
		if (!empty($mask)) {
    		$regex = MDE_Helper::buildRegex($mask);
			$text = preg_replace_callback($regex, array($this, '_callback'), $text);
		}
		return $text;
	}

	/**
	 * Process each inclusion, errors are wirtten as comments
	 *
	 * @param array $matches One set of results form the `transform()` function
	 * @return string The result of the inclusion parsed if so
	 */
	protected function _callback($matches) 
	{
		$filename = $matches[1];
		try {
		    $base_dirname = \MarkdownExtended\MarkdownExtended::getInstance()
		        ->getContent()
		        ->getDirname();
		    if (!file_exists($filename)) {
		        $filename = rtrim($base_dirname, DIRECTORY_SEPARATOR)
		            . DIRECTORY_SEPARATOR . $filename;
		    }
            $content = new \MarkdownExtended\Content(null, $filename);
            $content_id = $content->getId();
            $parsed_content = \MarkdownExtended\MarkdownExtended::getInstance()
                ->get('Parser')
                ->parse($content, true)
                ->getContent($content_id)
		        ->getBody();
		} catch (MDE_Exception\InvalidArgumentException $e) {
		    $parsed_content = "<!-- ERROR while parsing $filename : '{$e->getMessage()}' -->";
		} catch (Exception $e) {
		    $parsed_content = "<!-- ERROR while parsing $filename : '{$e->getMessage()}' -->";
		}
        return $parsed_content;
	}

}

// Endfile