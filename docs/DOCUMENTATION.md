Man:        PHP-Markdown-Extended Developer Manual
Man-name:   markdown-extended-api
Section:    7
Author:     Pierre Cassat
Date:       27-12-2014
Version:    0.1-gamma4


## NAME

PHP-Markdown-Extended-API - Developer documentation of the internal API of PHP-Markdown-Extended.

The whole API is stored in the `\MarkdownExtended\API` namespace and is a set of interfaces
you must implement to use or override the parser or some of its objects.


## LIFE-CYCLE

The full schema of a Markdown parser usage could be:

            [source file content]   [options]    
                   ||                  ||
                   \/                  \/
            ---------------        ----------                            -------------------
            |  MD SOURCE  |   =>   | PARSER |   =>  [output format]  =>  | FORMATTED RESULT |
            ---------------        ----------                            -------------------
                   /\                  /\                                         ||
                   ||                  ||                                         \/
                [string]        [ configuration ]                           [special infos]


The original Markdown source can be either a buffered string (a form field for example)
or the content of a Markdown file

We want to parse the source content with a hand on options used during this parsing
(no need to parse metadata in a content that will never have some for example)

Finally, we want to get a formatted content and to be able to retrieve certain infos
from it, such as its metadata, its menu or the footnotes of the whole parsed result

Additionally, it would be best that we can obtain a full formatted result simply but
can also pass this result through a template builder to construct a complex final string


## CODING RESULTS

The first item of this chain is assumed by the `\MarkdownExtended\Content` object.
It is a simple class that just stores different infos about a parsed content, such as its 
original source, the body of the result (the real parsed content), its menu, its metadata, 
its DOM ids and its footnotes. This object is overloadable but MUST implement the
`\MarkdownExtended\API\ContentInterface` interface.

The second step is handled by the `\MarkdownExtended\Parser` object where lives the central
work of the syntax rules transformations. It depends on a configuration that can be reset
at every call. This object is overloadable but MUST implement the
`\MarkdownExtended\API\ParserInterface` interface.

Finally, the whole thing is contained in the `\MarkdownExtended\MarkdownExtended` object
that is a kind of global container for the Markdown work.

All API classes finally used to create objects are defined as configuration entries like:

```php
// the default API objects
'content_class'             => '\MarkdownExtended\Content',
'content_collection_class'  => '\MarkdownExtended\ContentCollection',
'parser_class'              => '\MarkdownExtended\Parser',
'templater_class'           => '\MarkdownExtended\Templater',
'grammar\filter_class'      => '\MarkdownExtended\Grammar\Filter',
'grammar\tool_class'        => '\MarkdownExtended\Grammar\Tool',
```

Please see the `\MarkdownExtended\Config` object source for a full and up-to-date list.


## FULL USAGE

### The "kernel" object

Creation of the container as a singleton instance:

```php
$mde = \MarkdownExtended\MarkdownExtended::create( options );
// to retrieve the same instance after creation:
$mde = \MarkdownExtended\MarkdownExtended::getInstance();
```

### The *Content* object

Creation of a new content object:

```php
// with a string:
$source = new \MarkdownExtended\Content( $string );

// with a file to get content from:
$source = new \MarkdownExtended\Content( null, $filepath );
```

If you configured your own object, use:

```php
// get or create your own object instance
$source = \MarkdownExtended\MarkdownExtended::get(
    'content', $config_as_an_array
);
```

### The *Parser* object

Get the parser instance from the container:

```php
$parser = $mde->get('Parser', $parser_options);    
```

If you configured your own object, use:

```php
// get or create your own object instance
$source = \MarkdownExtended\MarkdownExtended::get(
    'Parser', $parser_options_as_an_array
);
```

### The markdown process

Make the source transformation:

```php
// this will return the Container
$markdown = $parser->parse($source)
    // and this will return the Content object transformed
    ->getContent();
```

### The transformed content

Then, get the transformed content and other infos from the *Content* object:

```php
echo "<html><head>"
    .$markdown->getMetadataHtml()   // the content metadata HTML formatted
    ."</head><body>"
    .$markdown->getBody()           // the content HTML body
    ."<hr />"
    .$markdown->getNotesHtml()      // the content footnotes HTML formatted
    ."</body></html>";
```

In case of a simple source (such as a textarea field):

```php
echo $markdown->getBody();
```

For simplest calls, a *Helper* is designed to allow usage of:

```php
echo \MarkdownExtended\MarkdownExtended::getFullContent();
```

that will return the exact same string as the one constructed above (a full HTML page
by default).


## COMPONENTS

The Internal classes (required and not overloadable) are:

-   the API: `\MarkdownExtended\API`
-   the "kernel" object: `\MarkdownExtended\MarkdownExtended`
-   the configuration handler: `\MarkdownExtended\Config`
-   the registry (works as a container): `\MarkdownExtended\Registry`
-   the "output formatter" which depends on your chosen format: `\MarkdownExtended\OutputFormatBag`

The API classes, overloadables, are:

-   the "parser" who will handle all parsing steps: `\MarkdownExtended\Parser`,
    which must implement the `\MarkdownExtended\API\ParserInterface`
-   a "content" single object: `\MarkdownExtended\Content`,
    which must implement the `\MarkdownExtended\API\ContentInterface`
-   a collection of "contents": `\MarkdownExtended\ContentCollection`,
    which must implement the `\MarkdownExtended\API\CollectionInterface`
-   a "templater" object to load a parsed content in a template file: `\MarkdownExtended\Templater`,
    which must implement the `\MarkdownExtended\API\TemplaterInterface`

Each object is loaded as a service in the kernel and can be retrieved from the kernel instance
with a simple getter:

```php
$object = \MardownExtended\MarkdownExtended::getInstance()->get( name );
```

Trying to get it, if the object does not exist yet, it will be created.


## API KERNEL

The `\MarkdownExtended\API` is the central class object. It handles all the parsing
logic and acts like a services container for other API's objects.

The `\MarkdownExtended\MarkdownExtended` is the base public class object. It proposes
a large set of aliases to manage your contents (the original ones and their parsed results).

```php
// creation of the singleton instance of \MarkdownExtended\MarkdownExtended
$parser = \MarkdownExtended\MarkdownExtended::create( [options] );
```

The best practice is to use the kernel as a singleton instance but you are allowed to use
it as a "classic" object creating it like any other PHP object.

The *MarkdownExtended* package can be simply call writing:

```php
// creation of the singleton instance of \MarkdownExtended\MarkdownExtended
$content = \MarkdownExtended\MarkdownExtended::create()
    // get the \MarkdownExtended\Parser object passing it some options (optional)
    ->get('Parser', $options)
    // launch the transformation of a source content
    ->parse( new \MarkdownExtended\Content($source) )
    // get the result content object
    ->getContent();
```

This will load in *$content* the parsed HTML version of your original Markdown *$source*.
To get the part you need from the content, write:

```php
echo $content->getBody();
```

For simplest usage, some aliases are designed in the *MarkdownExtended* kernel:

```php
// to parse a string content:
\MarkdownExtended\MarkdownExtended::transformString($source [, $parser_options]);

// to parse a file content:
\MarkdownExtended\MarkdownExtended::transformSource($filename [, $parser_options]);
```

These two methods returns a *Content* object. To finally get an HTML
version, write:

```php
\MarkdownExtended\MarkdownExtended::transformString($source [, $parser_options]);
echo \MarkdownExtended\MarkdownExtended::getFullContent();
```

## SEE ALSO

php(1), pcre(3), markdown-extended(3)
