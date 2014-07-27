Name:       MarkdownExtended
Author:     Piero Wbmstr

Markdown Extended syntax
========================

## Typography

Bold text: `**bolded content**` or `__bolded content__` (example: **bold text**)

Italic text: `*italic content*` or `_italic content_` (example: *italic text*)

Allowing underscores in emphasis: `__my_underscored_bold_word__` (example: __my_underscored_bold_word__)

A code span: `` `function()` `` (example: `function()`)


## Links and images

Automatic links: `<http://example.com/>` and `<address@email.com>` (example: <http://example.com/> and <address@email.com>)

An hypertext link: `[link text](http://example.com/ "Optional link title")` (example: [link text](http://example.com/ "Optional link title"))

A referenced hypertext link: `[link text] [myid]` and after the paragraph, anywhere in the document `[myid]: http://example.com/ "Optional link title"` (example: [link text] [myid])

An embedded image: `![Alt text](http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title")` (example: ![Alt text](http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title"))

A referrenced embedded image: `![Alt text][myimageid]` and after the paragraph, anywhere in the document `[myimageid]: http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title"` (example: ![iumage] [myimageid])

An inpage link: `[link text](#anchor)` will return to `# my title {#anchor}` (example: [link text](#myanchor))

An self-reference link: `[link text][anchor]` will refer to `My text[anchor]` (example: [link text][#mynewanchor] or [atitleanchor][])

A referenced link image with attributes: `[mylink][]` will refer to reference `[mylink]: http://example.com/ "Optional title" class=external style="border: solid black 1px;"`

Example: [mylink][] and [mylink2][] and [mylink3][]

An embedded image with attributes: `![myimage][]` will refer to reference `[myimage]: http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title" width=40 height=40`

Example: ![myimage][]

A referenced embedded image with attributes: `![alternative text][myimage]` will refer to reference `[myimage]: http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title" width=40 height=40`

Example: ![my image][myimage]

[myid]: http://example.com/ "Optional link title"
[myimageid]: http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title"
[atest]: http://myexample.com/ (Optional link title)
[myimage]: http://upload.wikimedia.org/wikipedia/commons/7/70/Example.png "Optional image title" width=40 height=40
[mylink]: http://example.com/ "Optional title" class=external rel=external
[mylink2]: http://example.com/ "Optional title" class=external rel=external style="border: solid black 1px;"
[mylink3]: http://example.com/ "Optional title" class=external rel="external"
    style="border: solid black 1px;"


## Lists

### Unordered lists

Begin each entry by an asterisk, a plus or an hyphen followed by 3 spaces

    -   first item
    *   second item
        - first sub-item
        * second sub-item
    -   third item

Example:

-   first item
*   second item
    - first sub-item
    * second sub-item
-   third item

### Ordered lists

Begin each entry by a number followed by a dot and 3 spaces

    1.   first item
    1.   second item
        1. first sub-item
        1. second sub-item
    2.   third item

Example:

1.   first item
1.   second item
    1. first sub-item
    1. second sub-item
2.   third item


## Blocks and structural elements

Titles: `#my title level 1` or `###my title level 3` (example: just above)

Paragraphs: just pass a line (exemple this line ...)

### Pre-formatted contents

Begin lines with 4 spaces (example this block)

        pre formed content

Fenced code block: a line of tildes (at least 3)

    ~~~~
    My code here
    ~~~~

Example:
~~~~
My code here
~~~~

Fenced code block with information about the language: a line of tildes (at least 3) followed by language name (without space)

    ~~~~html
    My code here
    ~~~~

Example:
~~~~html
My code here
~~~~

### Blockquotes and citations

Begin lines by '>':

    > my citation

Example:

> My citation
>
> With a paragraph and some `code`
>
>     and even a preformatted string

Adding a reference URL to blockquote:

    > (http://example.com) this is my citation extracted from the content
    > of the page at http://example.com URL

Example:

> (http://example.com) this is my citation extracted from the content
> of the page at http://example.com URL

### Horizontal rules

3 or more hyphens, asterisks or underscores on a line

    ----

Example:

----


## Definitions

A definition:

    Apple
    :   Pomaceous fruit of plants of the genus Malus in
        the family Rosaceae.

Example:

Term 1
:   This is a definition with two paragraphs. Lorem ipsum
    dolor sit amet, consectetuer adipiscing elit. Aliquam
    hendrerit mi posuere lectus.

    Vestibulum enim wisi, viverra nec, fringilla in, laoreet
    vitae, risus.

:   Second definition for term 1, also wrapped in a paragraph
    because of the blank line preceding it.

Term 2
:   This definition has a code block, a blockquote and a list.

        code block.

    > block quote
    > on two lines.

    1.  first list item
    2.  second list item


## Abbreviations

An abbreviation:

    *[HTML]: Hyper Text Markup Language

Example: A text whit HTML expression.

*[HTML]: Hyper Text Markup Language

*[W3C]:  World Wide Web Consortium


## Tables

A table:

    | First Header  | Second Header |
    | ------------- | ------------: |
    | Content Cell  | Content Cell  |
    | Content Cell  | Content Cell  |

or (without leading pipe) :

    First Header  | Second Header |
    ------------- | ------------: |
    Content Cell  | Content Cell  |
    Content Cell  | Content Cell  |

or (not constant spaces) :

    | First Header | Second Header |
    | ------------ | ------------: |
    | Cell | Cell |
    | Cell | Cell |

Example:

| First Header  | Second Header |
| ------------- | ------------: |
| Content Cell  | Content Cell  |
| Content Cell  | Content Cell  |

and

First Header  | Second Header |
------------- | ------------: |
Content Cell  | Content Cell  |
Content Cell  | Content Cell  |

and

| First Header | Second Header |
| ------------ | ------------: |
| Cell | Cell |
| Cell | Cell |

A table with multi-bodies:

    |             | Grouping                    ||
    First Header  | Second Header | Third header |
    ------------- | ------------: | :----------: |
    Content Cell  |  *Long Cell*                ||
    Content Cell  | **Cell**      | **Cell**     |

    New section   |   More        |         Data |
    And more      |           And more          ||

Example:

|             | Grouping                    ||
First Header  | Second Header | Third header |
------------- | ------------: | :----------: |
Content Cell  |  *Long Cell*                ||
Content Cell  | **Cell**      | **Cell**     |

New section   |   More        |         Data |
And more      |           And more          ||

New example:

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

Same example with caption after the table:

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



## Notes

### Footnotes

A footnote:

    That's some text with a footnote.[^1]

    [^1]: And that's the footnote.

Example: That's some[^2] text with three footnotes.[^1][^3]

[^1]: And that's the first footnote.

    That's the second paragraph.

[^2]: And that's another footnote (the second)
    on *two lines* for test ...

[^3]: And that's a footnote [with a link](http://example.com).

### Citation notes

Citations : like footnote begining by a sharp

    This is a statement that should be attributed to
    its source[p. 23][#Doe:2006].

    And following is the description of the reference to be
    used in the bibliography.

    [#Doe:2006]: John Doe. *Some Big Fancy Book*.  Vanity Press, 2006.

Example:

This is a statement that should be attributed to
its source[p. 23][#Doe:2006].

And following is the description of the reference to be
used in the bibliography.

[#Doe:2006]: John Doe. *Some Big Fancy Book*.  Vanity Press, 2006.

### Glossary notes

Glossary footnotes :

    [^glossaryfootnote]: glossary: term (optional sort key)
        The actual definition belongs on a new line, and can continue on
        just as other footnotes.

Example:

My text with a footnote ref [^glossaryfootnote].

[^glossaryfootnote]: glossary: term (2)
    The actual definition belongs on a new line, and can continue on
    just as other footnotes.


## Inline HTML

This is a regular paragraph.

<table border="2px" cellspacing="2px" cellpadding="6px">
    <tr>
        <td>Foo</td>
        <td>*Foo*</td>
        <td>`Foo`</td>
    </tr>
</table>

This is another regular paragraph, with another call of footnote 2 [^2].

Below is the same table as above with argument `markdown="1"`.

<table border="2px" cellspacing="2px" cellpadding="6px" markdown="1">
<tr>
    <td>Foo</td>
    <td>*Foo*</td>
    <td>`Foo`</td>
</tr>
</table>

This is another regular paragraph.


## Mathematics

where \(\alpha = (t_1 - t_0)/L\) is the rate at which the thickness increases

Putting this into Castigliano's equation, we get

\[\Delta = \frac{\partial U^*}{\partial F} = \frac{12F}{Eb} \int_0^L \frac{x^2}{(t_0 + \alpha x)^3} dx\]



----
"**Markdown Extended Syntax**" - last updated at 27 july 2014

Creator & maintainer: [@pierowbmstr](http://github.com/piwi).

Original source of this file, see <http://github.com/piwi/markdown-extended/Syntax.md>.

For comments & bugs, see <http://github.com/piwi/markdown-extended/issues>.

