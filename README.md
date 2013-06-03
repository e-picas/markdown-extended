PHP Markdown Extended
=====================

A complete PHP 5.3 version of the Markdown syntax parser.

**PHP Markdown Extended** is a PHP class to transform Markdown text files or strings in HTML. This new version of a Markdown parser tries to purpose a complete set of Markdown syntax tags and rules and to be PHP5 compliant.


## What is Markdown?

Created by [John Gruber](http://daringfireball.net/projects/markdown/) in 2004, **Markdown** is, as he says:

>    a text-to-HTML conversion tool for web writers. Markdown allows you 
>    to write using an easy-to-read, easy-to-write plain text format, then convert it 
>    to structurally valid XHTML (or HTML).

As I would say, **Markdown** is a set of writing rules to build some human readable text contents, such as `.txt` common files, which can be parsed to build some HTML valid content, structurally and typographically.

This syntax has become one of the most common standards of rich-text contents, used for example by [GitHub](http://github.com) as one of the proposed syntaxes for informational files (such like this one).


### A short history of Markdown

The first idea was from [John Gruber](http://daringfireball.net/), coded in *Perl* script.

An extended implementation, known as **Markdown Extra**, has been written by [Michel Fortin](http://michelf.com/), coded in *PHP* script.

Another extended implementation, known as **Multi Markdown**, has been written by [Fletcher T. Penney](http://fletcherpenney.net/), coded in *Perl* script.


### So why a new version of Markdown?

-   This version, known as **PHP Markdown Extended**, tries to collect the different rules and tags used by the three versions listed above.
-   It is a PHP script that can be used in any PHP project.
-   It can be used in command line interface, with a full set of options.
-   It can be configured for specific needs.


## How-to

As for all our work, we try to follow the coding standards and naming rules most commonly in use:

-   the [PEAR coding standards](http://pear.php.net/manual/en/standards.php)
-   the [PHP Framework Interoperability Group standards](https://github.com/php-fig/fig-standards).

Knowing that, all classes are named and organized in an architecture to allow the use of the
[standard SplClassLoader](https://gist.github.com/jwage/221634).

The whole package is embedded in the `MarkdownExtended` namespace.


### Installation

You can use this package in your work in many ways.

First, you can clone the [GitHub](https://github.com/atelierspierrot/markdown-extended) repository
and include it "as is" in your poject:

    https://github.com/atelierspierrot/markdown-extended

You can also download an [archive](https://github.com/atelierspierrot/markdown-extended/downloads)
from Github.

Then, to use the package classes, you just need to register the `MarkdownExtended` namespace directory
using the [SplClassLoader](https://gist.github.com/jwage/221634) or any other custom autoloader:

    require_once '.../src/SplClassLoader.php'; // if required, a copy is proposed in the package
    $classLoader = new SplClassLoader('MarkdownExtended', '/path/to/package/src');
    $classLoader->register();

If you are a [Composer](http://getcomposer.org/) user, just add the package to your requirements
in your `composer.json`:

    "require": {
        ...
        "atelierspierrot/markdown-extended": "dev-master"
    }

The namespace will be automatically added to the project Composer autoloader.


### Usage

#### PHP usage

The `MarkdownExtended` package can be simply call writing:

    // creation of the singelton instance of \MarkdownExtended\MarkdownExtended
    $content = \MarkdownExtended\MarkdownExtended::getInstance()
        // get the \MarkdownExtended\Parser object passing it some options (optional)
        ->get('\MarkdownExtended\Parser', $options)
        // launch the transformation of a source string
        ->transform($source);

This will load in `$content` the parsed HTML version of your original Markdown `$source`.

NOTE - To keep the package compatible with old versions of Markdown, an interface is 
embedded with the common `Markdown($content)` function ; to use it, just include the file
`src/markdown.php`.

#### Command line usage

A short CLI interface is proposed in the package running:

    ~$ bin/mde_console --help

The console allows you to parse one or more files, extract some informations from sources,
write the results in files and some other stuff.

#### Apache handler usage

A special direct [Apache](http://www.apache.org/) handler is designed in the `cgi-scripts/`
directory of the package. It allows you to automatically transform Markdown content files
in HTML thru a browser classic navigation. To learn more about this feature, please see the
dedicated [How-To](cgi-scripts/HOWTO.md).


## Licenses

This software, as the original Markdown, is licensed under the terms of the BSD open source license.

You can use, transform and distribute this software and its dependencies as you wish, as long as you mention the copyrights below:

    Markdown Extended
    Copyright © 2004-2013 Pierre Cassat & contributors
    All rights reserved.

    MultiMarkdown
    Copyright © 2005-2009 Fletcher T. Penney
    http://fletcherpenney.net/
    All rights reserved.

    PHP Markdown & Extra
    Copyright © 2004-2012 Michel Fortin
    http://michelf.com/projects/php-markdown/
    All rights reserved.

    Mardown
    Copyright © 2004-2006, John Gruber
    http://daringfireball.net/
    All rights reserved.

    Redistribution and use in source and binary forms, with or without modification, are permitted 
    provided that the following conditions are met:

    - Redistributions of source code must retain the above copyright notice, this list of conditions 
      and the following disclaimer.

    - Redistributions in binary form must reproduce the above copyright notice, this list of conditions 
      and the following disclaimer in the documentation and/or other materials provided with the distribution.

    - Neither the name “Markdown” nor the names of its contributors may be used to endorse or promote 
      products derived from this software without specific prior written permission.

    This software is provided by the copyright holders and contributors “as is” and any express or 
    implied warranties, including, but not limited to, the implied warranties of merchantability and 
    fitness for a particular purpose are disclaimed. In no event shall the copyright owner or contributors 
    be liable for any direct, indirect, incidental, special, exemplary, or consequential damages 
    (including, but not limited to, procurement of substitute goods or services; loss of use, data, or profits; 
    or business interruption) however caused and on any theory of liability, whether in contract, 
    strict liability, or tort (including negligence or otherwise) arising in any way out of the use of 
    this software, even if advised of the possibility of such damage.
