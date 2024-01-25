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

default: help
.PHONY: default

## Build the docker image for cli development
docker-build-mde:
	docker build \
		-f $$(pwd)/dev/docker/php/Dockerfile \
		-t mde_dev \
		$$(pwd)/
.PHONY: docker-build-mde

## Run the cli docker container
docker-run-mde:
	docker run -ti --rm \
		--name mde_dev_app \
		-v $$(pwd):/mde-src \
		-w /mde-src/ \
		mde_dev bash
.PHONY: docker-run-mde

## Build the docker image for apache/demo
docker-build-apache:
	docker build \
		-f $$(pwd)/dev/docker/php-apache/Dockerfile \
		-t mde_server \
		$$(pwd)/
.PHONY: docker-build-apache

## Run the apache/demo docker container
docker-run-apache:
	docker run -ti -d --rm \
		--name mde_server_app \
		-v $$(pwd):/var/www/ \
		-p ${APACHE_PORT_80}:80 \
		-w /mde-src/ \
		mde_server
.PHONY: docker-run-apache

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
