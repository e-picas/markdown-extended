<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\API\Kernel;

/**
 * Process Markdown in-text HTML
 */
class HTML extends Filter
{
    /**
     * @var string  Tags that are always treated as block tags:
     */
    public $block_tags_re = 'p|div|h[1-6]|blockquote|pre|table|dl|ol|ul|address|form|fieldset|iframe|hr|legend';

    /**
     * @var string  Tags treated as block tags only if the opening tag is alone on it's line:
     */
    public $blocks_tags_re = 'script|noscript|math|ins|del';

    /**
     * @var string  Tags where markdown="1" default to span mode:
     */
    public $contain_span_tags_re = 'p|h[1-6]|li|dd|dt|td|th|legend|address';

    /**
     * @var string  Tags which must not have their contents modified, no matter where they appear
     */
    public $clean_tags_re = 'script|math';

    /**
     * @var string  Tags that do not need to be closed.
     */
    public $auto_close_tags_re = 'hr|img';

    /**
     * Hashify HTML Blocks and "clean tags".
     *
     * We only want to do this for block-level HTML tags, such as headers,
     * lists, and tables. That's because we still want to wrap <p>s around
     * "paragraphs" that are wrapped in non-block-level tags, such as anchors,
     * phrase emphasis, and spans. The list of tags we're looking for is
     * hard-coded.
     *
     * This works by calling _HashHTMLBlocks_InMarkdown, which then calls
     * _HashHTMLBlocks_InHTML when it encounter block tags. When the markdown="1"
     * attribute is found whitin a tag, _HashHTMLBlocks_InHTML calls back
     *  _HashHTMLBlocks_InMarkdown to handle the Markdown syntax within the tag.
     * These two functions are calling each other. It's recursive!
     *
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        // Call the HTML-in-Markdown hasher.
        list($text, ) = self::_hashBlocks_inMarkdown($text);
        return $text;
    }

    /**
     * Parse markdown text, calling _HashHTMLBlocks_InHTML for block tags.
     *
     * *   $indent is the number of space to be ignored when checking for code
     *     blocks. This is important because if we don't take the indent into
     *     account, something like this (which looks right) won't work as expected:
     *
     *     <div>
     *         <div markdown="1">
     *         Hello World.  <-- Is this a Markdown code block or text?
     *         </div>  <-- Is this a Markdown code block or a real tag?
     *     <div>
     *
     *     If you don't like this, just don't indent the tag on which
     *     you apply the markdown="1" attribute.
     *
     * *   If $enclosing_tag_re is not empty, stops at the first unmatched closing
     *     tag with that name. Nested tags supported.
     *
     * *   If $span is true, text inside must treated as span. So any double
     *     newline will be replaced by a single newline so that it does not create
     *     paragraphs.
     *
     * Returns an array of that form: ( processed text , remaining text )
     *
     * @param   string  $text       The text to be parsed
     * @param   int     $indent     The indentation to use
     * @param   string  $enclosing_tag_re   The closing tag to use
     * @param   bool    $span       Are we in a span element (false by default)
     * @return  array               ( processed text , remaining text )
     */
    protected function _hashBlocks_inMarkdown($text, $indent = 0, $enclosing_tag_re = '', $span = false)
    {
        if ($text === '') {
            return ['', ''];
        }

        // Regex to check for the presense of newlines around a block tag.
        $newline_before_re = '/(?:^\n?|\n\n)*$/';
        $newline_after_re =
            '{
                ^                             # Start of text following the tag.
                (?>[ ]*<!--.*?-->)?           # Optional comment.
                [ ]*\n                        # Must be followed by newline.
            }xs';

