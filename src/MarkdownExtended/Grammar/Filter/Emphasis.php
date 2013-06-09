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
 * Process Markdown emphasis: bold & italic
 */
class Emphasis extends Filter
{
	
	/**#@+
	 * Redefining emphasis markers so that emphasis by underscore does not
	 * work in the middle of a word.
	 */
	var $em_relist = array(
		''  => '(?:(?<!\*)\*(?!\*)|(?<![a-zA-Z0-9_])_(?!_))(?=\S|$)(?![\.,:;]\s)',
		'*' => '(?<=\S|^)(?<!\*)\*(?!\*)',
		'_' => '(?<=\S|^)(?<!_)_(?![a-zA-Z0-9_])',
	);

	var $strong_relist = array(
		''   => '(?:(?<!\*)\*\*(?!\*)|(?<![a-zA-Z0-9_])__(?!_))(?=\S|$)(?![\.,:;]\s)',
		'**' => '(?<=\S|^)(?<!\*)\*\*(?!\*)',
		'__' => '(?<=\S|^)(?<!_)__(?![a-zA-Z0-9_])',
	);

	var $em_strong_relist = array(
		''    => '(?:(?<!\*)\*\*\*(?!\*)|(?<![a-zA-Z0-9_])___(?!_))(?=\S|$)(?![\.,:;]\s)',
		'***' => '(?<=\S|^)(?<!\*)\*\*\*(?!\*)',
		'___' => '(?<=\S|^)(?<!_)___(?![a-zA-Z0-9_])',
	);

	static $em_strong_prepared_relist;
	/**#@-*/

	/**
	 * Prepare regular expressions for searching emphasis tokens in any context.
	 */
	public function prepare() 
	{
		foreach ($this->em_relist as $em => $em_re) {
			foreach ($this->strong_relist as $strong => $strong_re) {
				// Construct list of allowed token expressions.
				$token_relist = array();
				if (isset($this->em_strong_relist["$em$strong"])) {
					$token_relist[] = $this->em_strong_relist["$em$strong"];
				}
				$token_relist[] = $em_re;
				$token_relist[] = $strong_re;
				
				// Construct master expression from list.
				$token_re = '{('. implode('|', $token_relist) .')}';
				self::$em_strong_prepared_relist["$em$strong"] = $token_re;
			}
		}
	}
	
	/**
	 * @param string $text
	 * @return string
	 */
	public function transform($text) 
	{
		$token_stack = array('');
		$text_stack = array('');
		$em = '';
		$strong = '';
		$tree_char_em = false;
		
		while (1) {

			// Get prepared regular expression for seraching emphasis tokens in current context.
			$token_re = self::$em_strong_prepared_relist["$em$strong"];
			
			// Each loop iteration search for the next emphasis token. 
			// Each token is then passed to handleSpanToken.
			$parts = preg_split($token_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE);
			$text_stack[0] .= $parts[0];
			$token =& $parts[1];
			$text =& $parts[2];
			
			if (empty($token)) {
				// Reached end of text span: empty stack without emitting any more emphasis.
				while ($token_stack[0]) {
					$text_stack[1] .= array_shift($token_stack);
					$text_stack[0] .= array_shift($text_stack);
				}
				break;
			}
			
			$token_len = strlen($token);
			if ($tree_char_em) {
				// Reached closing marker while inside a three-char emphasis.
				if ($token_len == 3) {
					// Three-char closing marker, close em and strong.
					array_shift($token_stack);
					$span = parent::runGamut('span_gamut', array_shift($text_stack));
                    $span = MarkdownExtended::get('OutputFormatBag')
                        ->buildTag('em', $span);
                    $span = MarkdownExtended::get('OutputFormatBag')
                        ->buildTag('strong', $span);
					$text_stack[0] .= parent::hashPart($span);
					$em = '';
					$strong = '';
				} else {
					// Other closing marker: close one em or strong and
					// change current token state to match the other
					$token_stack[0] = str_repeat($token{0}, 3-$token_len);
					$tag = $token_len == 2 ? "strong" : "italic";
					$span = parent::runGamut('span_gamut', $text_stack[0]);
                    $span = MarkdownExtended::get('OutputFormatBag')
                        ->buildTag($tag, $span);
					$text_stack[0] = parent::hashPart($span);
					$$tag = ''; // $$tag stands for $em or $strong
				}
				$tree_char_em = false;
			} else if ($token_len == 3) {
				if ($em) {
					// Reached closing marker for both em and strong.
					// Closing strong marker:
					for ($i = 0; $i < 2; ++$i) {
						$shifted_token = array_shift($token_stack);
						$tag = strlen($shifted_token) == 2 ? "strong" : "italic";
						$span = parent::runGamut('span_gamut', array_shift($text_stack));
                        $span = MarkdownExtended::get('OutputFormatBag')
                            ->buildTag($tag, $span);
						$text_stack[0] .= parent::hashPart($span);
						$$tag = ''; // $$tag stands for $em or $strong
					}
				} else {
					// Reached opening three-char emphasis marker. Push on token 
					// stack; will be handled by the special condition above.
					$em = $token{0};
					$strong = "$em$em";
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$tree_char_em = true;
				}
			} else if ($token_len == 2) {
				if ($strong) {
					// Unwind any dangling emphasis marker:
					if (strlen($token_stack[0]) == 1) {
						$text_stack[1] .= array_shift($token_stack);
						$text_stack[0] .= array_shift($text_stack);
					}
					// Closing strong marker:
					array_shift($token_stack);
					$span = parent::runGamut('span_gamut', array_shift($text_stack));
                    $span = MarkdownExtended::get('OutputFormatBag')
                        ->buildTag('strong', $span);
					$text_stack[0] .= parent::hashPart($span);
					$strong = '';
				} else {
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$strong = $token;
				}
			} else {
				// Here $token_len == 1
				if ($em) {
					if (strlen($token_stack[0]) == 1) {
						// Closing emphasis marker:
						array_shift($token_stack);
						$span = parent::runGamut('span_gamut', array_shift($text_stack));
                        $span = MarkdownExtended::get('OutputFormatBag')
                            ->buildTag('em', $span);
						$text_stack[0] .= parent::hashPart($span);
						$em = '';
					} else {
						$text_stack[0] .= $token;
					}
				} else {
					array_unshift($token_stack, $token);
					array_unshift($text_stack, '');
					$em = $token;
				}
			}
		}
		return $text_stack[0];
	}

}

// Endfile