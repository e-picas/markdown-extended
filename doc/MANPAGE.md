Man:        PHP-Markdown-Extended Manual
Man-name:   markdown-extended
Author:     Pierre Cassat
Date:       2015-04-16
Version:    0.1.0-dev


## NAME

PHP-Markdown-Extended - Yet another PHP parser for the markdown (*extended*) syntax.


## SYNOPSIS

**markdown-extended**  [*options*]  (*--*)  [*arguments*]

**markdown-extended**  [**-V**|**--version**]  [**-h**|**--help**]
    [**-x**|**-v**|**-q**] [**--debug**|**--verbose**|**--quiet**|**--force**]
    [**-o**|**--output** *filename*] [**--no-output**]
    [**-c**|**--config** *filename*]
    [**-f**|**--format** *format*]
    [**-r**|**--response** *type*]
    [**-e**|**--extract** [=*block*]]
    [**-t**|**--template** [=*filename*]] [**--no-template**]
        *input_filename*  [*input_filename*]  [...]
        "*markdown string read from STDIN*"


## DESCRIPTION

**PHP-Markdown-Extended** converts markdown-extended syntax text(s) source(s) from specified file(s)
(or STDIN). The rendering can be the full parsed content or just a part of this content.
By default, result is written through STDOUT in HTML format.

To transform a file content, write its path as script argument. To process a list of input
files, just write the concerned paths as arguments, separated by a space.

To transform a string read from STDIN, write it as last argument between quotes or EOF.
To process a list of input strings, just write them as arguments, separated by a space.
You can also use the output of a previous command with the pipe notation.

For more information about the **markdown extended syntax**, see <http://aboutmde.org/>.

Developers (or curious people) can refer to <markdown-extended(7)> for an internal
**PHP-Markdown-Extended** documentation.

## OPTIONS

### Usage of script's options

You can group short options like `-xc`, set an option argument like `-d(=)value` or
`--long=value` or `--long value` and use the double-dash notation `--` to explicitly 
specify the end of the script options. You can mix short and long options at your 
convenience.

The equal sign separator for an option with argument IS REQUIRED when this argument
is optional (see the list below). It is NOT required when the option requires an
argument.

Options are treated in the command line order (`-vq` will finally retain `-q`).

### The following options are supported by the CLI interface:

**-h** , **--help**
:   Get a simple help information.

**-V** , **--version**
:   Get the current package version number and information ; use option **quiet** to
get only the version number.

**-r** , **--response** *type*
:   Specify the CLI response type to get in *plain* (default), *json* or *php* ; using
    another type than "plain" will render the full content object (not just the parsed content) ;
    using the "php" response type will render a serialization of concerned contents.

**-e** , **--extract** [=*meta*]
:   Define a content block to extract ; default extracted block is *metadata* ; you can
    extract any "block" of the content object ; use a metadata name to extract its value.

**-q** , **--quiet**
:   Decrease script's verbosity ; only result strings, Markdown parser and PHP error
    messages are written on *STDOUT* or *STDERR* ; this mode disables **verbose** one.

**-v** , **--verbose**
:   Increase script's verbosity ; some steps are explained on *STDOUT* ; this mode
disables **quiet** one.

A special **--debug** or **-x** option can be used during development to drastically
increase script's verbosity.

### The following options are loaded in the markdown parser:

**-c** , **--config** *filename*
:   Define a specific configuration filename to use for the Markdown parser ;
    configuration files must be in *INI* or *JSON* format.

**-f** , **--format** *type*
:   Define the output format to use to generate final rendering ; internal formats 
    are "html" and "man" (for manpage) ; you can specify your own output format class ; 
    default is *html*.

**-o** , **--output** *filename*
:   Specify a single file name or a file names mask to write generated content(s) in ; by
    default, files are generated in current working directory ; masks may use the *%%* string
    which will be fill in with content's identifier.

**--no-output**
:   Prohibits to write the result in a file.

**-t** , **--template** [=*filename*]
:   Return the content inserted in a parsed template file ; if no **filename** argument is 
    passed, this will use the configuration template file.

**--no-template**
:   Prohibits use of a template.

**--force**
:   Use this to not backup generated files (the default behavior is to backup all existing files
    to a "FILENAME.EXT~YYYY-MM-DD-HH-II-SS" file).

## RESULT

The command result can have various types. Actually, for all the types described below, the
*--response* option will define the final response content type.

