# PHP Markdown Extended changelog

# 1.0.0-rc.1 (2024-02-24)


### Bug Fixes

* arrayIterator + null string warnings ([ea09c57](https://github.com/e-picas/markdown-extended//commit/ea09c57cdc7c50e134b999650f2338933a0e8f31))
* break the inifinite loop of bump versions on master ([0ca2883](https://github.com/e-picas/markdown-extended//commit/0ca2883da921ef16dba6b2b308ab2949c66c1450))
* **ci:** add a CI script for running tests silently ([bf79b81](https://github.com/e-picas/markdown-extended//commit/bf79b8194b5780bc402ba6804770435c2b5644ec))
* **ci:** force to use PHP 7 in CI ([a52bc4f](https://github.com/e-picas/markdown-extended//commit/a52bc4fde74fc1d26ecb274a2375768f069bd4ca))
* **ci:** try to not write anything for simple testing ([1e8ef53](https://github.com/e-picas/markdown-extended//commit/1e8ef53827c9d1e14af5a1531e65869c86b1410d))
* **code-fixer:** new run of PHPCS ([a795a7f](https://github.com/e-picas/markdown-extended//commit/a795a7f83d6bbc553dae9bbf035f2112e945718b))
* **code:** try to be PHP8 compliant ([b487130](https://github.com/e-picas/markdown-extended//commit/b4871306469f15d821211041d7fff6e6448815f4))
* fi/then/else in Makefile ([9570828](https://github.com/e-picas/markdown-extended//commit/9570828746f14e3cd63eba625267c9f99aef283a))
* full renaming the author info ([0bde62d](https://github.com/e-picas/markdown-extended//commit/0bde62d8422fb632e23f267ce3ed1085a4b1b562))
* full renaming the author info ([2cc4f51](https://github.com/e-picas/markdown-extended//commit/2cc4f5177f7d76630269396cd41f654620ff6568))
* migrate configuration from 'git' to 'semantic-release/github' plugin ([0535e3e](https://github.com/e-picas/markdown-extended//commit/0535e3eec2b7a7d7a80c359abaab3e8449021c90))
* **php7:** strip typing throwing errors (maybe PHP8 only?) ([11149a3](https://github.com/e-picas/markdown-extended//commit/11149a3049183fbe9229485ccbc2ee6c93c92d33))
* review the document title extraction & fix tests ([#17](https://github.com/e-picas/markdown-extended/issues/17)) ([4fe606f](https://github.com/e-picas/markdown-extended//commit/4fe606f2eb7c2c5e03a2046b78acf196c90ef2fc))
* rollback the composer plugin of semantic-release as it seems abandoned ([f841cd1](https://github.com/e-picas/markdown-extended//commit/f841cd114964f6514048c8385328df950f789968))
* try to build the phar when publishing ([1a4f1b5](https://github.com/e-picas/markdown-extended//commit/1a4f1b5c462823daaedcc3287fdacf2daf3713e9))
* try to build the phar when publishing (step 2) ([ea2ad0e](https://github.com/e-picas/markdown-extended//commit/ea2ad0e5f68c58659e7a2c319ad944f890783bab))
* try to build the phar when publishing (step 3) ([cc625a4](https://github.com/e-picas/markdown-extended//commit/cc625a45105a13bb26ebf607aeda4c7c4bba634a))
* try to build the phar when publishing (step 4) ([129224a](https://github.com/e-picas/markdown-extended//commit/129224aa3cbb58b0610c75d462f667c74d136100))
* try to build the phar when publishing (step 5) ([c8872a6](https://github.com/e-picas/markdown-extended//commit/c8872a6ebdc9c5dead799f86ec9afac0a3871901))
* try to make a commit when publishing ([599c3ed](https://github.com/e-picas/markdown-extended//commit/599c3edfc80b3faddd7d4dd2fc33819308093aaa))
* try to make a commit when publishing (step 2) ([f59f93b](https://github.com/e-picas/markdown-extended//commit/f59f93bef037e260016fe6147b8137dd9e5481ba))
* **type:** rollback to Exception type for the 'handleException' method ([d9c5072](https://github.com/e-picas/markdown-extended//commit/d9c50728d6745b490fe7e5bf2398658f52936d1a))
* use the raw version parameter for the 'make-release' internal action ([16c80ce](https://github.com/e-picas/markdown-extended//commit/16c80ce4de2a0d08c36012f7bff3c6586b98872e))
* use the raw version parameter for the 'make-release' internal action (step 2) ([ffa3221](https://github.com/e-picas/markdown-extended//commit/ffa3221c46618c5cb437a301d12849bc200e0213))


### Features

* add a new dockerfile for a complete dev image ([61b9427](https://github.com/e-picas/markdown-extended//commit/61b94275ea9e5ba488ede4435a2cb43a37a43e0f))
* add a test runner for PHP5 ([db27428](https://github.com/e-picas/markdown-extended//commit/db27428bd82d92c53f0a4ef0de2176a1fb48d592))
* add a test runner for PHP8 ([72ae813](https://github.com/e-picas/markdown-extended//commit/72ae8131ee495447d8fd4545d794fc97b7b83480))
* be php 5 compilant ([53549f7](https://github.com/e-picas/markdown-extended//commit/53549f716a76a2d8b37447c1913587410c0b42b1))
* **chore:** review the dev tools: phpcs, phpdoc, phpmd, phpunit ([f464a31](https://github.com/e-picas/markdown-extended//commit/f464a3166bfbe1c2a10cc25822874d28d400e6b7))
* **ci:** create release from 'develop' ([af118e3](https://github.com/e-picas/markdown-extended//commit/af118e38b6abb3c3d4ac29c376ea77ca900e7540))
* **ci:** no more Travis, replaced by GitHub Actions with Semantic Release ([62304ed](https://github.com/e-picas/markdown-extended//commit/62304ede39686119df50d8f22219e3a0951a509a))
* force a new release ([4977fc2](https://github.com/e-picas/markdown-extended//commit/4977fc29f19ff85f18ba67b22ed4ff2e3bf0fe8b))
* make some cleanup after experiencing a real version bump ([cebc803](https://github.com/e-picas/markdown-extended//commit/cebc803372120b932ce6230887c4843c7a44fb84))
* **php7-migration:** push the project back to life ([faf8844](https://github.com/e-picas/markdown-extended//commit/faf8844d1f9efc5bef8d6d83fd3b76416f2cd64a))
* **php7:** run the code standard fixer with PHP7 standards ([5a34609](https://github.com/e-picas/markdown-extended//commit/5a34609dd84d739b468404b1f862e03313fcf3b1))
* send tests coverage to codeclimate ([00272e5](https://github.com/e-picas/markdown-extended//commit/00272e58c03aa09d7014d54ec0ddbf200cdb205a))
* **test:** review of tests with groups and information about the manifest part treated ([e115230](https://github.com/e-picas/markdown-extended//commit/e115230ecfa76f88fcbfc1d7e0b1e0f78928ed03))
* **tests:** review of unit tests for a better display ([ad4d0d3](https://github.com/e-picas/markdown-extended//commit/ad4d0d38d2fce16c2e7411d0f7c411789bfaf4cf))

# [1.0.0-rc.12](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.11...v1.0.0-rc.12) (2024-02-12)


### Bug Fixes

* review the document title extraction & fix tests ([#17](https://github.com/e-picas/markdown-extended/issues/17)) ([b9b3926](https://github.com/e-picas/markdown-extended//commit/b9b3926901865a1d9f99f646b6e6093d40e14d61))

# [1.0.0-rc.11](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.10...v1.0.0-rc.11) (2024-02-04)


### Features

* send tests coverage to codeclimate ([324d226](https://github.com/e-picas/markdown-extended//commit/324d2269fdd7131d57435d5e0466aaee042cec00))

# [1.0.0-rc.10](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.9...v1.0.0-rc.10) (2024-02-04)


### Features

* make some cleanup after experiencing a real version bump ([066589a](https://github.com/e-picas/markdown-extended//commit/066589a8e4cf523b5725af9ad68784857a4cd7aa))

# [1.0.0-rc.9](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.8...v1.0.0-rc.9) (2024-02-04)


### Bug Fixes

* try to build the phar when publishing ([05e66bc](https://github.com/e-picas/markdown-extended//commit/05e66bcd6ec7d9e8c93785e167df2474a9ff438b))
* try to build the phar when publishing (step 2) ([a01ab19](https://github.com/e-picas/markdown-extended//commit/a01ab1924ea8cc300960c119f0c0290058d925fe))
* try to build the phar when publishing (step 3) ([67ebc2e](https://github.com/e-picas/markdown-extended//commit/67ebc2e3b8aa38b01b671042c260d502cc578f6a))
* try to build the phar when publishing (step 4) ([ef89d78](https://github.com/e-picas/markdown-extended//commit/ef89d78906910ab962ed24521ad2ef0e5170dbb0))
* try to build the phar when publishing (step 5) ([5d98371](https://github.com/e-picas/markdown-extended//commit/5d983711c7dbed99ef34e4b4d85cef638aa905f6))

# [1.0.0-rc.8](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.7...v1.0.0-rc.8) (2024-02-04)


### Bug Fixes

* try to make a commit when publishing (step 2) ([1ebcb30](https://github.com/e-picas/markdown-extended//commit/1ebcb300bd44bb30e57f6bca91f51f39cbb67ed9))

# [1.0.0-rc.6](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.5...v1.0.0-rc.6) (2024-02-04)


### Bug Fixes

* break the inifinite loop of bump versions on master ([811dac3](https://github.com/e-picas/markdown-extended//commit/811dac382a31a888dd2e74a18fb4cdfeaefbe2a6))

# [1.0.0-rc.5](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.4...v1.0.0-rc.5) (2024-02-04)


### Features

* add a new dockerfile for a complete dev image ([7874683](https://github.com/e-picas/markdown-extended//commit/78746830342c7fb00847f0807a810152917cf460))

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
* migrate configuration from 'git' to 'semantic-release/github' plugin ([ed6de37](https://github.com/e-picas/markdown-extended//commit/ed6de3780e228888ef2831778cdd734a2950cc59))
* **php7:** strip typing throwing errors (maybe PHP8 only?) ([4bee740](https://github.com/e-picas/markdown-extended//commit/4bee7407678310877201aee1f7e39f4ce2dc300a))
* rollback the composer plugin of semantic-release as it seems abandoned ([288742c](https://github.com/e-picas/markdown-extended//commit/288742c7a53e008f04977402b63c5fc165893c72))
* **type:** rollback to Exception type for the 'handleException' method ([8f35eac](https://github.com/e-picas/markdown-extended//commit/8f35eacde6fc95eed05277b7758500480fc55f7e))
* use the raw version parameter for the 'make-release' internal action ([a1e0c0f](https://github.com/e-picas/markdown-extended//commit/a1e0c0fded745ef3740a82065d76fb57898d2100))
* use the raw version parameter for the 'make-release' internal action (step 2) ([bc8b562](https://github.com/e-picas/markdown-extended//commit/bc8b562cec7cbc95b4662c56a77c311474fce368))


### Features

* add a test runner for PHP5 ([2bbf2ae](https://github.com/e-picas/markdown-extended//commit/2bbf2ae93669d0b8433c5c402bd5ccbd291b78ff))
* add a test runner for PHP8 ([81005c9](https://github.com/e-picas/markdown-extended//commit/81005c9f18989d37b40f83e149d3167e40d0bb33))
* be php 5 compilant ([e043e82](https://github.com/e-picas/markdown-extended//commit/e043e8219a3e6ef48e0582eb698e5ee072a2ee1d))
* **chore:** review the dev tools: phpcs, phpdoc, phpmd, phpunit ([7ba2e57](https://github.com/e-picas/markdown-extended//commit/7ba2e5744cba3d1da92566bbf32a5af3daf0b323))
* **ci:** create release from 'develop' ([f8f0117](https://github.com/e-picas/markdown-extended//commit/f8f01174ac5ed50dd0fd4d90e0ad335068bc65ec))
* **ci:** no more Travis, replaced by GitHub Actions with Semantic Release ([c7f5365](https://github.com/e-picas/markdown-extended//commit/c7f5365f9cdfcfb99fa7c8c6ba60214644329f61))
* force a new release ([c6e6d73](https://github.com/e-picas/markdown-extended//commit/c6e6d73ea1c2ef9b6dfa47d41a914f9eb83889c2))
* **php7-migration:** push the project back to life ([6607973](https://github.com/e-picas/markdown-extended//commit/6607973d9f326ee0d94a5dc38908c9c27c16de4d))
* **php7:** run the code standard fixer with PHP7 standards ([052b79d](https://github.com/e-picas/markdown-extended//commit/052b79d975cc3d99cbdff16de2b31659032aa713))
* **test:** review of tests with groups and information about the manifest part treated ([d9fed44](https://github.com/e-picas/markdown-extended//commit/d9fed44685f388107d5d79631795017b38d0d785))
* **tests:** review of unit tests for a better display ([6b06456](https://github.com/e-picas/markdown-extended//commit/6b064566c8fad033823fe86f99cdc847e8ceb49c))

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
* migrate configuration from 'git' to 'semantic-release/github' plugin ([ed6de37](https://github.com/e-picas/markdown-extended//commit/ed6de3780e228888ef2831778cdd734a2950cc59))
* **php7:** strip typing throwing errors (maybe PHP8 only?) ([4bee740](https://github.com/e-picas/markdown-extended//commit/4bee7407678310877201aee1f7e39f4ce2dc300a))
* rollback the composer plugin of semantic-release as it seems abandoned ([288742c](https://github.com/e-picas/markdown-extended//commit/288742c7a53e008f04977402b63c5fc165893c72))
* **type:** rollback to Exception type for the 'handleException' method ([8f35eac](https://github.com/e-picas/markdown-extended//commit/8f35eacde6fc95eed05277b7758500480fc55f7e))
* use the raw version parameter for the 'make-release' internal action ([a1e0c0f](https://github.com/e-picas/markdown-extended//commit/a1e0c0fded745ef3740a82065d76fb57898d2100))
* use the raw version parameter for the 'make-release' internal action (step 2) ([bc8b562](https://github.com/e-picas/markdown-extended//commit/bc8b562cec7cbc95b4662c56a77c311474fce368))


### Features

* add a test runner for PHP5 ([2bbf2ae](https://github.com/e-picas/markdown-extended//commit/2bbf2ae93669d0b8433c5c402bd5ccbd291b78ff))
* add a test runner for PHP8 ([81005c9](https://github.com/e-picas/markdown-extended//commit/81005c9f18989d37b40f83e149d3167e40d0bb33))
* be php 5 compilant ([e043e82](https://github.com/e-picas/markdown-extended//commit/e043e8219a3e6ef48e0582eb698e5ee072a2ee1d))
* **chore:** review the dev tools: phpcs, phpdoc, phpmd, phpunit ([7ba2e57](https://github.com/e-picas/markdown-extended//commit/7ba2e5744cba3d1da92566bbf32a5af3daf0b323))
* **ci:** create release from 'develop' ([f8f0117](https://github.com/e-picas/markdown-extended//commit/f8f01174ac5ed50dd0fd4d90e0ad335068bc65ec))
* **ci:** no more Travis, replaced by GitHub Actions with Semantic Release ([c7f5365](https://github.com/e-picas/markdown-extended//commit/c7f5365f9cdfcfb99fa7c8c6ba60214644329f61))
* force a new release ([c6e6d73](https://github.com/e-picas/markdown-extended//commit/c6e6d73ea1c2ef9b6dfa47d41a914f9eb83889c2))
* **php7-migration:** push the project back to life ([6607973](https://github.com/e-picas/markdown-extended//commit/6607973d9f326ee0d94a5dc38908c9c27c16de4d))
* **php7:** run the code standard fixer with PHP7 standards ([052b79d](https://github.com/e-picas/markdown-extended//commit/052b79d975cc3d99cbdff16de2b31659032aa713))
* **test:** review of tests with groups and information about the manifest part treated ([d9fed44](https://github.com/e-picas/markdown-extended//commit/d9fed44685f388107d5d79631795017b38d0d785))
* **tests:** review of unit tests for a better display ([6b06456](https://github.com/e-picas/markdown-extended//commit/6b064566c8fad033823fe86f99cdc847e8ceb49c))

# [1.0.0-rc.4](https://github.com/e-picas/markdown-extended/compare/v1.0.0-rc.3...v1.0.0-rc.4) (2024-02-04)


### Bug Fixes

* rollback the composer plugin of semantic-release as it seems abandoned ([288742c](https://github.com/e-picas/markdown-extended//commit/288742c7a53e008f04977402b63c5fc165893c72))
* use the raw version parameter for the 'make-release' internal action (step 2) ([bc8b562](https://github.com/e-picas/markdown-extended//commit/bc8b562cec7cbc95b4662c56a77c311474fce368))

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
