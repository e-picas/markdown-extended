# PHP Markdown Extended changelog

# [1.0.0-rc.2](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.1...v1.0.0-rc.2) (2024-02-04)


### Features

* force a new release ([c6e6d73](https://github.com/e-picas/markdown-extended//commit/c6e6d73ea1c2ef9b6dfa47d41a914f9eb83889c2))

# 1.0.0-rc.1 (2024-02-04)


### Bug Fixes

* **ci:** add a CI script for running tests silently ([1486a61](https://github.com/e-picas/markdown-extended//commit/1486a6125bfd9e30e1284fd6b53a39b7ab775287))
* **ci:** force to use PHP 7 in CI ([1f2bf0b](https://github.com/e-picas/markdown-extended//commit/1f2bf0b9e20f1ce8f406244463927aa8de04184f))
* **ci:** try to not write anything for simple testing ([3253061](https://github.com/e-picas/markdown-extended//commit/325306181ac8fd6eac48edf5f10433eab6b39adc))
* **code-fixer:** new run of PHPCS ([30462ee](https://github.com/e-picas/markdown-extended//commit/30462eebab99b78011647e1119e79f3e8176f448))
* **code:** try to be PHP8 compliant ([eec71af](https://github.com/e-picas/markdown-extended//commit/eec71af45bf81edba785b8b4feb435a7947d21c0))
* fi/then/else in Makefile ([c9d601c](https://github.com/e-picas/markdown-extended//commit/c9d601c44a2d7fe454efcc0123ef27db76effaaa))
* full renaming the author info ([0bde62d](https://github.com/e-picas/markdown-extended//commit/0bde62d8422fb632e23f267ce3ed1085a4b1b562))
* full renaming the author info ([2cc4f51](https://github.com/e-picas/markdown-extended//commit/2cc4f5177f7d76630269396cd41f654620ff6568))
* **php7:** strip typing throwing errors (maybe PHP8 only?) ([4bee740](https://github.com/e-picas/markdown-extended//commit/4bee7407678310877201aee1f7e39f4ce2dc300a))
* **type:** rollback to Exception type for the 'handleException' method ([8f35eac](https://github.com/e-picas/markdown-extended//commit/8f35eacde6fc95eed05277b7758500480fc55f7e))


### Features

* add a test runner for PHP5 ([2bbf2ae](https://github.com/e-picas/markdown-extended//commit/2bbf2ae93669d0b8433c5c402bd5ccbd291b78ff))
* add a test runner for PHP8 ([81005c9](https://github.com/e-picas/markdown-extended//commit/81005c9f18989d37b40f83e149d3167e40d0bb33))
* be php 5 compilant ([e043e82](https://github.com/e-picas/markdown-extended//commit/e043e8219a3e6ef48e0582eb698e5ee072a2ee1d))
* **chore:** review the dev tools: phpcs, phpdoc, phpmd, phpunit ([7ba2e57](https://github.com/e-picas/markdown-extended//commit/7ba2e5744cba3d1da92566bbf32a5af3daf0b323))
* **ci:** create release from 'develop' ([f8f0117](https://github.com/e-picas/markdown-extended//commit/f8f01174ac5ed50dd0fd4d90e0ad335068bc65ec))
* **ci:** no more Travis, replaced by GitHub Actions with Semantic Release ([c7f5365](https://github.com/e-picas/markdown-extended//commit/c7f5365f9cdfcfb99fa7c8c6ba60214644329f61))
* **php7-migration:** push the project back to life ([6607973](https://github.com/e-picas/markdown-extended//commit/6607973d9f326ee0d94a5dc38908c9c27c16de4d))
* **php7:** run the code standard fixer with PHP7 standards ([052b79d](https://github.com/e-picas/markdown-extended//commit/052b79d975cc3d99cbdff16de2b31659032aa713))
* **test:** review of tests with groups and information about the manifest part treated ([d9fed44](https://github.com/e-picas/markdown-extended//commit/d9fed44685f388107d5d79631795017b38d0d785))
* **tests:** review of unit tests for a better display ([6b06456](https://github.com/e-picas/markdown-extended//commit/6b064566c8fad033823fe86f99cdc847e8ceb49c))

# CHANGELOG for old history (before version 1.0.0)


* (upcoming release)

    * 5afb3da - review of the 'auto' templating logic (picas)
    * 1767efa - rename 'Gramar\GamutsLoader' service to 'GamutsLoader' (picas)

* v0.1.0-delta (2015-04-16 - b2c1a05)

    * b7d515f - prepare version 0.1.0-delta (picas)
    * 223930a - new 'make-release' dev action (picas)
    * 19cbc6d - manage the case where no default template is set (inline template instead) (picas)
    * 164210f - externalization of the work in Parser (picas)
    * 38f3100 - review of filters & API (picas)
    * abf5629 - rename manpages with section number (picas)
    * d7cbdef - [REFOUND] large review of the code (picas)
    * 7ade2d9 - usage of '@stable' development dependencies (picas)
    * 7ae5dfc - usage of my fork of the SplClassLoader (picas)
    * 30ca234 - improve bootstraper inclusion in console (picas)
    * 0a2f2f1 - rename of 'docs' to 'doc' (picas)
    * 88c1f61 - more comprehensive 'slugification' process (picas)
    * f415f4a - fix #9: differentiate inline and block maths (picas)
    * a04ea4f - always add an ID for referenced images (picas)
    * 6cccbc5 - review of the builders (picas)

* v0.1.0-gamma.5 (2015-01-03 - 89a6358)

    * 00f793a - uniformization of EOL to LF (some files were CRLF) (picas)
    * 5f861a3 - review of phpunit tests (picas)
    * fa8045c - inversion of email/url treatments (url must be treated last) (picas)

* v0.1-gamma4 (2014-12-26 - 9a76856)

    * c93a433 - add the manpages in composer binaries (automatically added in project's 'bin/') (picas)
    * 8fdb149 - new ignored files for tag tarballs (picas)
    * 2540f42 - force cherry-picking of #75ce621 (picas)
    * f813b0c - fix CRLF in md file (picas)
    * 10a45fb - adding the @package info to the whole classes (picas)
    * abb4c29 - fix list of meta-characters in Config.php (picas)
    * 9d24bd6 - adding the MDE specs info (picas)
    * 5a2f8de - auto-escaping the '>' following rule A7 (picas)
    * 6ecfbd7 - adding the '>' escaped character in configuration following spec D2 (picas)
    * cb87ab9 - do not verify the link IS an url (no protocol) (picas)
    * c984b5f - allow backticks for fenced code blocks (picas)
    * 618f52e - fix #6: we now transform Sextet in ATX and let the ATX be parsed ... (picas)
    * ff28275 - light-weight demo review using CDN for jQuery, Bootstrap and Font Awesome (picas)
    * dc55796 - new Syntax documentation (first try - to be continued) (picas)
    * b8187c9 - new 'replace' info in composer.json (old versions) (picas)

* v0.1.0-gamma.3 (2014-07-18)

    * 7553a50 - new feature: Maths (from http://github.com/drdrang/php-markdown-extra-math) (picas)

* v0.1.0-gamma.2 (2014-06-28)

    * 5681190 - new versions following commit 7edee42 (picas)
    * 6bb94bb - transferring ownership of the repo - renaming the package in 'picas/markdown-extended' (picas)

* v0.1.0-gamma (2014-06-14)
* v0.1.0-beta (2014-05-07)

    * 4a48411 - Fixing files rights (picas)
    * ec67a2f - Full and reviewed license (picas)
    * 68c3f1c - New special title for anchors links (in page links) (picas)
    * 9d2b65b - Skipping the new "email" attribute in HTML link tags (picas)
    * 95b6101 - Managing the email encoding when it's not wanted (picas)

* v0.1.0-alpha (2013-10-21)

    * f9bef62 - Merging wip for Helper.php (picas)
    * 568f971 - Adding PHPMD to the requirements (picas)
    * 98078a7 - Working on the MAN format (picas)
    * 85fa166 - Correction in BlockQuote (picas)
    * 8729c27 - Renaming "LICENSE" (picas)
    * 96bccb0 - New demo + console models (picas)
    * 9fbb21c - New helper methods (picas)
    * cf3def2 - Renaming all gamuts with correct cases (picas)
    * 5292ee0 - Corrections in gamuts cases for case-sensitive OS (picas)
    * d6e8010 - Corrections in the Console and README file (picas)
    * 3ff7f07 - New Sami documentation config (picas)
    * 1dea462 - Corrections and Penney as author (picas)
    * 2c4d5ac - New strategy in the demo (picas)

* MarkdownExtended-OO-v1.0 (2012-03-03 - fe1d093)

    * d5645c9 - Preparing tag OO version 1.0 (picas)
    * 2c72757 - Fully Object Oriented version (picas)
    * dc5e2b3 - Renaming of the project to 'PHP Extended Markdown' (picas)
    * 0928955 - I add a cheat sheet tool (HTML) (picas)
    * 1a7d957 - Attributes for images and links are OK (picas)
    * 85e7cda - Refund of the index to test MultiMarkdown (picas)
    * 6ee67b0 - First version of the all code (picas)
