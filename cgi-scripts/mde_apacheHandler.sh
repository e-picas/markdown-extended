#!/bin/bash
#
# CGI-script : parse '.md' files with the PHP Markdown Extended class
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

# Config
CHARSET='utf-8'
OPTIONS=''
REQ="$PATH_TRANSLATED"
if [ ! -z "$MDE_CHARSET" ]; then CHARSET="$MDE_CHARSET"; fi
if [ ! -z "$MDE_CONSOLE_OPTIONS" ]; then OPTIONS="$MDE_CONSOLE_OPTIONS"; fi
if [ ! -z "$*" ]; then REQ="$PATH_TRANSLATED/$*"; fi
PLAIN=${REQ:(-5)}

# Process 
CONSOLE=$(pwd)/../bin/mde_console
MARKDOWN_RESULT=$(php "$CONSOLE" "$OPTIONS" "$REQ")

# Start with outputting the HTTP headers
if [ "plain" = "$PLAIN" ]
    then echo "Content-type: text/plain;charset=$CHARSET"
    else echo "Content-type: text/html;charset=$CHARSET"
fi
echo

# debug
#echo "query : $QUERY_STRING"
#echo "console : $CONSOLE"
#echo "PATH_INFO : $PATH_INFO"
#echo "PATH_TRANSLATED : $PATH_TRANSLATED"
#echo "REDIRECT_HANDLER : $REDIRECT_HANDLER"
#echo "MDE_TPL : $MDE_TPL"
#exit 0

# Start HTML content
if [ ! -z "$MARKDOWN_RESULT" ]
    then  echo "$MARKDOWN_RESULT";
    else cat "$PATH_INFO";
fi
echo

# Endfile
