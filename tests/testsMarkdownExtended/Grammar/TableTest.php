<?php
/*
 * This file is part of the PHP-MarkdownExtended package.
 *
 * (c) Pierre Cassat <me@e-piwi.fr> and contributors
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace testsMarkdownExtended\Grammar;

use \testsMarkdownExtended\MarkdownExtendedBaseTest;

class TableTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {

        // simple table
        $this->processParseTest(
            "
| First Header  | Second Header |
| ------------- | ------------: |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |
        ",
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr></tbody></table>',
            'Simple table fails!',
            true,
            true
        );

        // simple table with no leading pipe
        $this->processParseTest(
            "
First Header  | Second Header |
------------- | ------------: |
Content Cell  | Content Cell  |
Content Cell  | Content Cell  |
        ",
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr></tbody></table>',
            'Simple table with no leading pipe fails!',
            true,
            true
        );

        // simple table with not constant spacing
        $this->processParseTest(
            "
| First Header | Second Header |
| ------------ | ------------: |
| Cell | Cell |
| Cell | Cell |
        ",
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Cell</td><td style="text-align:right;">Cell</td></tr><tr><td>Cell</td><td style="text-align:right;">Cell</td></tr></tbody></table>',
            'Simple table with not constant spacing cells fails!',
            true,
            true
        );

        // table with multiple headers and label before
        $this->processParseTest(
            "
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
        ",
            '<table><caption id="prototype-table">[prototype <em>table</em>]</caption><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr><tr><th>First comment</th><th style="text-align:right;">Second comment</th><th style="text-align:center;">Third comment</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr><tr><td colspan="2">And more</td><td style="text-align:center;">And more</td></tr></tbody></table>',
            'Complex table with multiple headers and caption above fails!',
            true,
            true
        );

        // table with multiple headers and label after
/*
        $this->processParseTest(
            "
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
        ",
            '<table><caption id="prototype_table">[prototype <em>table</em>]</caption><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr><tr><th>First comment</th><th style="text-align:right;">Second comment</th><th style="text-align:center;">Third comment</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr><tr><td colspan="2">And more</td><td style="text-align:center;">And more</td></tr></tbody></table>',
            'Complex table with multiple headers and caption below fails!',
            true,
            true
        );
*/

        // table with multiple bodies
/*
        $this->processParseTest(
            "
|             | Grouping                    ||
First Header  | Second Header | Third header |
------------- | ------------: | :----------: |
Content Cell  |  *Long Cell*                ||
Content Cell  | **Cell**      | **Cell**     |

New section   |   More        |         Data |
And more      |           And more          ||
        ",
            '<table><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr></tbody><tbody><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr></tbody></table>',
            'Complex table with multiple bodies fails!',
            true,
            true
        );
*/
    }
    
}
