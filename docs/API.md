Name:       MarkdownExtended API
Author:     Les Ateliers Pierrot
Date: 2013-10-21
Version: 0.1-alpha


The Markdown Extended PHP package API
=====================================


The whole API is stored in the `\MarkdownExtended\API` namespace and is a set of interfaces
you must implement to use or override the parser or some of its objects.

## How does it work?

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
its DOM ids and its footnotes. This object is overloadable but MUST implement the
`MarkdownExtended\API\ContentInterface` interface.

The second step is handled by the `MarkdownExtended\Parser` object where lives the central
work of the syntax rules transformations. It depends on a configuration that can be reset
at every call. This object is overloadable but MUST implement the
`MarkdownExtended\API\ParserInterface` interface.

Finally, the whole thing is contained in the `MarkdownExtended\MarkdownExtended` object
that is a kind of global container for the Markdown work.

All API classes finally used to create objects are defined as configuration entries like:

        // the default API objects
        'content_class'             => '\MarkdownExtended\Content',
        'content_collection_class'  => '\MarkdownExtended\ContentCollection',
        'parser_class'              => '\MarkdownExtended\Parser',
        'templater_class'           => '\MarkdownExtended\Templater',
        'grammar\filter_class'      => '\MarkdownExtended\Grammar\Filter',
        'grammar\tool_class'        => '\MarkdownExtended\Grammar\Tool',

Please see the `MarkdownExtended\Config` object source for a full and up-to-date list.


## Full usage

### The "kernel" object

Creation of the container as a singleton instance:

    $mde = \MarkdownExtended\MarkdownExtended::create( options );

    // to retrieve the same instance after creation:
    $mde = \MarkdownExtended\MarkdownExtended::getInstance();

### The `Content` object

Creation of a new content object:

    // with a string:
    $source = new \MarkdownExtended\Content( $string );

    // with a file to get content from:
    $source = new \MarkdownExtended\Content( null, $filepath );

If you configured your own object, use:

    // get or create your own object instance
    $source = \MarkdownExtended\MarkdownExtended::get(
        'content', $config_as_an_array
    );


### The `Parser` object

Get the parser instance from the container:

    $parser = $mde->get('Parser', $parser_options);    

If you configured your own object, use:

    // get or create your own object instance
    $source = \MarkdownExtended\MarkdownExtended::get(
        'Parser', $parser_options_as_an_array
    );


### The markdown process

Make the source transformation:

    // this will return the Container
    $markdown = $parser->parse($source)
        // and this will return the Content object transformed
        ->getContent();

### The transformed content

Then, get the transformed content and other infos trom the `Content` object:

    echo "<html><head>"
        .$markdown->getMetadataHtml()   // the content metadata HTML formated
        ."</head><body>"
        .$markdown->getBody()           // the content HTML body
        ."<hr />"
        .$markdown->getNotesHtml()      // the content footnotes HTML formated
        ."</body></html>";

In case of a simple source (such as a textarea field):

    echo $markdown->getBody();

For simplest calls, a `Helper` is designed to allow usage of:

    echo \MarkdownExtended\MarkdownExtended::getFullContent();

that will return the exact same string as the one constructed above (a full HTML page
by default).


## Components

The Internal classes (required and not overloadable) are:

-   the API: `MarkdownExtended\API`
-   the "kernel" object: `MarkdownExtended\MarkdownExtended`
-   the configuration handler: `MarkdownExtended\Config`
-   the registry (works as a container): `MarkdownExtended\Registry`
-   the "output formater" which depends on your chosen format: `MarkdownExtended\OutputFormatBag`

The API classes, overloadables, are:

-   the "parser" who will handle all parsing steps: `MarkdownExtended\Parser`,
    which must implement the `MarkdownExtended\API\ParserInterface`
-   a "content" single object: `MarkdownExtended\Content`,
    which must implement the `MarkdownExtended\API\ContentInterface`
-   a collection of "contents": `MarkdownExtended\ContentCollection`,
    which must implement the `MarkdownExtended\API\CollectionInterface`
-   a "templater" object to load a parsed content in a template file: `\MarkdownExtended\Templater`,
    which must implement the `MarkdownExtended\API\TemplaterInterface`

Each object is loaded as a service in the kernel and can be retrieved from the kernel instance
with a simple getter:

    $object = \MardownExtended\MarkdownExtended::getInstance()->get( name );
    
Trying to get it, if the object does not exist yet, it will be created.


## The API kernel

The `MarkdownExtended\API` is the central class object. It handles all the parsing
logic and acts like a services container for other API's objects.

The `MarkdownExtended\MarkdownExtended` is the base public class object. It proposes
a large set of aliases to manage your contents (the original ones and their parsed results).

    // creation of the singleton instance of \MarkdownExtended\MarkdownExtended
    $parser = \MarkdownExtended\MarkdownExtended::create( [options] );

The best practice is to use the kernel as a singleton instance but you are allowed to use
it as a "classic" object creating it like any other PHP object.

The `MarkdownExtended` package can be simply call writing:

    // creation of the singleton instance of \MarkdownExtended\MarkdownExtended
    $content = \MarkdownExtended\MarkdownExtended::create()
        // get the \MarkdownExtended\Parser object passing it some options (optional)
        ->get('Parser', $options)
        // launch the transformation of a source content
        ->parse( new \MarkdownExtended\Content($source) )
        // get the result content object
        ->getContent();

This will load in `$content` the parsed HTML version of your original Markdown `$source`.
To get the part you need from the content, write:

    echo $content->getBody();

For simplest usage, some aliases are designed in the `MarkdownExtended` kernel:

    // to parse a string content:
    \MarkdownExtended\MarkdownExtended::transformString($source [, $parser_options]);
    
    // to parse a file content:
    \MarkdownExtended\MarkdownExtended::transformSource($filename [, $parser_options]);

These two methods returns a `\MarkdownExtended\Content` object. To finally get an HTML
version, write:

    \MarkdownExtended\MarkdownExtended::transformString($source [, $parser_options]);
    echo \MarkdownExtended\MarkdownExtended::getFullContent();

----
"**Markdown Extended API**" - last updated at 05 may 2014

Creator & maintainer: [@pierowbmstr](http://github.com/pierowbmstr).

Original source of this file, see <http://github.com/atelierspierrot/markdown-extended/API.md>.

For comments & bugs, see <http://github.com/atelierspierrot/markdown-extended/issues>.

