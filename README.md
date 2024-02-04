PHP Markdown Extended
=====================

Yet another PHP parser for the markdown (*extended*) syntax.

[![Community](https://img.shields.io/badge/Join%20the%20community-on%20GitHub%20Discussions-blue)](https://github.com/e-picas/markdown-extended/discussions)
[![License](http://poser.pugx.org/picas/markdown-extended/license)](https://packagist.org/packages/picas/markdown-extended)
[![PHP Version Require](http://poser.pugx.org/picas/markdown-extended/require/php)](https://packagist.org/packages/picas/markdown-extended)
[![Version](http://poser.pugx.org/picas/markdown-extended/version)](https://packagist.org/packages/picas/markdown-extended)
[![Last Release](https://img.shields.io/github/v/tag/e-picas/markdown-extended?sort=semver&label=last%20release)](https://github.com/e-picas/markdown-extended/releases)
[![Dependency Status](https://www.versioneye.com/user/projects/570e45aafcd19a00415b1287/badge.svg?style=flat)](http://www.versioneye.com/user/projects/570e45aafcd19a00415b1287)
[![Code Climate](https://codeclimate.com/github/e-picas/markdown-extended/badges/gpa.svg)](http://codeclimate.com/github/e-picas/markdown-extended)

[![Tests status PHP7](https://github.com/e-picas/markdown-extended/actions/workflows/test_php7.yml/badge.svg?branch=develop-php7)](https://github.com/e-picas/markdown-extended/actions/workflows/test_php7.yml)
[![Tests status PHP8](https://github.com/e-picas/markdown-extended/actions/workflows/test_php8.yml/badge.svg?branch=develop-php7)](https://github.com/e-picas/markdown-extended/actions/workflows/test_php8.yml)
[![Tests status PHP5](https://github.com/e-picas/markdown-extended/actions/workflows/test_php5.yml/badge.svg?branch=develop-php7)](https://github.com/e-picas/markdown-extended/actions/workflows/test_php5.yml)

[![PHP](https://img.shields.io/badge/PHP-5%2F7%2F8-e10079?logo=php)](https://www.php.net/)
[![Composer](https://img.shields.io/badge/Composer-grey?logo=composer)](https://getcomposer.org/)
[![Git Flow](https://img.shields.io/badge/GitFlow-grey?logo=git)](https://git-flow.readthedocs.io/en/latest/presentation.html)
[![Semantic Release](https://img.shields.io/badge/Semantic_Release-angular-e10079?logo=semantic-release)](https://semantic-release.gitbook.io/semantic-release/)
[![Docker](https://img.shields.io/badge/Docker-grey?logo=docker)](https://www.docker.com/)
[![GitHub actions](https://img.shields.io/badge/Github_Actions-grey?logo=github)](https://github.com/features/actions)
[![Conventional commits](https://img.shields.io/badge/Conventional_Commits-grey?logo=conventionalcommits)](https://www.conventionalcommits.org/en/v1.0.0/)

[![PHP Unit](https://img.shields.io/badge/testing-PHP_Unit-grey?style=social&logo=packagist)](https://phpunit.de/)
[![PHP CS Fixer](https://img.shields.io/badge/quality-PHP_CS_Fixer-grey?style=social&logo=packagist)](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer)
[![PHP Mess Detector](https://img.shields.io/badge/quality-PHP_Mess_Detector-grey?style=social&logo=packagist)](https://phpmd.org/)
[![PHP Metrics](https://img.shields.io/badge/quality-PHP_Metrics-grey?style=social&logo=packagist)](https://phpmetrics.org/)
[![PHP Doc](https://img.shields.io/badge/doc-PHP_Doc-grey?style=social&logo=packagist)](https://docs.phpdoc.org/)

----

**PHP Markdown Extended** is a PHP parser to transform [Markdown](http://en.wikipedia.org/wiki/Markdown) 
text files or strings in HTML or other formats. This new version of a Markdown parser tries to follow the 
[extended syntax specifications](http://manifest.aboutmde.org/) and is PHP-5.3 compliant
and highly customizable.

You can use this package in PHP scripts just as usual (for PHP apps) and also like a standalone command
line utility. The CLI interface is interactive with a large set of options and fully documented.

**README contents:**

-   [Installation](#installation)
    -   [Raw PHP package](#raw-php-package)
    -   [Using Composer](#using-composer)
    -   [Using a standalone version](#using-a-standalone-version)
        -   [Locally](#locally)
        -   [Personally](#personally)
        -   [Globally](#globally)
-   [Usage](#usage)
    -   [Usage for writers](#usage-for-writers)
    -   [Usage for developers](#usage-for-developers)
    -   [Command line usage](#command-line-usage)
    -   [Old parsers compatibility](#old-parsers-compatibility)
-   [Open-Source & Community](#open-source--community)
-   [License](#license)

----

Installation
------------

You can use this package in your work in many ways. Please note that it requires
a running [PHP](http://php.net/) version of 5.3.3 minimum.

### Raw PHP package

First, you can clone the [GitHub](http://github.com/e-picas/markdown-extended)
repository and include it "as is" in your project:

```bash
$ git clone https://github.com/e-picas/markdown-extended.git
```

You can also download an [archive](http://github.com/e-picas/markdown-extended/downloads)
from GitHub:

```bash
$ wget --no-check-certificate https://github.com/e-picas/markdown-extended/archive/master.tar.gz
$ tar -xvf master.tar.gz
```

Then, to use the package classes, you just need to include its *bootstrapper* which
will register its namespaces in current runtime environment:

```php
require_once 'path/to/package/src/bootstrap.php';
```

### Using Composer

Another way to use the package, if you are a [Composer](http://getcomposer.org/) user,
is to add it to your requirements in your `composer.json` file:

```json
"picas/markdown-extended": "dev-master"
```

The namespace will be automatically added to the project's Composer's *autoloader*.


### Using a standalone version

Finally, if you plan to use the parser as a single binary, you can use a 
[PHAR archive](http://php.net/manual/en/book.phar.php) directly, which embeds 
the whole source as a standalone binary (~220Kb). Its usage is the same as the 
`bin/markdown-extended` script [described below](#command-line-usage).

The archive is stored in a specific `phar-latest` branch on the repository:

```bash
$ wget --no-check-certificate https://github.com/e-picas/markdown-extended/archive/phar-latest.tar.gz
$ tar -xvf phar-latest.tar.gz
$ cd phar-latest
```

#### Locally

If you only need the archive for a local project, you can copy it where you
want:

```bash
$ cp bin/markdown-extended.phar your/project/path/
$ php your/project/path/markdown-extended.phar ...
```

#### Personally

To install the binary in your user's binaries:

```bash
$ ./install.sh ~/bin false
```

#### Globally

For a complete global install, run:

```bash
$ sudo ./install.sh /usr/local
```


Usage
-----

An HTML demonstration and the code documentation are available on the "dev" branch.

### Usage for writers

To be compliant with the **extended** Markdown syntax, writers may construct their contents
following the rules described at <http://cheatsheet.aboutmde.org/> (all basic markdown rules
are still available and valid).

For a full example and a test file, you can refer to the `demo/MD_syntax.md` file of the package ;
the latest version can be found at <http://github.com/e-picas/markdown-extended/blob/dev/demo/MD_syntax.md>.

### Usage for developers

The source code documentation of the last stable release can also be found online at
<http://docs.ateliers-pierrot.fr/markdown-extended/>.

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

This will load in `$content` the parsed version of your original Markdown 
source (file content or string).

The returned `$content` variable is actually a `\MarkdownExtended\API\ContentInterface` 
object but you can write it directly using:

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

You can also use the `\MarkdownExtended\Parser` object as a procedural non-static
object (this is in fact what the static methods above really do):

```php
// create an instance with custom options
$parser = new \MarkdownExtended\Parser( $options );

// parse a string
$content = $parser->transform( "my markdown string" );

// parse a file content
$content = $parser->transformSource( 'my-markdown-file.md' );
```

A more complete usage documentation is available in the package's documents
(`doc/DOCUMENTATION.md`). You can read it online at 
<https://github.com/e-picas/markdown-extended/blob/master/doc/DOCUMENTATION.md>.
Its *manpage* version is embedded in the package running:

```bash
$ man ./man/markdown-extended.7.man
```


### Command line usage

A command line interface is proposed with the package running:

```bash
$ ./bin/markdown-extended --help
```

The interface allows to parse one or more files, extract some information from sources,
write the results in files and some other stuff. A large set of options are available
to customize the transformation.

A complete *manpage* is available in the package's `man/` directory and its markdown source is
available in its documents (`doc/MANPAGE.md`). To read it, run:

```bash
$ man ./man/markdown-extended.3.man
```

The developer documentation is also available as a *manpage* running:

```bash
$ man ./man/markdown-extended.7.man
```

**Examples of cli usage:**

```bash
# transform a simple string
$ ./bin/markdown-extended "my **markdown** _extended_ string"
my <strong>markdown</strong> <em>extended</em> string

# transform a file content with output to STDOUT
$ ./bin/markdown-extended my-markdown-file.md
...

# transform a file content with output in file
$ ./bin/markdown-extended --output=my-transformed-markdown.html my-markdown-file.md
...

# generate the manpage itslef
$ ./bin/markdown-extended -f man -o man/markdown-extended.man doc/MANPAGE.md
...
```

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


Open-Source & Community
-----------------------

This parser is a free software, available under [BSD license](http://en.wikipedia.org/wiki/BSD_licenses) ; 
you can freely use it, for yourself or a commercial use, modify its source code according
to your needs, freely distribute your work and propose it to the community, as long as you
let an information about its first authors.

As the sources are hosted on a [GIT](http://git-scm.com/) repository on
[GitHub](http://github.com/e-picas/markdown-extended), you can modify it, to
ameliorate a feature or correct an error. Please read the [`CONTRIBUTING.md`
file of the package](https://github.com/e-picas/markdown-extended/blob/master/CONTRIBUTING.md) 
for more info.


License
-------

This software, as the original Markdown, is licensed under the terms of the
[BSD-3-Clause license](http://opensource.org/licenses/BSD-3-Clause).
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
    Copyright (c) 2008-2024 Pierre Cassat & contributors
    <http://picas.fr/>  
    All rights reserved.
