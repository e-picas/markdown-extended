#!/usr/bin/env bash
#
# This file is part of the PHP-Markdown-Extended package.
#
# (c) Pierre Cassat (me at picas dot fr) and contributors
#
# For the full copyright and license information, please view the
# LICENSE file that was distributed with this source code.
#
# CGI-script to parse Markdown files with the PHP Markdown Extended class
# with APACHE direct handling
#

## Config
CONSOLE="$(pwd)/../../bin/markdown-extended"
CHARSET='utf-8'
OPTIONS=''
PHP_BIN="$(which php)"
REQ="$PATH_TRANSLATED"
PLAIN="$QUERY_STRING"
if [ ! -z "$MDE_CHARSET" ]; then CHARSET="$MDE_CHARSET"; fi
if [ ! -z "$MDE_CONSOLE_OPTIONS" ]; then OPTIONS="$MDE_CONSOLE_OPTIONS"; fi

## debug
# echo "Content-type: text/plain;charset=${CHARSET}"
# echo
# echo "## Server infos:"
# echo "QUERY : $QUERY_STRING"
# echo "PATH_INFO : $PATH_INFO"
# echo "PATH_TRANSLATED : $PATH_TRANSLATED"
# echo "REDIRECT_HANDLER : $REDIRECT_HANDLER"
# echo
# echo "## MDE infos:"
# echo "CONSOLE : $CONSOLE"
# echo "PHP_BIN : $PHP_BIN"
# echo "REQ : $REQ"
# echo "CHARSET : $CHARSET"
# echo "OPTIONS : $OPTIONS"
# echo
# echo "> gonna run:"
# echo "$PHP_BIN $CONSOLE $OPTIONS $PATH_TRANSLATED"
# exit 0

## Process 
MARKDOWN_RESULT=$($PHP_BIN $CONSOLE $OPTIONS $PATH_TRANSLATED)

## Start with outputting the HTTP headers
## And then the content
if [ "$PLAIN" = 'plain' ]
then
    echo "Content-type: text/plain;charset=${CHARSET}"
    echo
    cat "$PATH_TRANSLATED";
else
    if [ ! -z "$MARKDOWN_RESULT" ]
    then
        echo "Content-type: text/html;charset=${CHARSET}"
        echo
        echo "$MARKDOWN_RESULT";
    else
        echo "Content-type: text/plain;charset=${CHARSET}"
        echo
        cat "$PATH_TRANSLATED";
    fi
fi
echo
exit 0
# Endfile
