Markdown Extended Road-map
==========================

This document is for developers usage.


## What we want to do


The full schema of a Markdown parser usage could be:

        [source file content]       [options]    
                   ||                  ||
                   \/                  \/
            ---------------        ----------        -------------------
            |  MD SOURCE  |   =>   | PARSER |   =>   | FORMATED RESULT |
            ---------------        ----------        -------------------
                   /\                 /\                    ||
                   ||                 ||                    \/
                [string]       [ configuration ]     [special infos]

1.  The original Markdown source can be either a buffered string (a form field for example)
    or the content of a Markdown file

2.  We want to parse the source content with a hand on options used during this parsing
    (no need to parse metadata in a content that will never have some for example)

3.  Finally, we want to get a formated content and to be able to retrieve certain infos
    from it, such as its metadata, its menu or the footnotes of the whole parsed result

## Result in the code

The first item of this chain is assumed by the `MarkdownExtended\Content` object.
It is a simple class that just stores different infos about a parsed content, such as its 
original source, the body of the result (the real parsed content), its menu, its metadata, 
its DOM ids and its footnotes.

The second step handler is the `MarkdownExtended\Parser` object where lives the central work
of the syntax rules transformations. It depends on a configuration that can be reset at
every call.

Finally, the whole thing is contained in the `MarkdownExtended\MarkdownExtended` object
that is a kind of global container for the Markdown work.

### Full usage

#### The "kernel" object

Creation of the container as a singleton instance:

    $mde = \MarkdownExtended\MarkdownExtended::create();

    // to retrieve the same instance after creation:
    $mde = \MarkdownExtended\MarkdownExtended::getInstance();

#### The `Content` object

Creation of a new content object:

    // with a string:
    $source = new \MarkdownExtended\Content( $string );

    // with a file to get content from:
    $source = new \MarkdownExtended\Content( null, $filepath );

#### The `Parser` object

Get the parser instance from the container:

    $parser = $mde->get('Parser', $parser_options);    

#### The markdown process

Make the source transformation:

    // this will return the Content object transformed:
    $markdown = $parser->parse($source);

#### The transformed content

Then, get the transformed content and other infos trom the `Content` object:

    echo "<html><head>"
        .$markdown->getMetadataHtml() // the content metadata HTML formated
        ."</head><body>"
        .$markdown->getBody() // the content HTML body
        ."<hr />"
        .$markdown->getNotesHtml() // the content footnotes HTML formated
        ."</body></html>";

In case of a simple source (such as a textarea field):

    echo $markdown->getBody();

## TODOS

-   test the command line interface with direct stdin input
-   issue on footnotes
-   issue on tables with caption at its bottom
-   build a list of figures (?) : images, tables ...



----
"**Markdown Extended ROADMAP**" - last updated at 08 june 2013

Creator & maintainer: Pierre Cassat <piero.wbmstr@gmail.com>.

Original source of this file, see <https://github.com/atelierspierrot/markdown-extended/ROADMAP.md>.

For comments & bugs, see <https://github.com/atelierspierrot/markdown-extended/issues>.