        // Regex to match any tag.
        $block_tag_re =
            '{
                (                               # $2: Capture hole tag.
                    </?                         # Any opening or closing tag.
                        (?>                     # Tag name.
                            '.$this->block_tags_re.'            |
                            '.$this->blocks_tags_re.'    |
                            '.$this->clean_tags_re.'            |
                            (?!\s)'.$enclosing_tag_re.'
                        )
                        (?:
                            (?=[\s"\'/a-zA-Z0-9])   # Allowed characters after tag name.
                            (?>
                                ".*?"       |       # Double quotes (can contain `>`)
                                \'.*?\'     |       # Single quotes (can contain `>`)
                                .+?                 # Anything but quotes and `>`.
                            )*?
                        )?
                    >                               # End of tag.
                |
                    <!--    .*?     -->         # HTML Comment
                |
                    <\?.*?\?> | <%.*?%>         # Processing instruction
                |
                    <!\[CDATA\[.*?\]\]>         # CData Block
                |
                                                # Code span marker
                    `+
                '. (!$span ? '                 # If not in span.
                |
                                                # Indented code block
                    (?: ^[ ]*\n | ^ | \n[ ]*\n )
                    [ ]{'.($indent + 4).'}[^\n]* \n
                    (?>
                        (?: [ ]{'.($indent + 4).'}[^\n]* | [ ]* ) \n
                    )*
                |
                                                # Fenced code block marker
                    (?> ^ | \n )
                    [ ]{0,'.($indent).'}~~~+[ ]*\n
                ' : ''). '                     # End (if not is span).
                )
            }xs';

        $depth = 0;     // Current depth inside the tag tree.
        $parsed = "";   // Parsed text that will be returned.

        // Loop through every tag until we find the closing tag of the parent
        // or loop until reaching the end of text if no parent tag specified.
        do {

            // Split the text using the first $tag_match pattern found.
            // Text before  pattern will be first in the array, text after
            // pattern will be at the end, and between will be any catches made
            // by the pattern.
            $parts = preg_split($block_tag_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE);

            // If in Markdown span mode, add a empty-string span-level hash
            // after each newline to prevent triggering any block element.
            if ($span) {
                $void = parent::hashPart("", ':');
                $newline = "$void\n";
                $parts[0] = $void . str_replace("\n", $newline, $parts[0]) . $void;
            }

            $parsed .= $parts[0]; // Text before current tag.

            // If end of $text has been reached. Stop loop.
            if (count($parts) < 3) {
                $text = "";
                break;
            }

            $tag  = $parts[1]; // Tag to handle.
            $text = $parts[2]; // Remaining text after current tag.
            $tag_re = preg_quote($tag); // For use in a regular expression.

            // Check for: Code span marker
            if ($tag[0] == "`") {
                // Find corresponding end marker.
                $tag_re = preg_quote($tag);
                // End marker found: pass text unchanged until marker.
                if (preg_match('{^(?>.+?|\n(?!\n))*?(?<!`)'.$tag_re.'(?!`)}', $text, $matches)) {
                    $parsed .= $tag . $matches[0];
                    $text = substr($text, strlen($matches[0]));

                    // Unmatched marker: just skip it.
                } else {
                    $parsed .= $tag;
                }
            }

            // Check for: Fenced code block marker.
            elseif (preg_match('{^\n?[ ]{0,'.($indent + 3).'}~}', $tag)) {
                // Fenced code block marker: find matching end marker.
                $tag_re = preg_quote(trim($tag));
                // End marker found: pass text unchanged until marker.
                if (preg_match('{^(?>.*\n)+?[ ]{0,'.($indent).'}'.$tag_re.'[ ]*\n}', $text, $matches)) {
                    $parsed .= $tag . $matches[0];
                    $text = substr($text, strlen($matches[0]));

                    // No end marker: just skip it.
                } else {
                    $parsed .= $tag;
                }
            }

            // Check for: Indented code block.
            elseif ($tag[0] == "\n" || $tag[0] == " ") {
                // Indented code block: pass it unchanged, will be handled later.
                $parsed .= $tag;
            }

            // Check for: Opening Block level tag or
            //            Opening Context Block tag (like ins and del)
            //               used as a block tag (tag is alone on it's line).
            elseif (preg_match('{^<(?:'.$this->block_tags_re.')\b}', $tag) ||
                (preg_match('{^<(?:'.$this->blocks_tags_re.')\b}', $tag) &&
                    preg_match($newline_before_re, $parsed) &&
                    preg_match($newline_after_re, $text))
            ) {
                // Need to parse tag and following text using the HTML parser.
                list($block_text, $text) =
                    self::_hashBlocks_inHTML($tag . $text, "hashBlock", true);

                // Make sure it stays outside of any paragraph by adding newlines.
                $parsed .= "\n\n$block_text\n\n";
            }

            // Check for: Clean tag (like script, math)
            //            HTML Comments, processing instructions.
            elseif (preg_match('{^<(?:'.$this->clean_tags_re.')\b}', $tag) ||
                $tag[1] == '!' || $tag[1] == '?') {
                // Need to parse tag and following text using the HTML parser.
                // (don't check for markdown attribute)
                list($block_text, $text) =
                    $this->_hashBlocks_inHTML($tag . $text, "hashClean", false);
                $parsed .= $block_text;
            }

            // Check for: Tag with same name as enclosing tag.
            elseif ($enclosing_tag_re !== '' &&
                # Same name as enclosing tag.
                preg_match('{^</?(?:'.$enclosing_tag_re.')\b}', $tag)) {
                // Increase/decrease nested tag count.
                if ($tag[1] == '/') {
                    $depth--;
                } elseif ($tag[strlen($tag) - 2] != '/') {
                    $depth++;
                }
                if ($depth < 0) {
                    // Going out of parent element. Clean up and break so we
                    // return to the calling function.
                    $text = $tag . $text;
                    break;
                }
                $parsed .= $tag;
            } else {
                $parsed .= $tag;
            }
        } while ($depth >= 0);

        return [$parsed, $text];
    }

