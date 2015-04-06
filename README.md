PHP Markdown Extended
=====================

Yet another PHP parser for the markdown (*extended*) syntax.

**PHP Markdown Extended** is a PHP class to transform Markdown text files or strings in
HTML or other formats. This new version of a Markdown parser tries to follow the 
[extended syntax specifications](http://manifest.aboutmde.org/) and is PHP-5.3 compliant
and highly customizable.

[![Build Status](https://travis-ci.org/piwi/markdown-extended.svg?branch=master)](http://travis-ci.org/piwi/markdown-extended)
[![Dependency Status](https://www.versioneye.com/user/projects/550e3650bc1c12efc3000067/badge.svg?style=flat)](http://www.versioneye.com/user/projects/550e3650bc1c12efc3000067)
[![Code Climate](https://codeclimate.com/github/piwi/markdown-extended/badges/gpa.svg)](http://codeclimate.com/github/piwi/markdown-extended)
[![Test Coverage](https://codeclimate.com/github/piwi/markdown-extended/badges/coverage.svg)](http://codeclimate.com/github/piwi/markdown-extended)


Installation
------------

You can use this package in your work in many ways.

First, you can clone the [GitHub](http://github.com/piwi/markdown-extended)
repository and include it "as is" in your poject:

    ~$ wget --no-check-certificate http://github.com/piwi/markdown-extended

You can also download an [archive](http://github.com/piwi/markdown-extended/downloads)
from Github.

Then, to use the package classes, you just need to include its *bootstrapper* which
will register its namespaces to PHP using:

```php
require_once 'path/to/package/src/bootstrap.php';
```

Another way to use the package, if you are a [Composer](http://getcomposer.org/) user,
is to add it to your requirements in your `composer.json`:

```json
"piwi/markdown-extended": "dev-master"
```

The namespace will be automatically added to the project's Composer autoloader.

If you plan to use the parser as a single binary, you can use the PHAR archive directly,
which embeds the whole source as a standalone binary. Its usage is the same as the
`bin/markdown-extended` script described below.

    $ curl -O http://releases.aboutmde.org/markdown-extended-php/markdown-extended.phar
    $ chmod a+x markdown-extended.phar
    $ ./markdown-extended.phar --help


Usage
-----

### Usage for writers

To be compliant with the **extended** Markdown syntax, writers may construct their contents
following the rules described at <http://cheatsheet.aboutmde.org/>.

For a full example and a test file, you can refer to the `demo/MD_syntax.md` file of the package ;
the latest version can be found at <http://github.com/piwi/markdown-extended/blob/dev/demo/MD_syntax.md>.

### PHP script usage

The `MarkdownExtended` package can be simply call writing:

```php
use \MarkdownExtended\MarkdownExtended;     // load the namespace

$options = array( /* ... */ );              // parser options, see documentation

// parse a string or a file content
$content = MarkdownExtended::parse( "my markdown string" OR 'my-markdown-file.md' , $options );

// parse a string
$content = MarkdownExtended::parseString( "my markdown string" , $options );

// parse a file content
$content = MarkdownExtended::parseSource( 'my-markdown-file.md' , $options );

```

This will load in `$content` the parsed version of your original Markdown source (file content or string).

The returned `$content` variable is actually a `\MarkdownExtended\API\ContentInterface` object but you can
write it directly using:

```php
echo $content;          // shortcut for $content->getContent()
```

To get the part you need from the content, write:

```php
$content
    ->getContent()      // the full content
    ->getCharset()      // a guessed character set
    ->getTitle()        // the guessed title
    ->getBody()         // the body
    ->getMetadata()     // the metadata as array
    ->getNotes()        // the notes as array
;
```

You can also use it as a procedural non-static object:

```php
// create an instance with custom options
$parser = new \MarkdownExtended\MarkdownExtended( $options );

// parse a string
$content = $parser->transform( "my markdown string" );

// parse a file content
$content = $parser->transformSource( 'my-markdown-file.md' );
```

A full PHP documentation of the last stable release can be found at
<http://docs.aboutmde.org/markdown-extended-php/>.

### Old parsers compatibility

To keep the package compatible with old versions of Markdown, an interface is embedded
with the common `Markdown($content)` function ; to use it, just include the file
`src/markdown.php` of the package:

```php
require_once 'path/to/src/markdown.php';

// to get result of a string parsing:
echo Markdown($string [, $options]);

// to get result of a file content parsing:
echo MarkdownFromSource($file_name [, $options]);
```

This way, you may be able to change your Markdown parser without so much work and, 
I hope so, a better result ;)

### Command line usage

A command line interface is proposed in the package running:

    ~$ ./bin/markdown-extended --help

The interface allows to parse one or more files, extract some information from sources,
write the results in files and some other stuff.

A full *manpage* should be available in the package:

    ~$ man ./man/markdown-extended.man

To re-generate the man-page from file `doc/MANPAGE.md` with the interface itself, run:

    ~$ ./bin/markdown-extended -f man -o man/markdown-extended.man doc/MANPAGE.md
    ~$ man ./man/markdown-extended.man


Open-Source & Community
-----------------------

This parser is a free software, available under [BSD license](http://en.wikipedia.org/wiki/BSD_licenses) ; 
you can freely use it, for yourself or a commercial use, modify its source code according
to your needs, freely distribute your work and propose it to the community, as long as you
let an information about its first authors.

As the sources are hosted on a [GIT](http://git-scm.com/) repository on
[GitHub](http://github.com/piwi/markdown-extended), you can modify it, to
ameliorate a feature or correct an error, by [creating your own fork](http://help.github.com/articles/fork-a-repo)
of this repository, modifying it and [asking to pull your modifications](http://github.com/piwi/markdown-extended/pulls)
on the original branch.

Please note that the "master" branch is **always the latest stable version** of the code. 
Development is done on branch "dev" and you can create a new one for your own developments.
A developer help and roadmap is provided [in the docs](docs/ROADMAP.md).
The latest version of the package documentation is available online at
<http://docs.aboutmde.org/markdown-extended-php/>.

Note that the package is integrated with [Travis CI](http://travis-ci.org/).


Licenses
--------

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
