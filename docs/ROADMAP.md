Markdown Extended Road-map
==========================

This document is for developers usage.


## TODOS that MUST be done before version 1.0

-   DONE - test the command line interface with direct stdin input, multi-input files etc
-   DONE - re-organize outputs from the command line interface (normal, verbose, quiet ...)
-   manage the replacement of in-text tags (`{% TOC %}` for instance)
-   test of different configuration sets (input fields, full files etc)
-   clarification of the rules : WRITE THE MANIFEST (?!) => anchors rules (!!)
-   find a better management for full HTML/body only return
-   make the code compliant with PHP MessDetector
-   embed a parser for the package "composer.json" to extract infos

## TODOS that SHOULD be done before version 1.0

-   manage the "markdown reminders" based on the `src/Resources/docs/` per-rule files
-   a unit test file for each syntax's rule
-   inform user that the Apache handler in the demo REQUIRES a config to fit actual server paths

## Known bugs

-   issue on footnotes (multi-reference causes a problem of multi-id) => OK, ticket 1
-   issue on tables with caption at its bottom => OK, ticket 2

## Evolutions

-   build a list of figures (?) : images, tables ...


----


## Development notes

Development of the `MarkdownExtended` namespace is done on repository branch named "wip".

To install the development environment, run:

    ~$ cd path/to/markdown-extended
    ~$ php path/to/composer install --dev

This will install [Sami](http://github.com/fabpot/sami), a PHP documentation generator, and
[PHPUnit](http://github.com/sebastianbergmann/phpunit/), a unit tester.

### Generate the documentation

You can (re-)generate a full PHP documentation, any time, running:

    ~$ php bin/sami.php render/update sami.config.php

NOTE - we figured that for some versions of Sami, if the documentation does not exist, you
need to first run the `render` action AND then the `update` one at the same time.

The documentation is built in a `phpdoc/` directory in the package, and requires a temporary
directory for its generation that is configured on:

    path/to/markdown-extended/../tmp/cache/markdown-extended/

You can modify this setting editing the `sami.config.php` file.

The documentation is not under version control (except for the "dev" branch of the stable
version).

### Launch unit-tests

You can verify that your package passes all tests running:

    ~$ php bin/phpunit

### MarkdownExtended auto-update

For auto-update of the Container class constants and the command line interface MANUEL based
on the actual `composer.json` values, a pre-commit hook is defined in `pre-commit-hook.sh`.
To use it, run:

    ~$ mkdir .git/hooks && cp pre-commit-hook.sh .git/hooks/pre-commit
    ~$ chmod +x .git/hooks/pre-commit

Or, you can simply run the following before a new version's commit:

    ~$ bash pre-commit-hook.sh


----


## What we want to do


The full schema of a Markdown parser usage could be:

        [source file content]       [options]    
                   ||                  ||
                   \/                  \/
            ---------------        ----------                            -------------------
            |  MD SOURCE  |   =>   | PARSER |   =>  [output format]  =>  | FORMATED RESULT |
            ---------------        ----------                            -------------------
                   /\                  /\                                         ||
                   ||                  ||                                         \/
                [string]        [ configuration ]                           [special infos]

1.  The original Markdown source can be either a buffered string (a form field for example)
    or the content of a Markdown file

2.  We want to parse the source content with a hand on options used during this parsing
    (no need to parse metadata in a content that will never have some for example)

3.  Finally, we want to get a formated content and to be able to retrieve certain infos
    from it, such as its metadata, its menu or the footnotes of the whole parsed result

4.  Additionally, it would be best that we can obtain a full formated result simply but
    can also pass this result through a template builder to construct a complex final string

## Result in the code

The first item of this chain is assumed by the `MarkdownExtended\Content` object.
It is a simple class that just stores different infos about a parsed content, such as its 
original source, the body of the result (the real parsed content), its menu, its metadata, 
its DOM ids and its footnotes.

The second step is handled by the `MarkdownExtended\Parser` object where lives the central
work of the syntax rules transformations. It depends on a configuration that can be reset
at every call.

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

    // this will return the Container:
    $markdown = $parser->parse($source)
        // and this will return the Content object transformed:
        ->getContent();

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

For simplest calls, a `Helper` is designed to allow usage of:

    echo \MarkdownExtended\MarkdownExtended::getFullContent();

that will return the exact same string as the one constructed above (a full HTML page
by default).


----
"**Markdown Extended ROADMAP**" - last updated at 08 june 2013

Creator & maintainer: Pierre Cassat <piero.wbmstr@gmail.com>.

Original source of this file, see <http://github.com/atelierspierrot/markdown-extended/ROADMAP.md>.

For comments & bugs, see <http://github.com/atelierspierrot/markdown-extended/issues>.
