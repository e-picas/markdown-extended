Man:        PHP-Markdown-Extended Developer Manual
Man-name:   markdown-extended-api
Section:    7
Author:     Pierre Cassat
Date:       2024-02-24
Version:    1.0.0-rc.1


NAME
----

PHP-Markdown-Extended API - Developer documentation of the internal API of the "picas/markdown-extended" package.

The whole API is stored in the `\MarkdownExtended\API` namespace and is a set of interfaces
you must implement to use or override the parser or some of its objects.


PARSER OPTIONS
--------------

The parser can accept a large set of options to customize or adapt the final
rendering. For a complete list, please see the `getDefaults()` method
of [the `\MarkdownExtended\MarkdownExtended` class](http://docs.ateliers-pierrot.fr/markdown-extended/MarkdownExtended/MarkdownExtended.html).

Below is a review of interesting basic options:

config_file
:   Type: file path
:   Default: `null`
:   Define a configuration file to overwrite defaults ; configuration files may be
    in [INI](http://en.wikipedia.org/wiki/INI_file), [JSON](http://json.org/) or 
    raw [PHP array](http://php.net/array) formats (it must return an array).

template
:   Type: bool / 'auto' / file path
:   Default: `false` if the content has no metadata / `true` otherwise
:   If it is `true`, the default (basic) template is used, otherwise, the template
    defined at `file path` will be used. The default value is `auto`, which lets the
    parser choose if a template seems required (basically if the parsed content has
    metadata or not). You can set it on `false` to never use a template.

template_options
:   Type: array
:   Default: *see sources*
:   The template object options.

output_format
:   Type: string
:   Default: `html`
:   The output format to use to build final rendering.

output_format_options
:   Type: array
:   Default: *see sources*
:   The output formats options, stored by output formats type (`output_format_options.html = array( ... )`).

output
:   Type: string
:   Default: `null`
:   When it is defined, this option will be used to build a file name to write parsing result in ;
    it can be defined as a mask like `output-filename-%%.html` where `%%` will be filled with current
    content file path or title ; when parsing multiple inputs, 

Some options can be defined as *callbacks* to process a custom logic when necessary:

    my_option = function(arg) { ...; return $arg; }

PARSER FILTERS
--------------

Each filters' list executed while parsing a full content or a peace of a content
is defined as a `xxx_gamuts` array configuration entry with filter's name as
item's key and its order as item's value. Each gamut sub-item is constructed like 

    gamut_alias or class name : method or class name : method name
    
i.e.

    // call the default method of filter Detab
    'filter:Detab'              => '5',

    // call the init() method of filter Detab
    'filter:Detab:init'         => '10',

    // call the RemoveUtf8Marker tool
    'tool:RemoveUtf8Marker'     => '15',

    // call another gamuts stack
    'block_gamut'               => '30',

Below is a list of gamuts stacks used by the parser:

-   `initial_gamut`
-   `transform_gamut`
-   `document_gamut`
-   `span_gamut`
-   `block_gamut`
-   `html_block_gamut`

You can define and use a custom filter by defining a PHP object that is compliant with
the API (see below) and inserting it in a gamuts stack.


LIFE-CYCLE
----------

The full schema of a Markdown parser usage could be:

    [source file content]   [options]                                  [template]
           ||                  ||                                         /\
           \/                  \/                                         ||
    ---------------        ----------                            --------------------
    |  MD SOURCE  |   =>   | PARSER |   =>  [output format]  =>  | FORMATTED RESULT |
    ---------------        ----------                            --------------------
           /\                  /\                                         ||
           ||                  ||                                         \/
        [string]         [configuration]                            [special info]

The original Markdown source can be either a buffered string (a form field for example)
or the content of a Markdown file.

We want to parse the source content with a hand on options used during this parsing
(no need to parse metadata in a content that will never have some for example).

Finally, we want to get a formatted content and to be able to retrieve certain information
from it, such as its metadata, its footnotes or the whole parsed result.

Additionally, it would be best that we can obtain a full formatted result simply but
can also pass this result through a template builder to construct a complex final string.


PARSER API
----------

The public direct parser's access is the `\MarkdownExtended\MarkdownExtended`
object. It handles transformation and constructs dependencies. It returns a
parsed content as a `\MarkdownExtended\Content` object implementing the API
interface `\MarkdownExtended\API\ContentInterface`. The rendered final format
transformations are done by an object implementing the API interface
`\MarkdownExtended\API\OutputFormatInterface` which is called and managed
by the `\MarkdownExtended\OutputFormatBag` object. Internal available formats
are stored in the `\MarkdownExtended\OutputFormat` namespace. The filters applied
during content's parsing are managed by the `\MarkdownExtended\Grammar\Lexer`
object, which actually call various "gamuts" methods or classes using the
`\MarkdownExtended\Grammar\GamutsLoader` object. Each filter gamut is an
object implementing the API interface `\MarkdownExtended\API\GamutInterface`.
The parser can load parsed content in a template file using an object implementing
the API interface `\MarkdownExtended\API\TemplateInterface` and defaults to
the `\MarkdownExtended\Templater` object.

Finally, the internal central service container registering all the objects
involved in the parsing process is the `\MarkdownExtended\API\Kernel`, which
only contains static methods.

### Public *MarkdownExtended*

The public `\MarkdownExtended\MarkdownExtended` object follows a simple static API:

    \MarkdownExtended\MarkdownExtended::parse( content/file path , options ) : \MarkdownExtended\Content

    \MarkdownExtended\MarkdownExtended::parseString( content , options ) : \MarkdownExtended\Content

    \MarkdownExtended\MarkdownExtended::parseFile( file path , options ) : \MarkdownExtended\Content

These methods actually distribute the "real" work to the `\MarkdownExtended\Parser` 
object, which can be used as a literal procedural object like:

    $parser = new \MarkdownExtended\Parser( options );
    
    $content = $parser->transform( source string );
    
    $content = $parser->transformSource( source file path );

You can use both `\MarkdownExtended\Parser` and `\MarkdownExtended\MarkdownExtended`
objects in this case.

### The *Content* object 

The transformation process of the parser returns an object implementing interface
`\MarkdownExtended\API\ContentInterface`. You can define your own object by passing
it directly to the `\MarkdownExtended\MarkdownExtended` parse methods (instead of a
raw string or file name).

The content object API allows to access each "block" of content and
to write the object directly:

    string  Content::__toString()
    array   Content::__toArray()

    string  Content::getContent()

    string  Content::getCharset()
    string  Content::getTitle()
    string  Content::getBody()
    array   Content::getNotes()
    array   Content::getMetadata()

    string  Content::getNotesFormatted()
    string  Content::getMetadataFormatted()

    string  Content::getSource()
    array   Content::getParsingOptions()

The special `get...Formatted()` methods are designed to render a string from an array
of information (basically footnotes and metadata). Metadata follows a special logic
as the data with a name in the `special_metadata` option will be stripped from the
output (you can customize this option to use a metadata but not actually render it).

### The *Filters* objects

A filter must implement the `\MarkdownExtended\API\GamutInterface` interface 
and may extend the `\MarkdownExtended\Grammar\Filter` object:

    Filter->getDefaultMethod()
    Filter->transform( text )

Filters stacks to run during transformation are defined in the `xxx_gamut` items
of the configuration.

### The *OutputFormat* rendering

An output format renderer must implement the `\MarkdownExtended\API\OutputFormatInterface`
interface, which defines some basic methods to build a content:

    OutputFormat->buildTag( tag_name, content = null, array attributes = array() )

    OutputFormat->getTagString( content, tag_name, array attributes = array() )

The interface also defines two methods called to process the `get...Formatted()` logic
of the content:

    OutputFormat->getNotesToString( array notes , content )

    OutputFormat->getMetadataToString( array metadata , content )

### The *Template* renderer

A template object must implement the `\MarkdownExtended\API\TemplateInterface`
interface, which contains one single method:
 
    Template->parse( ContentInterface )

### The app's *Kernel*

It acts like a service container:

    \MarkdownExtended\Kernel::get('MarkdownExtended')   // the parser singleton
    \MarkdownExtended\Kernel::get('Content')            // current parsed content
    \MarkdownExtended\Kernel::get('ContentCollection')  // parsed contents collection
    \MarkdownExtended\Kernel::get('Lexer')              // grammar lexer
    \MarkdownExtended\Kernel::get('GamutLoader')        // grammar gamuts loader
    \MarkdownExtended\Kernel::get('OutputFormatBag')    // wrapper for current output format
    \MarkdownExtended\Kernel::get('Template')           // template engine
    \MarkdownExtended\Kernel::get('DomId')              // DOM registry manager

It also acts like a configuration setter/getter:

    // set a configuration entry
    \MarkdownExtended\Kernel::setConfig( index.subindex , value )

    // merge a configuration entry with any existing string or array value
    \MarkdownExtended\Kernel::addConfig( index.subindex , value )

    // get a configuration entry
    \MarkdownExtended\Kernel::getConfig( index.subindex )
    
    // apply a callback configuration entry on a list of parameters
    \MarkdownExtended\Kernel::applyConfig( index.subindex , parameters )
    
### Exceptions & errors

A full set of specific exceptions is defined in the app to differentiate external
and internal errors. Internal errors only defines a specific error code you can retrieve
with `$exception->getCode()` between 90 and 95:

-   *90* is the default exception thrown when invalid arguments are met
    (basic usage error - `\MarkdownExtended\Exception\InvalidArgumentException`)
-   *91* is the default exception thrown when a file or directory could not be found, read or written
    (`\MarkdownExtended\Exception\FileSystemException`)
-   *92* is the default exception thrown when an invalid value is met
    (deeper usage error - `\MarkdownExtended\Exception\UnexpectedValueException`)
-   *93* is the default exception thrown when an error occurred at runtime
    (`\MarkdownExtended\Exception\RuntimeException`)
-   *94* is the default exception status
    (`\MarkdownExtended\Exception\Exception`)
-   *95* is the default error-exception status
    (development error - `\MarkdownExtended\Exception\ErrorException`)


SEE ALSO
--------

An online documentation of last stable version is available at
<http://docs.ateliers-pierrot.fr/markdown-extended/>.

php(1), pcre(3), markdown-extended(3)
