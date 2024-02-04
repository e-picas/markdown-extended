#
# This file is part of the PHP-Markdown-Extended package.
#
# Copyright (c) 2008-2024, Pierre Cassat (me at picas dot fr) and contributors
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# !! THIS FILE IS FOR DEVELOPMENT USAGE ONLY !!
#
# Usage: make [...]
#

SHELL := /bin/bash
CWD := $(shell pwd)
-include .env
export

PHP_VERS ?=
DOCKER_IMAGE_PHP ?= 7
DOCKER_IMAGE_APACHE ?= 7.4.27-apache
DOCKERFILE ?= docker/Dockerfile
APACHE_PORT_80 ?= 8080
MDE_DEV_DOCKER_CMD ?= bash

default: help
.PHONY: default

# setup versions based on the PHP_VERS variable
ifeq (${PHP_VERS}, 5)
DOCKERFILE:=docker/php-5/Dockerfile
DOCKER_IMAGE_PHP:=5
DOCKER_IMAGE_APACHE:=5.6-apache
endif
ifeq (${PHP_VERS}, 8)
DOCKER_IMAGE_PHP:=8
DOCKER_IMAGE_APACHE:=8.3.2-apache
endif

## Build the docker image for cli development (use the `PHP_VERS` variable to select the PHP version)
docker-build:
	docker build \
		-f $$(pwd)/${DOCKERFILE} \
		--target mde_dev \
		-t mde_dev:${DOCKER_IMAGE_PHP} \
		--build-arg IMAGE_VERSION=${DOCKER_IMAGE_PHP} \
		$$(pwd)/
.PHONY: docker-build

## Run the cli docker container (use the `PHP_VERS` variable to select the PHP version)
docker-run: docker-build
	docker run -ti --rm \
		-v $$(pwd):/mde-src \
		-w /mde-src/ \
		--name mde_dev_app_${DOCKER_IMAGE_PHP} \
		mde_dev:${DOCKER_IMAGE_PHP} \
		bash -c "${MDE_DEV_DOCKER_CMD}"
.PHONY: docker-run

## Build the docker image for apache/demo (use the `PHP_VERS` variable to select the PHP version)
docker-apache-build:
	docker build \
		-f $$(pwd)/${DOCKERFILE} \
		-t mde_server:${DOCKER_IMAGE_APACHE} \
		--build-arg IMAGE_VERSION=${DOCKER_IMAGE_APACHE} \
		$$(pwd)/
.PHONY: docker-apache-build

## Start the apache docker container for demo (use the `PHP_VERS` variable to select the PHP version)
docker-apache-start: docker-apache-build
	docker run -ti -d --rm \
		-v $$(pwd):/var/www/ \
		-p ${APACHE_PORT_80}:80 \
		-w /var/www/ \
		--name mde_server_app_${DOCKER_IMAGE_APACHE} \
		mde_server:${DOCKER_IMAGE_APACHE}
.PHONY: docker-apache-start

## Stop the apache/demo docker container (use the `PHP_VERS` variable to select the PHP version)
docker-apache-stop:
	docker stop mde_server_app_${DOCKER_IMAGE_APACHE}
.PHONY: docker-apache-stop

## Run the PHPUnit tests in the 'mde_dev' container
run-tests: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer test" make docker-run
.PHONY: run-tests

## Generate the PHPUnit tests reports in the 'mde_dev' container
generate-tests-coverage: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer test-coverage" make docker-run
.PHONY: generate-tests-coverage

## Run the Code Standards Fixer in the 'mde_dev' container
run-code-fixer: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer cs-fixer" make docker-run
.PHONY: run-code-fixer

## Generate the documentation running `phpdoc` in the 'mde_dev' container
generate-documentation: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer doc" make docker-run
.PHONY: generate-documentation

## Run the PHP Metrics tool in the 'mde_dev' container
generate-code-metrics: generate-tests-coverage
	MDE_DEV_DOCKER_CMD="composer install && composer metrics" make docker-run
.PHONY: generate-code-metrics

## Run the PHP Dedup tool in the 'mde_dev' container
run-code-deduplicator: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer dedup" make docker-run
.PHONY: run-code-deduplicator

## Run the PHP Loc tool in the 'mde_dev' container
run-code-sizer: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer sizer" make docker-run
.PHONY: run-code-sizer

## Run the PHP Mess Detector tool on the sources in the 'mde_dev' container
run-mess-detector: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector" make docker-run
.PHONY: run-mess-detector

## Generate a PHP Mess Detector tool report in `dev/code-analyzer-report.html` analyzing the sources in the 'mde_dev' container
generate-mess-detector-report: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector-report" make docker-run
.PHONY: generate-mess-detector-report

