Markdown Extended Road-map
==========================

This document is for developers usage.


## TODOS that MUST be done before version 1.0

-   DONE - test the command line interface with direct stdin input, multi-input files etc
-   DONE - re-organize outputs from the command line interface (normal, verbose, quiet ...)
-   DONE - TO BE TESTED - manage the replacement of in-text tags (`{% TOC %}` for instance)
-   TO BE TESTED - test of different configuration sets (input fields, full files etc)
-   find a better management for full HTML/body only return
-   embed a parser for the package "composer.json" to extract infos

-   clarification of the rules : WRITE THE MANIFEST (?!) => anchors rules (!!)
        => this will now be done at <https://github.com/markdown-extended/manifest>

## TODOS that SHOULD be done before version 1.0

-   DONE - manage the "markdown reminders" based on the `src/Resources/docs/` per-rule files
-   ONGOING - a unit test file for each syntax's rule
-   inform user that the Apache handler in the demo REQUIRES a config to fit actual server paths

## Known bugs

-   issue on footnotes (multi-reference causes a problem of multi-id) => OK, ticket 1
-   issue on tables with caption at its bottom => OK, ticket 2
-   command line: error on multiple input in template (only the first one is fetched in template)

## Evolutions

-   make the code compliant with PHP MessDetector (! big stuff)
-   build a list of figures (?) : images, tables ...


----


## Development notes

Development of the `MarkdownExtended` namespace is done on the repository branch named "dev".

To install the development environment, run:

    ~$ cd path/to/markdown-extended
    ~$ php path/to/composer install --dev

This will install [Sami](http://github.com/fabpot/sami), a PHP documentation generator, and
[PHPUnit](http://github.com/sebastianbergmann/phpunit/), a unit tester.


### Development life-cycle

As said above, all development MUST be done on the `dev` branch of the repository. Doing so we
can commit our development features to let users using a clone test and improve them.

When the work gets a stable stage, it seems to be time to build and publish a new release. This
is done by creating a tag named like `vX.Y.Z[-status]`[^1] from the "master" branch after having
merged your updates.


### New release step-by-step

Creating a release, you MAY go on the following steps:

-   BEFORE a release, on the "dev" branch:
    -   the best practice is to rebuild the `markdown_reminders` page before each release
    -   you MAY run the PHPUnit tests to check current work
-   CREATING the release:
    -   merge "dev" into "master"
    -   you MUST use the `pre-commit-hook.sh` to create a release (the best practice is to
        use the `piwi/dev-tools` package to do so) ; this MAY do:
        -   update the version number in the `src/MarkdownExtended/MarkdownExtended.php` file
            and in the `docs/MANPAGE.php` file
        -   regenerate the `bin/markdown-extended.man` manpage from the `docs/MANPAGE.md` file
        -   commit and push all these modifications
        -   create a new tag with version number name
    -   publish the release by pushing it to GitHub
-   AFTER a release:
    -   as the `PHAR` archive is NOT under version control, you MUST build it first in the "dev" branch,
        then manually add it on "master"
    -   merge "master" into "dev"
    -   you MUST update the PHP documentation of the "dev" branch and commit it

Each release-push to GitHub MAY update the <http://sites.ateliers-pierrot.fr/markdown-extended/> and
<http://docs.ateliers-pierrot.fr/markdown-extended/> websites.


## How-tos

### Generate the "PHAR" archive

To automatically re-generate the "markdown-extended.phar" file from current version, you can use
the `bin/compile` binary of the "dev" branch:

    ~$ php build/make-phar.php


### Generate the "markdown reminders"

To automatically re-generate the "markdown_reminders.html" file from current version, you can use
the `bin/build_reminders` binary:

    ~$ php build/make-reminders.php


### Generate the man-page

To generate the manpage of the `bin/markdown-extended` binary (and suddenly also the PHAR archive),
run:

    ~$ bin/markdown-extended -f man -o bin/markdown-extended.man docs/MANPAGE.md
    ~$ man ./bin/markdown-extended.man

Please note that some systems REQUIRED to use the equal sign between option name and value:

    ~$ bin/markdown-extended -f=man -o=bin/markdown-extended.man docs/MANPAGE.md


### Generate the documentation

You can (re-)generate a full PHP documentation, any time, running:

    ~$ php bin/sami.php render/update sami.config.php

NOTE - we figured that for some versions of Sami, if the documentation does not exist, you
need to first run the `render` action AND then the `update` one at the same time.

The documentation is built in a `phpdoc/` directory in the package, and requires a temporary
directory for its generation that is configured on:

    path/to/markdown-extended/../tmp/cache/markdown-extended/

You can modify this setting editing the `sami.config.php` file.

The documentation is not under version control (except for the "dev" branch of the stable
version).


### Launch unit-tests

You can verify that your package passes all tests running:

    ~$ php bin/phpunit


### Mess detection

You can check code mess running:

    ~$ php bin/phpmd src text codesize


### MarkdownExtended auto-update

For auto-update of the Container class constants and the command line interface MANUAL based
on the actual `composer.json` values, a pre-commit hook is defined in `pre-commit-hook.sh`.
To use it, run:

    ~$ mkdir .git/hooks && cp pre-commit-hook.sh .git/hooks/pre-commit
    ~$ chmod +x .git/hooks/pre-commit

The hook is also compliant with the <http://github.com/piwi/dev-tools> package using
its `version-tag` tool.


[^1]: Please see the [Semantic Versioning](http://semver.org/) work by Tom Preston-Werner for
more info about the release version name construction rules.

----
"**Markdown Extended ROADMAP**" - last updated at 26 december 2014

Creator & maintainer: [@pierowbmstr](http://e-piwi.fr/).

Original source of this file, see <http://github.com/piwi/markdown-extended/ROADMAP.md>.

For comments & bugs, see <http://github.com/piwi/markdown-extended/issues>.
