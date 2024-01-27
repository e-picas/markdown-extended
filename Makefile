#
# This file is part of the PHP-Markdown-Extended package.
#
# Copyright (c) 2008-2015, Pierre Cassat (me at picas dot fr) and contributors
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

APACHE_PORT_80 ?= 8080
MDE_DEV_DOCKER_CMD ?= bash

default: help
.PHONY: default

## Build the docker image for cli development
docker-build-mde:
	docker build \
		-f $$(pwd)/docker/php/Dockerfile \
		-t mde_dev \
		$$(pwd)/
.PHONY: docker-build-mde

## Run the cli docker container
docker-run-mde: docker-build-mde
	docker run -ti --rm \
		--name mde_dev_app \
		-v $$(pwd):/mde-src \
		-w /mde-src/ \
		mde_dev bash -c "${MDE_DEV_DOCKER_CMD}"
.PHONY: docker-run-mde

## Build the docker image for apache/demo
docker-build-apache:
	docker build \
		-f $$(pwd)/docker/php-apache/Dockerfile \
		-t mde_server \
		$$(pwd)/
.PHONY: docker-build-apache

## Run the apache/demo docker container
docker-run-apache: docker-build-apache
	docker run -ti -d --rm \
		--name mde_server_app \
		-v $$(pwd):/var/www/ \
		-p ${APACHE_PORT_80}:80 \
		-w /var/www/ \
		mde_server
.PHONY: docker-run-apache

## Stop the apache/demo docker container
docker-stop-apache:
	docker stop mde_server_app
.PHONY: docker-stop-apache

## Run the PHPUnit tests in the 'mde_dev' container
run-tests: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer test" make docker-run-mde
.PHONY: run-tests

## Generate the PHPUnit tests reports in the 'mde_dev' container
generate-tests-report: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer test-report" make docker-run-mde
.PHONY: generate-tests-report

## Run the Code Standards Fixer in the 'mde_dev' container
run-code-fixer: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer cs-fixer" make docker-run-mde
.PHONY: run-code-fixer

## Generate the documentation running `phpdoc` in the 'mde_dev' container
generate-documentation: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer doc" make docker-run-mde
.PHONY: generate-documentation

## Run the PHP Metrics tool in the 'mde_dev' container
generate-code-metrics: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer metrics" make docker-run-mde
.PHONY: generate-code-metrics

## Run the PHP Dedup tool in the 'mde_dev' container
run-code-deduplicator: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer dedup" make docker-run-mde
.PHONY: run-code-deduplicator

## Run the PHP Loc tool in the 'mde_dev' container
run-code-sizer: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer sizer" make docker-run-mde
.PHONY: run-code-sizer

## Run the PHP Mess Detector tool on the sources in the 'mde_dev' container
run-mess-detector: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector" make docker-run-mde
.PHONY: run-mess-detector

## Generate a PHP Mess Detector tool report in `dev/code-analyzer-report.html` analyzing the sources in the 'mde_dev' container
generate-mess-detector-report: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector-report" make docker-run-mde
.PHONY: generate-mess-detector-report

## Run the PHP Mess Detector tool comparing to the `.phpmd.baseline.xml` baseline on the sources in the 'mde_dev' container
run-mess-detector-with-baseline: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector-with-baseline" make docker-run-mde
.PHONY: run-mess-detector-with-baseline

## Generate the PHP Mess Detector tool `.phpmd.baseline.xml` baseline on the sources in the 'mde_dev' container
generate-mess-detector-baseline: docker-build-mde
	MDE_DEV_DOCKER_CMD="composer install && composer messdetector-generate-baseline" make docker-run-mde
.PHONY: generate-mess-detector-baseline

# largely inspired by <https://docs.cloudposse.com/reference/best-practices/make-best-practices/>
help:
	@printf "!! THIS FILE IS FOR DEVELOPMENT USAGE ONLY !!\n"
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
