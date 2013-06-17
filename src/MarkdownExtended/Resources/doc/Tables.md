Block: Block Elements
Title: Tables

Tables are constructed by visual resemblance. The first line (*the headers*) and the second
one (*the separator*) are required.

    [an optional caption]
    | First Header  | Second Header | Third Header |
    | ------------- | ------------: | :----------: |
    | Content Cell  | Content right-aligned | Content center-aligned |
    | Content Cell  | Content on two columns ||

will produce:

[an optional caption]
| First Header  | Second Header | Third Header |
| ------------- | ------------: | :----------: |
| Content Cell  | Content right-aligned | Content center-aligned |
| Content Cell  | Content on two columns ||

Use colons around seperate line to manage alignment of a column (colon on the right is right-aligned,
on the left is left-aligned and on both sides is center-aligned). Tables can have many lines
of headers and many body sections by passing a blank line between them.
