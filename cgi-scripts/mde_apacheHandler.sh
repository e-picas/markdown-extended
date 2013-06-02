#!/bin/bash
#
# CGI-script : parse '.md' files with the PHP Extended Markdown class
# with APACHE direct handling
#
# PHP Extended Markdown
# Copyleft (c) 2013 Pierre Cassat
# http://www.ateliers-pierrot.fr - contact@ateliers-pierrot.fr

###############
# Config
###############
CHARSET='utf-8'
OPTIONS=''

###############
# Process Markdown file.
###############
CONSOLE=$(pwd)/../bin/mde_console
MARKDOWN_RESULT=$(php "$CONSOLE" "$OPTIONS" "$PATH_TRANSLATED")

###############
# Start with outputting the HTTP headers.
###############
echo "Content-type: text/html;charset=$CHARSET"
echo

###############
# debug
###############
#echo "console : $CONSOLE"
#echo "PATH_INFO : $PATH_INFO"
#echo "PATH_TRANSLATED : $PATH_TRANSLATED"
#echo "REDIRECT_HANDLER : $REDIRECT_HANDLER"

###############
# Start HTML content.
###############
if [ ! -z "$MARKDOWN_RESULT" ]
then 
	echo "$MARKDOWN_RESULT";
else
	 cat "$PATH_INFO";
fi

# Endfile