With no *--extract* neither *--output* option defined, the command will render a `Content` 
object with transformed content. In fact, if you use the default "plain" response type, the
result will write the rendering content (a raw string) on the terminal. If you specify the
"json" or "php" response type, the full object will be dumped, with the following items:

-   *content*: the final rendered content ; this can be the "body" only for a simple one-line
    markdown content, the "metadata + body + notes" as a string for a more complex markdown content
    and the rendering of the parsed template if a *--template* option was used ;
-   *body*: the actual "body" of the parsed content, without metadata and notes ;
-   *notes*: the footnotes of the content (if so) as an array ;
-   *metadata*: the metadata of the content (if so) as an array ;
-   *charset*: the defined character set of the content ;
-   *title*: the guessed title of the content.

If you use a *--output* option, the content described above will be written in a file and the result
rendered on terminal will be the name of this file.

When you use the *PHP* response output, you will mostly have a serialized object as output.
To rebuild the original object, you will need to include its definition:

    markdown-extended -r=php demo/MD_syntax.md > test-php.txt
    php -r 'require "src/bootstrap.php"; $obj = file_get_contents("test-php.txt"); var_export(unserialize($obj));'

### Templating

The templating system of PHP-Markdown-Extended is a simple processor that will replace
a tag like `{% BODY %}` by its value for concerned content. Such notation can be used for
all content's items listed above: *body*, *title*, *charset*, *meta* (*metadata* as string) and
*notes* (as string).

The metadata follows a specific rule as you can access each data value by its name with a
tag like `{% META:data_name %}`.


## MESSAGES

The script output is designed to use options **-v** or **--verbose** to increase
script verbosity and **-q** or **--quiet** to decrease it. The idea is quiet simple:

-   in "**normal**" rendering (no "verbose" neither than "quiet" mode), the result of the 
    processed content is rendered, with the file name header in case of multi-files input
    and command line script's errors are rendered ;
-   in "**verbose**" mode, some process information are shown, informing user about what is
    happening, helps to follow process execution and get some information such as some
    string lengths ; the command line script errors are rendered ;
-   in "**quiet**" mode, nothing is written through SDTOUT except result of parsed content(s) ;
    the command line script's errors are NOT rendered.

For all of these cases, PHP errors caught during Markdown Extended classes execution are
rendered depending on your environment *error_reporting* setting and script execution may
exit with an error status code between *90* and *95*.


## ENVIRONMENT

This script requires [PHP version 5.3.3](http://php.net/) minimum with the
[PCRE extension](http://php.net/manual/en/book.pcre.php) (this is the case
by default).


## EXAMPLES

Classic parsing of the content of the Markdown syntax file `sample.md`:

    path/to/markdown-extended sample.md

For the same example, writing the output in file `sample_parsed.html`, run:

    path/to/markdown-extended -o sample_parsed.html sample.md

To extract meta-data from `sample.md`, run:

    path/to/markdown-extended -e sample.md

To build a man-page formatted file from the Markdown source `man-sample.md`, run:

    path/to/markdown-extended -f man -o man-sample.man man-sample.md
    // to open it with `man`:
    man ./man-sample.man

To transform a string read from STDIN, run:

    path/to/markdown-extended -e=body "My **Markdown** string"

To transform a string read from another command output, run:

    echo "My **Markdown** string" | path/to/markdown-extended -e=body


## LICENSE

This software is released under the BSD-3-Clause license. Please
read the LICENSE file for more information, or see
<http://opensource.org/licenses/BSD-3-Clause>. 

PHP Markdown Extended - 
Copyright (c) 2008-2015 Pierre Cassat - 
<http://e-piwi.fr/>

Based on MultiMarkdown - 
Copyright (c) 2005-2009 Fletcher T. Penney - 
<http://fletcherpenney.net/>

Based on PHP Markdown Lib - 
Copyright (c) 2004-2012 Michel Fortin - 
<http://michelf.com/>

Based on Markdown - 
Copyright (c) 2004-2006 John Gruber - 
<http://daringfireball.net/>

## BUGS

To transmit bugs, see <http://github.com/piwi/markdown-extended/issues>.

## AUTHOR

Created and maintained by Pierre Cassat (piwi - <http://e-piwi.fr/>) & contributors.

## SEE ALSO

php(1), pcre(3), markdown-extended(7)
