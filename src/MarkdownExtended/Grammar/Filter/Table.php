<?php
/**
 * PHP Markdown Extended - A PHP parser for the Markdown Extended syntax
 * Copyright (c) 2008-2014 Pierre Cassat
 * <http://github.com/piwi/markdown-extended>
 *
 * Based on MultiMarkdown
 * Copyright (c) 2005-2009 Fletcher T. Penney
 * <http://fletcherpenney.net/>
 *
 * Based on PHP Markdown Lib
 * Copyright (c) 2004-2012 Michel Fortin
 * <http://michelf.com/projects/php-markdown/>
 *
 * Based on Markdown
 * Copyright (c) 2004-2006 John Gruber
 * <http://daringfireball.net/projects/markdown/>
 */
namespace MarkdownExtended\Grammar\Filter;

use MarkdownExtended\MarkdownExtended;
use MarkdownExtended\Grammar\Filter;
use MarkdownExtended\Helper as MDE_Helper;
use MarkdownExtended\Exception as MDE_Exception;

/**
 * Process Markdown tables
 *
 * @package MarkdownExtended\Grammar\Filter
 */
class Table
    extends Filter
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
     * @param   string  $text
     * @return  string
     */
    public function transform($text)
    {
        $less_than_tab = MarkdownExtended::getConfig('less_than_tab');

        // Find tables with leading pipe.
        $text = preg_replace_callback('
            {
                ^                                   # Start of a line
                (                                   # A caption between brackets (optional)
                    [ ]{0,'.$less_than_tab.'}
                    \[.*?\][ \t]*\n
                )?
                [ ]{0,'.$less_than_tab.'}           # Allowed whitespace.
                (
                    (?>
                        [ ]{0,'.$less_than_tab.'}   # Allowed whitespace.
                        [|]                         # Optional leading pipe (present)
                        .* [|] .* \n
                    )*
                )                                   # $1: Header rows (at least one pipe)

                [ ]{0,'.$less_than_tab.'}           # Allowed whitespace.
                [|] ([ ]*[-:]+[-| :]*) \n           # $2: Header underline

                (                                   # $3: Cells
                    (?>
                        [ ]{0,'.$less_than_tab.'}   # Allowed whitespace.
                        [|] .* \n                   # Row content
                    )*
                )
                (?=\n|\Z)                           # Stop at final double newline.
            }xm',
            array($this, '_callback'), $text);

        // Find tables without leading pipe.
        $text = preg_replace_callback('
            {
                ^                                   # Start of a line
                (                                   # A caption between brackets (optional)
                    [ ]{0,'.$less_than_tab.'}
                    \[.*?\][ \t]*\n
                )?
                [ ]{0,'.$less_than_tab.'}           # Allowed whitespace.
                (
                    (?>
                        [ ]{0,'.$less_than_tab.'}   # Allowed whitespace.
                        \S .* [|] .* \n
                    )*
                )                                   # $1: Header rows (at least one pipe)

                ^[ ]{0,'.$less_than_tab.'}          # Allowed whitespace at the beginning
                ([-:]+[ ]*[|][-| :]*) \n            # $2: Header underline

                (                                   # $3: Cells
                    (?>
                        [ ]{0,'.$less_than_tab.'}   # Allowed whitespace.
                         .* [|] .* \n               # Row content
                    )*
                )
                (?=\n|\Z)                           # Stop at final double newline.
            }xm',
            array($this, '_callback'), $text);

        return $text;
    }

    /**
     * Form HTML tables: removes leading pipe for each row
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _leadingPipe_callback($matches)
    {
        return self::_callback(array(
            $matches[0], $matches[1], $matches[2], preg_replace('/^ *[|]/m', '', $matches[3])
        ));
    }

    /**
     * Form HTML tables: parses table contents
     *
     * @param   array   $matches
     * @return  string
     */
    protected function _callback($matches)
    {
        $attributes = array();

//self::doDebug($matches);
        // The head string may have a begin slash
        $caption    = count($matches)>3 ? $matches[1] : null;
        $head       = count($matches)>3 ?
            preg_replace('/^ *[|]/m', '', $matches[2]) : preg_replace('/^ *[|]/m', '', $matches[1]);
        $underline  = count($matches)>3 ? $matches[3] : $matches[2];
        $content    = count($matches)>3 ?
            preg_replace('/^ *[|]/m', '', $matches[4]) : preg_replace('/^ *[|]/m', '', $matches[3]);

        // Remove any tailing pipes for each line.
        $underline  = preg_replace('/[|] *$/m', '', $underline);
        $content    = preg_replace('/[|] *$/m', '', $content);

        // Reading alignement from header underline.
        $separators = preg_split('/ *[|] */', $underline);
        foreach ($separators as $n => $s) {
            $attributes[$n] = array();
            if (preg_match('/^ *-+: *$/', $s)) {
                $attributes[$n]['style'] = 'text-align:right;';
            } else if (preg_match('/^ *:-+: *$/', $s)) {
                $attributes[$n]['style'] = 'text-align:center;';
            } else if (preg_match('/^ *:-+ *$/', $s)) {
                $attributes[$n]['style'] = 'text-align:left;';
            }
        }

        // Split content by row.
        $headers = explode("\n", trim($head, "\n"));

        $text = '';
        if (!empty($caption)) {
            $this->table_id = MDE_Helper::header2Label($caption);
            $text .= preg_replace_callback('/\[(.*)\]/', array($this, '_doCaption'), parent::runGamut('span_gamut', $caption));
        }

        $lines = '';
        foreach ($headers as $_header) {
            $line = '';
            // Parsing span elements, including code spans, character escapes,
            // and inline HTML tags, so that pipes inside those gets ignored.
            $_header    = parent::runGamut('filter:Span', $_header);

            // Split row by cell.
            $_header    = preg_replace('/[|] *$/m', '', $_header);
            $_headers   = preg_split('/[|]/', $_header);
            $col_count  = count($_headers);

            // Write column headers.
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
                if ($__header!='') {
                    $cell_attributes = $attributes[$n];
                    if (isset($headspans[$n])) {
                        $cell_attributes['colspan'] = $headspans[$n];
                    }
                    $line .= MarkdownExtended::get('OutputFormatBag')
                        ->buildTag('table_cell_head', parent::runGamut('span_gamut', trim($__header)), $cell_attributes) . "\n";
                }
            }
            $lines .= MarkdownExtended::get('OutputFormatBag')
                ->buildTag('table_line', $line) . "\n";
        }
        $text .= MarkdownExtended::get('OutputFormatBag')
            ->buildTag('table_header', $lines);

        // Split content by row.
        $rows = explode("\n", trim($content, "\n"));

        $lines = '';
        foreach ($rows as $row) {
            $line = '';
            // Parsing span elements, including code spans, character escapes,
            // and inline HTML tags, so that pipes inside those gets ignored.
            $row = parent::runGamut('filter:Span', $row);

            // Split row by cell.
            $row_cells = preg_split('/ *[|] */', $row, $col_count);
            $row_cells = array_pad($row_cells, $col_count, '');

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
                if ($cell!='') {
                $cell_attributes = $attributes[$n];
                if (isset($colspans[$n])) {
                    $cell_attributes['colspan'] = $colspans[$n];
                }
                $line .= MarkdownExtended::get('OutputFormatBag')
                    ->buildTag('table_cell', parent::runGamut('span_gamut', trim($cell)), $cell_attributes) . "\n";
                }
            }
            $lines .= MarkdownExtended::get('OutputFormatBag')
                ->buildTag('table_line', $line) . "\n";
        }
        $text .= MarkdownExtended::get('OutputFormatBag')
            ->buildTag('table_body', $lines);

        $table = MarkdownExtended::get('OutputFormatBag')
            ->buildTag('table', $text);
        return parent::hashBlock($table) . "\n";
    }

    /**
     * Build a table caption
     */
    protected function _doCaption($matches)
    {
        return MarkdownExtended::get('OutputFormatBag')
            ->buildTag('table_caption', $matches[0], array(
                'id'=>$this->table_id
            ));
    }

}

// Endfile