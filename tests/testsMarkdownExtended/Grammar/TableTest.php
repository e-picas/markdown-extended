<?php
/**
 * PHP Markdown Extended
 * Copyright (c) 2008-2014 Pierre Cassat
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
namespace testsMarkdownExtended\Grammar;

use \testsMarkdownExtended\MarkdownExtendedBaseTest;

class TableTest extends MarkdownExtendedBaseTest
{

    public function testCreate()
    {
        $markdownParser = $this->createParser();

        // simple table
        $markdownContent5 = $this->createContent("
| First Header  | Second Header |
| ------------- | ------------: |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |
        ");
        $content5 = $markdownParser->parse($markdownContent5)->getContent();
        $this->assertEquals(
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr></tbody></table>',
            str_replace("\n", ' ', $this->getBody($content5, true)), 'Simple table fails!');

        // simple table with no leading pipe
        $markdownContent6 = $this->createContent("
First Header  | Second Header |
------------- | ------------: |
Content Cell  | Content Cell  |
Content Cell  | Content Cell  |
        ");
        $content6 = $markdownParser->parse($markdownContent6)->getContent();
        $this->assertEquals(
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr><tr><td>Content Cell</td><td style="text-align:right;">Content Cell</td></tr></tbody></table>',
            str_replace("\n", ' ', $this->getBody($content6, true)), 'Simple table with no leading pipe fails!');

        // simple table with not constant spacing
        $markdownContent6 = $this->createContent("
| First Header | Second Header |
| ------------ | ------------: |
| Cell | Cell |
| Cell | Cell |
        ");
        $content6 = $markdownParser->parse($markdownContent6)->getContent();
        $this->assertEquals(
            '<table><thead><tr><th>First Header</th><th style="text-align:right;">Second Header</th></tr></thead><tbody><tr><td>Cell</td><td style="text-align:right;">Cell</td></tr><tr><td>Cell</td><td style="text-align:right;">Cell</td></tr></tbody></table>',
            str_replace("\n", ' ', $this->getBody($content6, true)), 'Simple table with not constant spacing cells fails!');


        // table with multiple headers and label before
        $markdownContent7 = $this->createContent("
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
        ");
        $content7 = $markdownParser->parse($markdownContent7)->getContent();
        $this->assertEquals(
            '<table><caption id="prototype_table">[prototype <em>table</em>]</caption><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr><tr><th>First comment</th><th style="text-align:right;">Second comment</th><th style="text-align:center;">Third comment</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr><tr><td colspan="2">And more</td><td style="text-align:center;">And more</td></tr></tbody></table>',
            str_replace("\n", ' ', $this->getBody($content7, true)), 'Complex table with multiple headers and caption above fails!');

        // table with multiple headers and label after
        $markdownContent8 = $this->createContent("
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
        ");
        $content8 = $markdownParser->parse($markdownContent8)->getContent();
        $this->assertEquals(
            '<table><caption id="prototype_table">[prototype <em>table</em>]</caption><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr><tr><th>First comment</th><th style="text-align:right;">Second comment</th><th style="text-align:center;">Third comment</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr><tr><td colspan="2">And more</td><td style="text-align:center;">And more</td></tr></tbody></table>',
            str_replace("\n", ' ', $this->getBody($content8, true)), 'Complex table with multiple headers and caption below fails!');

        // table with multiple bodies
        $markdownContent8 = $this->createContent("
|             | Grouping                    ||
First Header  | Second Header | Third header |
------------- | ------------: | :----------: |
Content Cell  |  *Long Cell*                ||
Content Cell  | **Cell**      | **Cell**     |

New section   |   More        |         Data |
And more      |           And more          ||
        ");
        $content8 = $markdownParser->parse($markdownContent8)->getContent();
        $this->assertEquals(
            '<table><thead><tr><th></th><th style="text-align:right;" colspan="2">Grouping</th></tr><tr><th>First Header</th><th style="text-align:right;">Second Header</th><th style="text-align:center;">Third header</th></tr></thead><tbody><tr><td>Content Cell</td><td style="text-align:right;" colspan="2"><em>Long Cell</em></td></tr><tr><td>Content Cell</td><td style="text-align:right;"><strong>Cell</strong></td><td style="text-align:center;"><strong>Cell</strong></td></tr></tbody><tbody><tr><td>New section</td><td style="text-align:right;">More</td><td style="text-align:center;">Data</td></tr><tr><td>And more</td><td style="text-align:right;" colspan="2">And more</td></tr></tbody></table>',
            str_replace("\n", ' ', $this->getBody($content8, true)), 'Complex table with multiple bodies fails!');

    }
    
}
