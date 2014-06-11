Man:        markdown-extended Manual
Name:       MarkdownExtended
Author:     Les Ateliers Pierrot
Date: 2014-05-07
Version: 0.1-beta


## NAME

markdown-extended - PHP 5.3 parser for the Markdown syntax (extended version)


## SYNOPSIS

**markdown-extended  [options]  arguments**

**markdown-extended**  [**-h**|**-V**]  [**-x**|**-v**|**-q**|**-m**]
    [**-o** *filename*]  [**-c** *configfile*]  [**-f** *format*]
    [**-n** *a,b*]  [**-e**[=*block*]]  [**-g**[=*name*]] [**-t**[=*file*]]
    **input_filename**  [**input_filename**]  [...]
    "**markdown string read from STDIN**"


## DESCRIPTION

**Markdown Extended** converts markdown syntax text(s) source(s) from specified file(s)
(or STDIN). The rendering can be the full parsed content or just a part of this content.
By default, result is written through STDOUT in HTML format.

To transform a file content, write its path as script argument. To process a list of input
files, just write file paths as arguments, separated by space.

To transform a string read from STDIN, write it as last argument between double-quotes or EOF.
You can also use the output of a previous command using the pipe notation.


## OPTIONS

*The following options are supported:*

**-h** , **--help**
:   Get a simple help information.

**-V** , **--version**
:   Get the current package version number and informations.

**-v** , **--verbose**
:   Increase script's verbosity ; some steps are explained on STDOUT.

**-q** , **--quiet**
:   Decrease script's verbosity ; only result strings, Markdown Parser and PHP error
    messages are written on STDOUT.

**-m** , **--multi**
:   Treat multi-files input ; this option is automatically enables if multiple file
    names are found as arguments.

**-o**, **--output** =filename
:   Specify a single file name or a file name mask to write generated content in ; by
    default, files are generated in current working directory.

**-c** , **--config** =filename
:   Define a specific configuration filename to use for the Markdown Parser ;
    configuration files must be in `INI` format.

**-f** , **--format** =type
:   Define the output format to use to generate final rendering ; formats are stored in
    PHP namespace `\\MarkdownExtended\\OutputFormat` ; default is `HTML`.

**-g** , **--gamuts** [=name]
:   Define a single gamut or a list of gamuts to execute on Markdown content parsing.

**-t** , **--template** [=file]
:   Return the content inserted in a parsed template file ; if no `file` argument is 
    passed, this will use the configuration template file.

**-n** , **--nofilter** =name-a,name-b
:   Define a coma separated list of filters to disable during Markdown content parsing.

**-e** , **--extract** [=meta]
:   Define a content block to extract ; default extracted block is `metadata`.

*Some aliases are defined for quicker usage:*

**-b** , **--body**
:   Extract the `body` part from content(s) ; alias of option `-e=body`.

**-s** , **--simple**
:   Use the simple default configuration file defined by the `MarkdownExtended::SIMPLE_CONFIGFILE`
    constant ; this is a preset to treat contents coming from input fields.

A special '-x | --debug' option can be used during development ; it enables the `$debug`
flag of the PHP `\\MarkdownExtended\\CommandLine` namespace objects.

Use option '--man' to re-generate this manpage if possible.


## MESSAGES

The script output is designed to use options '-x' or '--verbose' to increase
script verbosity on STDOUT and '-q' or '--quiet' to decrease it. The idea is quiet simple:

-   in "**normal**" rendering (no "verbose" neither than "quiet" mode), the result of the 
    processed content is rendered, with the file name header in case of multi-files input
    and command line script's errors are rendered ;
-   in "**verbose**" mode, some process informations are shown, informing user about what is
    happening, helps to follow process execution and get some informations such as some
    string lengthes ; the command line script errors are rendered ;
-   in "**quiet**" mode, nothing is written through SDTOUT except result of parsed content(s) ;
    the command line script's errors are NOT rendered.

For all of these cases, PHP errors catched during Markdown Extended classes execution are
rendered depending on your environment `error_reporting` setting and script execution may
exit with a status code of '90'.


## ENVIRONMENT

This script requires PHP version 5.3.0 minimum.


## EXAMPLES

Classic parsing of the content of the Markdown syntax file `sample.md`:

    ~$ path/to/markdown-extended sample.md

For the same example, writing the output in file `sample_parsed.html`, run:

    ~$ path/to/markdown-extended -o sample_parsed.html sample.md

To extract meta-data from `sample.md`, run:

    ~$ path/to/markdown-extended -e sample.md

To build a man-page formated file from the Markdown source `man-sample.md`, run:

    ~$ path/to/markdown-extended -f man -o man-sample.man man-sample.md
    // to open it with `man`:
    ~$ man ./man-sample.man

To transform a string read from STDIN, run:

    ~$ path/to/markdown-extended -e=body "My **Markdown** string"

To transform a string read from another command output, run:

    ~$ echo "My **Markdown** string" | path/to/markdown-extended -e=body


## LICENSE

This software is released under the BSD-3-Clause open source license. Please
read the LICENSE file for more information, or see
<http://opensource.org/licenses/BSD-3-Clause>. 

PHP Markdown Extended - 
Copyright (c) 2008-2014 Pierre Cassat - 
<http://github.com/atelierspierrot/markdown-extended>

original MultiMarkdown - 
Copyright (c) 2005-2009 Fletcher T. Penney - 
<http://fletcherpenney.net/>

original PHP Markdown & Extra - 
Copyright (c) 2004-2012 Michel Fortin - 
<http://michelf.com/projects/php-markdown/>

original Markdown - 
Copyright (c) 2004-2006 John Gruber - 
<http://daringfireball.net/projects/markdown/>

## BUGS

To transmit bugs, see <http://github.com/atelierspierrot/markdown-extended/issues>.

## AUTHOR

**Les Ateliers Pierrot** <http://www.ateliers-pierrot.fr/> - Paris, France.

Created and maintained by **Pierre Cassat** (*piwi* - <http://github.com/piwi>)
& contributors.
