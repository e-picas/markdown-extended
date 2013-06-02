Extended Markdown Apache module - HOW TO
========================================


This document explains the usage of the `emd_apacheHandler.sh` shell script to use with [Apache](http://www.apache.org/).
This script is designed to handle Markdown syntax content files and serve to a webserver a parsed HTML
version of the original content.


## Prerequisite

To allow Apache to use this script, your webserver must run at least version 2 of Apache with the following modules:

-   mod_rewrite
-   mod_actions
-   mod_mime


## Sample htaccess file

    # We autorize CGIs
    Options +ExecCGI

    # We include 'sh' in exec scripts
    AddHandler cgi-script .sh

    # To display '.md' files as text if something went wrong
    # You can add any extension(s) you want to parse at the end of the line, separated by space
    AddType text/html .md

    # Treat '.md' files by the Markdown handler
    # CAUTION - this requires to know exactly where the CGI is ...
    AddHandler MarkDown .md
    Action MarkDown /{ SERVER ABSOLUTE PATH TO }/extended-markdown/cgi-scripts/emd_apacheHandler.sh virtual

