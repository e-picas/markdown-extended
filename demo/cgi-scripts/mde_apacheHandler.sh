#!/bin/bash
#
# CGI-script to parse Markdown files with the PHP Markdown Extended class
# with APACHE direct handling
#
# PHP Markdown Extended
# Copyright (c) 2004-2013 Pierre Cassat
#
# original MultiMarkdown
# Copyright (c) 2005-2009 Fletcher T. Penney
# <http://fletcherpenney.net/>
#
# original PHP Markdown & Extra
# Copyright (c) 2004-2012 Michel Fortin  
# <http://michelf.com/projects/php-markdown/>
#
# original Markdown
# Copyright (c) 2004-2006 John Gruber  
# <http://daringfireball.net/projects/markdown/>

## Config
CONSOLE="`pwd`/../../bin/mde_console"
CHARSET='utf-8'
OPTIONS=''
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
# echo "REQ : $REQ"
# echo "CHARSET : $CHARSET"
# echo "OPTIONS : $OPTIONS"
# exit 0

## Process 
MARKDOWN_RESULT=$(php "$CONSOLE" "$OPTIONS" "$PATH_TRANSLATED")

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
