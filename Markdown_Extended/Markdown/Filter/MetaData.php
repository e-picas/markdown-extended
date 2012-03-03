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
class Markdown_Filter_MetaData extends Markdown_Filter
{

	protected $metadata=array();
	
	protected static $inMetaData=0;
	
	// SPECIAL METADATA
	var $specials = array(
		'baseheaderlevel', 'quoteslanguage'
	);

	/**
	 * @param string $text Text to parse
	 * @return string The text parsed
	 */
	public function strip($text) 
	{
		$lines = preg_split('/\n/', $text);
		$text='';
		self::$inMetaData=1;
		foreach ($lines as $line) {
			if (self::$inMetaData===0) {
				$text .= $line."\n";
			} else {
				$text .= self::transform($line);
				if (preg_match('/^$/', $line)) {
					self::$inMetaData = 0;
				}
			}
		}
		
		if (!empty($this->metadata))
			Markdown_Extended::setVar('metadata', $this->metadata);

		return $text;
	}

	/**
	 * @param string $text Text to parse
	 * @return string The text parsed
	 */
	public function transform($line) 
	{
		$line = preg_replace_callback(
			'{^([a-zA-Z0-9][0-9a-zA-Z _-]*?):\s*(.*)$}i',
			array(&$this, '_callback'), $line);
		if (strlen($line)) $line .= "\n";
		return $line;
	}

	/**
	 * @param array $matches A set of results of the `transform` function
	 * @return string The text parsed
	 */
	protected function _callback($matches) 
	{
		$meta_key = strtolower(str_replace(' ', '', $matches[1]));
		$this->metadata[$meta_key] = trim($matches[2]);
		return '';
	}

	public function append($text)
	{
		$metadata = Markdown_Extended::getVar('metadata');
		Markdown_Extended::unsetVar('metadata');
		if (!empty($metadata)) {
			$metadata_str='';
			foreach($metadata as $meta_name=>$meta_value) {
				if (in_array($meta_name, $this->specials))
					Markdown_Extended::setConfig($meta_name, $meta_value);
				else
					$metadata_str .= "\n".
						$this->runGamut('tool:BuildMetaData', "$meta_name:$meta_value");
			}
			$text = $metadata_str."\n\n".$text;
		}
		return $text;
	}


}

// Endfile