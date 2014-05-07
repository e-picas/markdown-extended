Markdown Extended Road-map
==========================

This document is for developers usage.


## TODOS that MUST be done before version 1.0

-   DONE - test the command line interface with direct stdin input, multi-input files etc
-   DONE - re-organize outputs from the command line interface (normal, verbose, quiet ...)
-   manage the replacement of in-text tags (`{% TOC %}` for instance)
-   test of different configuration sets (input fields, full files etc)
-   clarification of the rules : WRITE THE MANIFEST (?!) => anchors rules (!!)
-   find a better management for full HTML/body only return
-   make the code compliant with PHP MessDetector
-   embed a parser for the package "composer.json" to extract infos

## TODOS that SHOULD be done before version 1.0

-   manage the "markdown reminders" based on the `src/Resources/docs/` per-rule files
-   a unit test file for each syntax's rule
-   inform user that the Apache handler in the demo REQUIRES a config to fit actual server paths

## Known bugs

-   issue on footnotes (multi-reference causes a problem of multi-id) => OK, ticket 1
-   issue on tables with caption at its bottom => OK, ticket 2

## Evolutions

-   build a list of figures (?) : images, tables ...


----


## Development notes

Development of the `MarkdownExtended` namespace is done on repository branch named "wip".

To install the development environment, run:

    ~$ cd path/to/markdown-extended
    ~$ php path/to/composer install --dev

This will install [Sami](http://github.com/fabpot/sami), a PHP documentation generator, and
[PHPUnit](http://github.com/sebastianbergmann/phpunit/), a unit tester.

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

Or, you can simply run the following before a new version's commit:

    ~$ bash pre-commit-hook.sh


----
"**Markdown Extended ROADMAP**" - last updated at 08 june 2013

Creator & maintainer: Pierre Cassat <piero.wbmstr@gmail.com>.

Original source of this file, see <http://github.com/atelierspierrot/markdown-extended/ROADMAP.md>.

For comments & bugs, see <http://github.com/atelierspierrot/markdown-extended/issues>.
