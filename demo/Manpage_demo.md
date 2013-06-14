Man:        markdown_extended Manual
Name:       MarkdownExtended
Author:     Les Ateliers Pierrot
Date:       June 14, 2013
Version:    0.0.5

## NAME

**Markdown Extended**

A complete PHP 5.3 package of Markdown syntax parser (extended version) - Command line interface

## SYNOPSIS

**markdown_extended**  [**-h**|**-V**]  [**-x**|**-v**|**-q**|**-m**]
    [**-o** *filename*]  [**-c** *configfile*]  [**-f** *format*]
    [**--gamuts**[=*name*]]  [**--nofilter**=*a,b*]  [**--extract**[=*block*]]
    **input_filename**  [**input_filename**]  [...]

## DESCRIPTION

**Markdown Extended** converts text(s) in specified file(s) (or stdin) from
markdown syntax source(s). The rendering can be the full parsed content or
just a part of this content. By default, result is written through STDOUT in
HTML format.

## OPTIONS

The following options are supported:

**-h** , **--help**
:   Get a simple help information.

**-V** , **--version**
:   Get the current package version number.

**-x** , **--verbose**
:   Increase script's verbosity ; some steps are explained on STDOUT.

**-q** , **--quiet**
:   Decrease script's verbosity ; only result strings, Markdown Parser and PHP error
    messages are written on STDOUT.

**-m**, **--multi**
:   Treat multi-files input ; this options is automatically enables if multiple file
    names are found as arguments.

**-o**, **--output** = *FILENAME*
:   Specify a single file name or a file name mask to write generated content in ; by
    default, files are generated in current working directory.

**-c** , **--config** = *FILENAME*
:   Define a specific configuration filename to use for the Markdown Parser ;
    configuration files must be in `INI` format.

**-f** , **--format** = *TYPE*
:   Define the output format to use to generate final rendering ; formats are stored in
    PHP namespace `\\MarkdownExtended\\OutputFormat` ; default is `HTML`.

**--gamuts** [= *NAME*]
:   Define a single gamut or a list of gamuts to execute on Markdown content parsing.

**--nofilter** = *NAME-A,NAME-B*
:   Define a coma separated list of filters to disable during Markdown content parsing.

**--extract** [= *META*]
:   Define a content block to extract ; default extracted block is `metadata`.

## EXAMPLES

Classic parsing of the content of the Markdown syntax file `sample.md`:

    ~$ path/to/markdown_extended sample.md

For the same example, writing the output in file `sample_parsed.html`, run:

    ~$ path/to/markdown_extended -o sample_parsed.html sample.md

To extract meta-data from `sample.md`, run:

    ~$ path/to/markdown_extended --extract sample.md

To build a man-page formated file from the Markdown source `man-sample.md`, run:

    ~$ path/to/markdown_extended -f man -o man-sample.man man-sample.md
    // to open it with `man`:
    ~$ man ./man-sample.man


## LICENSE

This software is released under the BSD-3-Clause open source license. Please
read the License.text file for more information, or see
<http://opensource.org/licenses/BSD-3-Clause>. 

PHP Markdown Extended
Copyright (c) 2008-2013 Pierre Cassat

original MultiMarkdown
Copyright (c) 2005-2009 Fletcher T. Penney
<http://fletcherpenney.net/>

original PHP Markdown & Extra
Copyright (c) 2004-2012 Michel Fortin
<http://michelf.com/projects/php-markdown/>

original Markdown
Copyright (c) 2004-2006 John Gruber
<http://daringfireball.net/projects/markdown/>

## BUGS

To transmit bugs, see <https://github.com/atelierspierrot/markdown-extended/issues>.

## AUTHOR

**Les Ateliers Pierrot** <http://www.ateliers-pierrot.fr/>
