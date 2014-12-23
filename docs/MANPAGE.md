Man:        PHP-Markdown-Extended Manual
Man-name:   markdown-extended
Author:     Pierre Cassat
Date: 2014-06-13
Version: 0.1-gamma


## NAME

PHP-Markdown-Extended - A PHP parser for the Markdown Extended syntax


## SYNOPSIS

**markdown-extended**  [*options*]  (*--*)  [*arguments*]

**markdown-extended**  [**-h**|**-V**]  [**--help**|**--version**]
    [**-x**|**-v**|**-q**|**-m**] [**--debug**|**--verbose**|**--quiet**|**--multi**]
    [**-o**|**--output** *filename*]
    [**-c**|**--config** *filename*]
    [**-f**|**--format** *format*]
    [**-n**|**--nofilter** *a,b*]
    [**-e**|**--extract** [=*block*]]
    [**-g**|**--gamuts** [=*name*]]
    [**-t**|**--template** [=*filename*]]
        *input_filename*  [*input_filename*]  [...]
        "*markdown string read from STDIN*"


## DESCRIPTION

**Markdown Extended** converts markdown-extended syntax text(s) source(s) from specified file(s)
(or STDIN). The rendering can be the full parsed content or just a part of this content.
By default, result is written through STDOUT in HTML format.

To transform a file content, write its path as script argument. To process a list of input
files, just write file paths as arguments, separated by space.

To transform a string read from STDIN, write it as last argument between double-quotes or EOF.
You can also use the output of a previous command using the pipe notation.

For more information about the Markdown-Extended syntax, see <http://aboutmde.org/>.

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

### The following options are supported:

**-c** , **--config** *filename*
:   Define a specific configuration filename to use for the Markdown Parser ;
    configuration files must be in *INI* format.

**-e** , **--extract** [*meta*]
:   Define a content block to extract ; default extracted block is *metadata*.

**-f** , **--format** *type*
:   Define the output format to use to generate final rendering ; formats are stored in
    PHP namespace `\MarkdownExtended\OutputFormat` ; default is *HTML*.

**-g** , **--gamuts** [*name*]
:   Define a single gamut or a list of gamuts to execute the content transformation.

**-h** , **--help**
:   Get a simple help information.

**-m** , **--multi**
:   Treat multi-files input ; this option is automatically enables if multiple file
    names are found as arguments.

**-n** , **--nofilter** *name-a,name-b*
:   Define a coma separated list of filters to disable during the content transformation.

**-o**, **--output** *filename*
:   Specify a single file name or a file name mask to write generated content in ; by
    default, files are generated in current working directory.

**-q** , **--quiet**
:   Decrease script's verbosity ; only result strings, Markdown Parser and PHP error
    messages are written on *STDOUT* ; this mode disables the **verbose** one.

**-t** , **--template** [*filename*]
:   Return the content inserted in a parsed template file ; if no **file** argument is 
    passed, this will use the configuration template file.

**-V** , **--version**
:   Get the current package version number and information ; use option **quiet** to
get only the version number.

**-v** , **--verbose**
:   Increase script's verbosity ; some steps are explained on *STDOUT* ; this mode
disables the **quiet** one.

### Some aliases are defined for quicker usage:

**-b** , **--body**
:   Extract the *body* part from content(s) ; alias of option **--extract=body**.

**-s** , **--simple**
:   Use the simple default configuration file defined by the `\MarkdownExtended\MarkdownExtended::SIMPLE_CONFIGFILE`
    constant ; this is a preset to treat contents coming from input fields.

### Special options

A special **--debug** or **-x** option can be used during development ; it enables the *$debug*
flag of the PHP `\MarkdownExtended\CommandLine` namespace objects.

Use option **--man** to re-generate this manpage if possible.


## MESSAGES

The script output is designed to use options **-v** or **--verbose** to increase
script verbosity on *STDOUT* and **-q** or **--quiet** to decrease it. The idea is quiet simple:

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
exit with a status code of *90*.


## ENVIRONMENT

This script requires [PHP version 5.3.0](http://php.net/) minimum with the 
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

This software is released under the BSD-3-Clause open source license. Please
read the LICENSE file for more information, or see
<http://opensource.org/licenses/BSD-3-Clause>. 

PHP Markdown Extended - 
Copyright (c) 2008-2014 Pierre Cassat - 
<http://e-piwi.fr/>

original MultiMarkdown - 
Copyright (c) 2005-2009 Fletcher T. Penney - 
<http://fletcherpenney.net/>

original PHP Markdown & Extra - 
Copyright (c) 2004-2012 Michel Fortin - 
<http://michelf.com/>

original Markdown - 
Copyright (c) 2004-2006 John Gruber - 
<http://daringfireball.net/>

## BUGS

To transmit bugs, see <http://github.com/piwi/markdown-extended/issues>.

## AUTHOR

Created and maintained by Pierre Cassat (piwi - <http://e-piwi.fr/>) & contributors.

## SEE ALSO

php(1), pcre(3)
