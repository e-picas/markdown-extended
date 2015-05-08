<?php
/*
 * This file is part of the PHP-Markdown-Extended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MarkdownExtendedTests\Grammar;

use \MarkdownExtendedTests\ParserTest;
use \MarkdownExtended\MarkdownExtended;

class TableTest extends ParserTest
{

    public function testCreate()
    {

        // simple table
        $md = <<<MSG
| First Header  | Second Header |
| ------------- | ------------: |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr></tbody></table>',
            '[parsing] test of simple table'
        );

        // simple table with no leading pipe
        $md = <<<MSG
First Header  | Second Header |
------------- | ------------: |
Content Cell  | Content Cell  |
Content Cell  | Content Cell  |
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr></tbody></table>',
            '[parsing] test of simple table with no leading pipe'
        );

        // simple table with not constant spacing
        $md = <<<MSG
| First Header | Second Header |
| ------------ | ------------: |
| Cell | Cell |
| Cell | Cell |
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Cell</td><td style="text-align:right;">Cell</td></tr><tr><td>Cell</td><td style="text-align:right;">Cell</td></tr></tbody></table>',
            '[parsing] test of simple table with not constant spaced cells'
        );

        // table with multiple headers and label before
        $md = <<<MSG
[prototype *table*]
|             | Grouping                    ||
First Header  | Second Header | Third header |
First comment  | Second comment | Third comment |
------------- | ------------: | :----------: |
Content Cell  |  *Long Cell*                ||
Content Cell  | **Cell**      | **Cell**     |
New section   |   More        |         Data |
And more      |           And more          ||
And more                     || And more     |
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<table><caption id="prototype-table">[prototype <em>table</em>]</caption><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr><tr><th>First comment</th><th style="text-align:right;">Second comment</th><th style="text-align:center;">Third comment</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr><tr><td colspan="2">And more</td><td style="text-align:center;">And more</td></tr></tbody></table>',
            '[parsing] test of complex table with multiple headers and a caption above'
        );

        // table with multiple headers and label after
/*/
        $md = <<<MSG
|             | Grouping                    ||
First Header  | Second Header | Third header |
First comment  | Second comment | Third comment |
------------- | ------------: | :----------: |
Content Cell  |  *Long Cell*                ||
Content Cell  | **Cell**      | **Cell**     |
New section   |   More        |         Data |
And more      |           And more          ||
And more                     || And more     |
[prototype *table*]
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<table><caption id="prototype_table">[prototype <em>table</em>]</caption><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr><tr><th>First comment</th><th style="text-align:right;">Second comment</th><th style="text-align:center;">Third comment</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr><tr><td colspan="2">And more</td><td style="text-align:center;">And more</td></tr></tbody></table>',
            '[parsing] test of a complex table with multiple headers and a caption below'
        );
//*/

        // table with multiple bodies
/*/
        $md = <<<MSG
|             | Grouping                    ||
First Header  | Second Header | Third header |
------------- | ------------: | :----------: |
Content Cell  |  *Long Cell*                ||
Content Cell  | **Cell**      | **Cell**     |

New section   |   More        |         Data |
MSG;
        $this->assertEquals(
            $this->stripWhitespaceAndNewLines(
                (string) MarkdownExtended::parse($md, array('template'=>false))
            ),
            '<table><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr></tbody><tbody><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr></tbody></table>',
            '[parsing] test of complex table with multiple bodies'
        );
//*/
    }
}