## Run the PHP Mess Detector tool comparing to the `.phpmd.baseline.xml` baseline on the sources in the 'mde_dev' container
run-mess-detector-with-baseline: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector-with-baseline" make docker-run
.PHONY: run-mess-detector-with-baseline

## Generate the PHP Mess Detector tool `.phpmd.baseline.xml` baseline on the sources in the 'mde_dev' container
generate-mess-detector-baseline: docker-build
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector-generate-baseline" make docker-run
.PHONY: generate-mess-detector-baseline

## Generate all reports
generate-all-reports: generate-tests-coverage generate-mess-detector-report generate-code-metrics generate-documentation
.PHONY: generate-all-reports

## RELEASE - Install the Semantic Release dependencies locally
gitflow-env-install:
	npm install --local \
		semantic-release \
		@semantic-release/github \
		@semantic-release/git \
		@semantic-release/changelog \
		@semantic-release/exec \
		@saithodev/semantic-release-backmerge
.PHONY: gitflow-env-install

CURRENT_BRANCH_NAME := $(shell git rev-parse --abbrev-ref HEAD)

## RELEASE - Create a 'release-X.Y.Z' branch from 'develop'
gitflow-make-release: gitflow-env-install
	@[[ ${CURRENT_BRANCH_NAME} =~ ^develop ]] || { echo "!! > you MUST be an a 'develop' branch to create a release"; exit 1; }
	git pull
	semantic-release --dry-run --no-ci
	git checkout -b release-$$(cat /tmp/mde-next_release)
	git push origin release-$$(cat /tmp/mde-next_release)
.PHONY: gitflow-make-release

## RELEASE - Merge a 'release-X.Y.Z' branch into 'master'
gitflow-publish-release:
	@[[ ${CURRENT_BRANCH_NAME} =~ ^release ]] || { echo "!! > you MUST be an a 'release' branch to publish it"; exit 1; }
	git fetch
	@[[ $$(git rev-parse HEAD) == $$(git rev-parse origin/${CURRENT_BRANCH_NAME}) ]] || { echo "!! > your local branch is not up to date with the remote"; exit 1; }
	git checkout master
	git reset --hard origin/master
	git merge --no-ff --no-edit --no-commit origin/${CURRENT_BRANCH_NAME}
	git commit -m "ci(${CURRENT_BRANCH_NAME}): merging prepared release";
	git push origin master;
	git branch -D ${CURRENT_BRANCH_NAME}
	git push --delete origin ${CURRENT_BRANCH_NAME}
.PHONY: gitflow-publish-release

# The followings are for hard dev and not documented ...
docker-dev-build:
	docker build \
		-f $$(pwd)/docker/dev-full/Dockerfile \
		-t mde_dev_full \
		--build-arg IMAGE_VERSION=${DOCKER_IMAGE_PHP} \
		$$(pwd)/
.PHONY: docker-dev-build
docker-dev-run: docker-dev-build
	docker run -ti --rm \
		-v $${HOME}/.ssh:/home/app_user/.ssh \
		-v $$(pwd):/mde-src \
		-w /mde-src/ \
		--name mde_dev_full_run \
		mde_dev_full \
		bash
.PHONY: docker-dev-run

# largely inspired by <https://docs.cloudposse.com/reference/best-practices/make-best-practices/>
help:
	@printf "#############################################\n!! THIS FILE IS FOR DEVELOPMENT USAGE ONLY !!\n#############################################\n"
	@printf "\n"
	@printf "To use this file, run: make <target>\n"
	@printf "\n"
	@printf "Available targets:\n"
	@printf "\n"
	@awk '/^[a-zA-Z\-\_0-9%:\\]+/ { \
		helpMessage = match(lastLine, /^## (.*)/); \
		if (helpMessage) { \
			helpCommand = $$1; \
			helpMessage = substr(lastLine, RSTART + 3, RLENGTH); \
			gsub("\\\\", "", helpCommand); \
			gsub(":+$$", "", helpCommand); \
			printf "  \x1b[32;01m%-35s\x1b[0m %s\n", helpCommand, helpMessage; \
		} \
	} \
	{ lastLine = $$0 }' $(MAKEFILE_LIST) | sort -u
	@printf "\n"
	@printf "To override a variable used in the Makefile, you can run: 'make <target> VAR_NAME=my-value'.\n"
	@printf "You can also declare it in a '.env' local file which is loaded at each run.\n"
	@printf "\n"
	@printf "Available variables and default values:\n"
	@printf "\n"
	@awk '/^[a-zA-Z\_0-9]+[ ]\?=/ { \
		var=val=$$0; \
		sub(/\?=.*/,"",var); \
		sub(/[^=]+=/,"",val); \
		printf "  \x1b[32;01m%-25s\x1b[0m %s\n", var, val; \
	}' $(MAKEFILE_LIST) | sort -u
	@printf "\n"
.PHONY: help
