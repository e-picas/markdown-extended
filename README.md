PHP Markdown Extended
=====================

A complete PHP 5.3 package for Markdown Extended syntax parsing.

**PHP Markdown Extended** is a PHP class to transform Markdown text files or strings in
HTML. This new version of a Markdown parser tries to propose a complete set of Markdown
syntax tags and rules and to be PHP-5.3 compliant.

[![Build Status](https://travis-ci.org/piwi/markdown-extended.svg?branch=master)](http://travis-ci.org/piwi/markdown-extended)

----

**WARNING - This package is still in development and not yet proposed in a "stable" version ;
some works remains before version 1.0. To get informed about the first stable version, you
can "Watch" the development by clicking the "Watch" button on the GitHub repository homepage
at <http://github.com/piwi/markdown-extended>.**

----

## Why a new Markdown parser?

-   This version tries to collect the different rules and tags used by original and various versions
    gleaned over the web.
-   It is a PHP script that can be used in any PHP project.
-   It is coded following the PHP 5.3 coding standards and is [Composer](http://getcomposer.org/)
    compliant (and ready).
-   It can be used via a command line interface, with a full set of options.
-   It can be configured for specific needs.
-   It is built to construct complex parsing by creating some single-key-unit objects for 
    all important Markdown stuff (content, configuration, parser, template and rules).

Finally, *PHP Markdown Extended* tries to implement Markdown following the
[**Markdown Extended specifications**](http://markdown-extended.github.io/manifest/) strictly.

## How-to

As for all our packages, we try to follow the coding standards and naming rules most
commonly in use:

-   the [PEAR coding standards](http://pear.php.net/manual/en/standards.php)
-   the [PHP Framework Interoperability Group standards](https://github.com/php-fig/fig-standards).

Knowing that, all classes are named and organized in an architecture to allow the use of 
the [standard SplClassLoader](https://gist.github.com/jwage/221634).

The whole package is embedded in the `MarkdownExtended` namespace.


### Installation

You can use this package in your work in many ways.

First, you can clone the [GitHub](http://github.com/piwi/markdown-extended)
repository and include it "as is" in your poject:

    ~$ wget --no-check-certificate http://github.com/piwi/markdown-extended

You can also download an [archive](http://github.com/piwi/markdown-extended/downloads)
from Github.

Then, to use the package classes, you just need to register the `MarkdownExtended`
namespace directory using the [SplClassLoader](https://gist.github.com/jwage/221634) or
any other custom autoloader (if required, a copy is proposed in the package):

    require_once 'path/to/package/src/SplClassLoader.php';
    $classLoader = new SplClassLoader('MarkdownExtended', '/path/to/package/src');
    $classLoader->register();

Another way to use the package, if you are a [Composer](http://getcomposer.org/) user,
is to add it to your requirements in your `composer.json`:

    "require": {
        ...
        "piwi/markdown-extended": "dev-master"
    }

The namespace will be automatically added to the project's Composer autoloader.

If you plan to use the parser as a single binary, you can use the PHAR archive directly,
which embeds the whole source as a standalone binary. Its usage is the same as the
`bin/markdown-extended` script described below.

### Usage

#### Usage for writers

To be compliant with the **extended** Markdown syntax, writers may construct their contents
following the rules described in the `markdown_reminders.html` file of the package ;
the latest version can be found at <http://sites.ateliers-pierrot.fr/markdown-extended/markdown_reminders.html>.

For a full example and a test file, you can refer to the `demo/MD_syntax.md` file of the package ;
the latest version can be found at <http://github.com/piwi/markdown-extended/blob/master/demo/MD_syntax.md>.

#### PHP script usage

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

A full PHP documentation of the last stable release can be found at
<http://docs.ateliers-pierrot.fr/markdown-extended/>.


#### Old parsers compatibility

To keep the package compatible with old versions of Markdown, an interface is embedded
with the common `Markdown($content)` function ; to use it, just include the file
`src/markdown.php`:

    require_once 'path/to/src/markdown.php';
    
    // to get result of a string parsing:
    echo Markdown($string [, $options]);

    // to get result of a file content parsing:
    echo MarkdownFromSource($file_name [, $options]);

This way, you may be able to change your Markdown parser without so much work and, we
hope so, a better result ;)

#### Command line usage

A short command line interface is proposed in the package running:

    ~$ bin/markdown-extended --help

This interface allows you to parse one or more files, extract some informations from sources,
write the results in files and some other stuff.

To generate a man-page from file `docs/MANPAGE.md` with the interface itself, run:

    ~$ bin/markdown-extended -f man -o bin/markdown-extended.man docs/MANPAGE.md
    ~$ man ./bin/markdown-extended.man


## Open-Source & Community

This plugin is a free software, available under [BSD license](http://en.wikipedia.org/wiki/BSD_licenses) ; 
you can freely use it, for yourself or a commercial use, modify its source code according
to your needs, freely distribute your work and propose it to the community, as long as you
let an information about its first authors.

As the sources are hosted on a [GIT](http://git-scm.com/) repository on
[GitHub](http://github.com/piwi/markdown-extended), you can modify it, to
ameliorate a feature or correct an error, by [creating your own fork](http://help.github.com/articles/fork-a-repo)
of this repository, modifying it and [asking to pull your modifications](http://github.com/piwi/markdown-extended/pulls)
on the original branch.

Please note that the "master" branch is **always the latest stable version** of the code. 
Development is done on branch "wip" and you can create a new one for your own developments.
A developer help and roadmap is provided [in the docs](docs/ROADMAP.md).
The latest version of the package documentation is available online at
<http://docs.ateliers-pierrot.fr/markdown-extended/>.

Note that the package is integrated with [Travis CI](http://travis-ci.org/).


## Licenses

This software, as the original Markdown, is licensed under the terms of the
[BSD-3-Clause open source license](http://opensource.org/licenses/BSD-3-Clause).
Please see the `LICENSE` file for a full text.

You can use, transform and distribute this software and its dependencies as you wish, as
long as you mention the copyrights below:

    Markdown  
    Copyright (c) 2003-2006 John Gruber   
    <http://daringfireball.net/>   
    All rights reserved.

    PHP Markdown & Extra  
    Copyright (c) 2004-2009 Michel Fortin  
    <http://michelf.com/>  
    All rights reserved.

    Multi Markdown  
    Copyright (c) 2005-2009 Fletcher T. Penney
    <http://fletcherpenney.net/>  
    All rights reserved.

    PHP Markdown Extended
    Copyright (c) 2008-2015 Pierre Cassat & contributors
    <http://e-piwi.fr/>  
    All rights reserved.