    /**
     * Parse HTML, calling _HashHTMLBlocks_InMarkdown for block tags.
     *
     * *   Calls $hash_method to convert any blocks.
     * *   Stops when the first opening tag closes.
     * *   $md_attr indicate if the use of the `markdown="1"` attribute is allowed.
     *     (it is not inside clean tags)
     *
     * Returns an array of that form: ( processed text , remaining text )
     *
     * @param   string  $text           The text to be parsed
     * @param   string  $hash_method    The method to execute
     * @param   string  $md_attr        The attributes to add
     * @return  array                   ( processed text , remaining text )
     */
    protected function _hashBlocks_inHTML($text, $hash_method, $md_attr)
    {
        if ($text === '') {
            return ['', ''];
        }

        // Regex to match `markdown` attribute inside of a tag.
        $markdown_attr_re = '
            {
                \s*           # Eat whitespace before the `markdown` attribute
                markdown
                \s*=\s*
                (?>
                    (["\'])        # $1: quote delimiter
                    (.*?)          # $2: attribute value
                    \1             # matching delimiter
                |
                    ([^\s>]*)      # $3: unquoted attribute value
                )
                ()                 # $4: make $3 always defined (avoid warnings)
            }xs';

        // Regex to match any tag.
        $tag_re = '{
                (                               # $2: Capture hole tag.
                    </?                         # Any opening or closing tag.
                        [\w:$]+                 # Tag name.
                        (?:
                            (?=[\s"\'/a-zA-Z0-9])   # Allowed characters after tag name.
                            (?>
                                ".*?"       |       # Double quotes (can contain `>`)
                                \'.*?\'     |       # Single quotes (can contain `>`)
                                .+?                 # Anything but quotes and `>`.
                            )*?
                        )?
                    >                               # End of tag.
                |
                    <!--    .*?     -->             # HTML Comment
                |
                    <\?.*?\?> | <%.*?%>             # Processing instruction
                |
                    <!\[CDATA\[.*?\]\]>             # CData Block
                )
            }xs';

        $original_text = $text; // Save original text in case of faliure.

        $depth      = 0;      // Current depth inside the tag tree.
        $block_text = "";     // Temporary text holder for current text.
        $parsed     = "";     // Parsed text that will be returned.

        // Get the name of the starting tag.
        // (This pattern makes $base_tag_name_re safe without quoting.)
        $base_tag_name_re = '';
        if (preg_match('/^<([\w:$]*)\b/', $text, $matches)) {
            $base_tag_name_re = $matches[1];
        }

        // Loop through every tag until we find the corresponding closing tag.
        do {

            // Split the text using the first $tag_match pattern found.
            // Text before  pattern will be first in the array, text after
            // pattern will be at the end, and between will be any catches made
            // by the pattern.
            $parts = preg_split($tag_re, $text, 2, PREG_SPLIT_DELIM_CAPTURE);

            if (count($parts) < 3) {
                // End of $text reached with unbalenced tag(s).
                // In that case, we return original text unchanged and pass the
                // first character as filtered to prevent an infinite loop in the
                // parent function.
                return [$original_text[0], substr($original_text, 1)];
            }

            $block_text .= $parts[0]; // Text before current tag.
            $tag         = $parts[1]; // Tag to handle.
            $text        = $parts[2]; // Remaining text after current tag.

            // Check for: Auto-close tag (like <hr/>) Comments and Processing Instructions.
            if (preg_match('{^</?(?:'.$this->auto_close_tags_re.')\b}', $tag) ||
                $tag[1] == '!' || $tag[1] == '?') {
                // Just add the tag to the block as if it was text.
                $block_text .= $tag;
            } else {

                // Increase/decrease nested tag count. Only do so if
                // the tag's name match base tag's.
                if (preg_match('{^</?'.$base_tag_name_re.'\b}', $tag)) {
                    if ($tag[1] == '/') {
                        $depth--;
                    } elseif ($tag[strlen($tag) - 2] != '/') {
                        $depth++;
                    }
                }

                // Check for `markdown="1"` attribute and handle it.
                if ($md_attr &&
                    preg_match($markdown_attr_re, $tag, $attr_m) &&
                    preg_match('/^1|block|span$/', $attr_m[2] . $attr_m[3])) {
                    // Remove `markdown` attribute from opening tag.
                    $tag = preg_replace($markdown_attr_re, '', $tag);

                    // Check if text inside this tag must be parsed in span mode.
                    $mode = $attr_m[2] . $attr_m[3];
                    $span_mode = $mode == 'span' || $mode != 'block' &&
                        preg_match('{^<(?:'.$this->contain_span_tags_re.')\b}', $tag);

                    // Calculate indent before tag.
                    if (preg_match('/(?:^|\n)( *?)(?! ).*?$/', $block_text, $matches)) {
                        /* @var callable $strlen */
                        $strlen = Kernel::getConfig('utf8_strlen');
                        $indent = $strlen($matches[1], 'UTF-8');
                    } else {
                        $indent = 0;
                    }

                    // End preceding block with this tag.
                    $block_text .= $tag;
                    $parsed     .= $this->$hash_method($block_text);

                    // Get enclosing tag name for the ParseMarkdown function.
                    // (This pattern makes $tag_name_re safe without quoting.)
                    preg_match('/^<([\w:$]*)\b/', $tag, $matches);
                    $tag_name_re = $matches[1];

                    // Parse the content using the HTML-in-Markdown parser.
                    list($block_text, $text) =
                        self::_hashBlocks_inMarkdown($text, $indent, $tag_name_re, $span_mode);

                    // Outdent markdown text.
                    if ($indent > 0) {
                        $block_text = preg_replace("/^[ ]{1,$indent}/m", "", $block_text);
                    }

                    // Append tag content to parsed text.
                    if (!$span_mode) {
                        $parsed .= "\n\n$block_text\n\n";
                    } else {
                        $parsed .= "$block_text";
                    }

                    // Start over a new block.
                    $block_text = "";
                } else {
                    $block_text .= $tag;
                }
            }
        } while ($depth > 0);

        // Hash last block text that wasn't processed inside the loop.
        $parsed .= $this->$hash_method($block_text);

        return [$parsed, $text];
    }
}
