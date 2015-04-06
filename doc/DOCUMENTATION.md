Man:        PHP-Markdown-Extended Developer Manual
Man-name:   markdown-extended-api
Section:    7
Author:     Pierre Cassat
Date:       27-12-2014
Version:    0.1-gamma4


NAME
----

Markdown-Extended-PHP API - Developer documentation of the internal API of the "piwi/markdown-extended" package.

The whole API is stored in the `\MarkdownExtended\API` namespace and is a set of interfaces
you must implement to use or override the parser or some of its objects.


PARSER OPTIONS
--------------

The parser can accept a large set of options to customize or adapt the final
rendering. For a complete list, please see the `getDefaults()` method
of [the `\MarkdownExtended\MarkdownExtended` class](http://docs.aboutmde.org/markdown-extended-php/MarkdownExtended/MarkdownExtended.html).

Below is a review of interesting basic options:

template
:   Type: bool / 'auto' / file path
:   Default: `false` if the content has no metadata / `true` otherwise
:   If it is `true`, the default (basic) template is used, otherwise, the template
    defined at `file path` will be used. The default value is `auto`, which let the
    parser choose if a template seems required (basically if the parsed content has
    metadata or not). You can set it on `false` to never use a template.

config_file
:   Type: file path
:   Default: `null`
:   Define a configuration file to overwrite defaults ; configuration files may be
    in [INI](http://en.wikipedia.org/wiki/INI_file), [JSON](http://json.org/) or 
    raw [PHP array](http://php.net/array) formats (it must return an array).

output_format
:   Type: string
:   Default: `html`
:   The output format to use to build final rendering.

output
:   Type: string
:   Default: `null`


LIFE-CYCLE
----------

The full schema of a Markdown parser usage could be:

    [source file content]   [options]    
           ||                  ||
           \/                  \/
    ---------------        ----------                            -------------------
    |  MD SOURCE  |   =>   | PARSER |   =>  [output format]  =>  | FORMATTED RESULT |
    ---------------        ----------                            -------------------
           /\                  /\                                         ||
           ||                  ||                                         \/
        [string]        [ configuration ]                           [special info]

The original Markdown source can be either a buffered string (a form field for example)
or the content of a Markdown file

We want to parse the source content with a hand on options used during this parsing
(no need to parse metadata in a content that will never have some for example)

Finally, we want to get a formatted content and to be able to retrieve certain information
from it, such as its metadata, its menu or the footnotes of the whole parsed result

Additionally, it would be best that we can obtain a full formatted result simply but
can also pass this result through a template builder to construct a complex final string


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

It also proposes a literal procedural usage API:

    $parser = new \MarkdownExtended\MarkdownExtended( options );
    
    $content = $parser->transform( source string );
    
    $content = $parser->transformSource( source file path );

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

### The *Filters* objects

A filter must implement the `\MarkdownExtended\API\GamutInterface` interface 
and may extend the `\MarkdownExtended\Grammar\Filter` object:

    Filter->getDefaultMethod()
    Filter->transform( text )

Filters stacks to run during transformation are defined in the `xxx_gamut` items
of the configuration.

### The *OutputFormat* rendering

An output format renderer must implement the `\MarkdownExtended\API\OutputFormatInterface`
interface defines some basic methods to build a content:

    OutputFormat->buildTag( tag_name, content = null, array attributes = array() )

    OutputFormat->getTagString( content, tag_name, array attributes = array() )

### The *Template* renderer

A template object must implement the `\MarkdownExtended\API\TemplateInterface`
interface, which contains one single method:
 
    Template->parse( ContentInterface )

### The app's *Kernel*

It acts like a service container:

    \MarkdownExtended\Kernel->get('MarkdownExtended')
    \MarkdownExtended\Kernel->get('Content')
    \MarkdownExtended\Kernel->get('ContentCollection')
    \MarkdownExtended\Kernel->get('Lexer')
    \MarkdownExtended\Kernel->get('Grammar\GamutLoader')
    \MarkdownExtended\Kernel->get('OutputFormatBag')
    \MarkdownExtended\Kernel->get('Template')
    \MarkdownExtended\Kernel->get('DomId')

It also acts like a configuration setter/getter:

    \MarkdownExtended\Kernel::setConfig( index.subindex , value )
    \MarkdownExtended\Kernel::addConfig( index.subindex , value )
    \MarkdownExtended\Kernel::getConfig( index.subindex )
    

SEE ALSO
--------

An online documentation of last stable version is available at
<http://docs.aboutmde.org/markdown-extended-php/>.

php(1), pcre(3), markdown-extended(3)
