baseheaderlevel: 2

Markdown Extended Apache module - HOW TO
========================================

This document explains the usage of the `demo/cgi-scripts/mde_apacheHandler.sh` shell
script to use with [Apache](http://www.apache.org/). This script is designed to handle
Markdown syntax content files and serve to a webserver a parsed HTML version of the
original content.

If you encountered errors when browsing to your installation, see the [FAQ](#faq) section below.


## Prerequisites

To allow this script to work on your webserver, you need the following environment:

-   a webserver running a Linux/UNIX operating system,
-   your requests must be handled by [Apache 2](http://httpd.apache.org/) or higher
    (or at least, `.htaccess` files must be activated)[^1]
-   [PHP 5.3](http://php.net/) or higher.

As the package uses some internal Apache's features, you will need to enable the following
Apache modules (*see the [FAQ](#faq) section below for an "how-to"*):

-   [mod_rewrite](http://httpd.apache.org/docs/2.2/en/mod/mod_rewrite.html)
-   [mod_actions](http://httpd.apache.org/docs/trunk/en/mod/mod_actions.html)
-   [mod_mime](http://httpd.apache.org/docs/2.2/en/mod/mod_mime.html)
-   [mod_cgi](http://httpd.apache.org/docs/2.2/en/mod/mod_cgi.html)
-   [mod_include](http://httpd.apache.org/docs/2.2/mod/mod_include.html)


## Apache configuration

To allow Apache cgi-script handling of Markdown files, you need to set up some options in
your [virtual host](http://httpd.apache.org/docs/2.2/en/vhosts/), in its configuration file
or in an `.htaccess` file in the directory containing your Markdown files **AND** in the 
directory containing the shell handler.

### Setting up an `.htaccess` file

    # any environment variable beginning with `MDE_` will be fetched to the app
    #SetEnv MDE_TPL /{ SERVER ABSOLUTE PATH TO }/markdown-extended/user/template.html
    #SetEnv MDE_CHARSET iso-8859-11

    # We autorize CGIs
    Options +ExecCGI

    # We include 'sh' in exec scripts
    AddHandler cgi-script .sh

    # To display '.md' files as text if something went wrong
    # You can add any extension(s) you want to parse at the end of the line, separated by space
    AddType text/html .md {you may add here your Markdown files extension(s)}

    # Treat '.md' files by the Markdown handler
    # CAUTION - this requires to know exactly where the CGI is ...
    AddHandler MarkDown .md {you may add here your Markdown files extension(s)}
    Action MarkDown /{ SERVER ABSOLUTE PATH TO }/extended-markdown/cgi-scripts/mde_apacheHandler.sh virtual


### Setting up a new virtual host

Depending on your system and your version of Apache, the virtual host definition may be added
in the `/etc/apache2/httpd.conf` file or in a new file `/etc/apache/sites-available/your.domain`.
In this second case, after defining your host, you will need to enable it and restart the
Apache server on your system. See the [FAQ](#faq) section below for more infos.

This is a classic virtual host configuration:

    <VirtualHost *:80>
        ServerAdmin your@email
        ServerName your.domain
    
        DocumentRoot /your/document/root/path/www
        <Directory "/your/document/root/path/www">

            Options +ExecCGI
            AddHandler cgi-script .sh
            AddType text/html .md {you may add here your Markdown files extension(s)}
            AddHandler MarkDown .md {you may add here your Markdown files extension(s)}
            Action MarkDown /your/document/root/path/cgi-scripts/mde_apacheHandler.sh virtual

            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>
    
        ScriptAlias /cgi-bin/ /your/document/root/path/cgi-scripts/
        <Directory "/your/document/root/path/cgi-scripts">

            # any environment variable beginning with `MDE_` will be fetched to the app
            #SetEnv MDE_TPL /{ SERVER ABSOLUTE PATH TO }/template.html
            #SetEnv MDE_CHARSET iso-8859-11

            Options +ExecCGI
            AddHandler cgi-script .sh
            AddType text/html .md {you may add here your Markdown files extension(s)}
            AddHandler MarkDown .md {you may add here your Markdown files extension(s)}
            Action MarkDown /your/document/root/path/cgi-scripts/mde_apacheHandler.sh virtual

            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>    

    </VirtualHost>

After that you will need to restart Apache with a command like (depending on your
system and your Apache's version):

    ~$ sudo /etc/init.d/apache2 restart


## FAQ

### How-to: enable an new site in Apache2

If you set up a virtual host defined in a single file in `/etc/apache/sites-available/your.domain`,
you need to enable it running:

    ~$ a2ensite your.domain

Then restart Apache running:

    ~$ sudo /etc/init.d/apache2 restart

### How-to: enable an Apache module

To enable the Apache module `mod_NAME` on your server, just run the following command:

    ~$ a2enmod NAME

Once you have enabled all required modules, restart Apache running:

    ~$ sudo /etc/init.d/apache2 restart

### Error: "You need to run Composer ..."

If you have an error like:

>    You need to run Composer on the project to build dependencies and auto-loading
>    (see: http://getcomposer.org/doc/00-intro.md#using-composer)!

it means that the package is not yet installed (*some required dependencies are missing*).
To finish the installation, just run:

    ~$ php path/to/composer.phar install

or, if you installed Composer globally in your environment:

    ~$ php composer install

Once the installation has finished, reload the page in your browser.

### Error: "Internal Server Error"

If your encountered an "Internal Server Error" trying to access your 
virtual host's domain name, try the following:

-   check and re-define your virtual host `DOCUMENT_ROOT` or all your paths used in the 
    `.htaccess` (they all may be relative to the document root)

-   if you still have an internal error, try to copy the entire `www/.htaccess` file content
    and paste it in the corresponding `Directory` definition of your virtual host (in a file
    like `/etc/apache/sites-available/your.domain`)

-   if you still have an error, come to ask us explaining your system & configuration at
    <http://github.com/atelierspierrot/markdown-extended/issues>.

### I see the plain text version of my content

This means that your server's configuration does not allow a CGI parsing as you write it.
In certain cases, you could try to rewrite your URLs to your CGI script directly using the
PHP handler and writing in your virtual-host configuration[^2]:

    # special CGI handling
    Options +ExecCGI +FollowSymLinks
    RewriteEngine On
    RewriteBase /
    RewriteRule ^((.*)\.md)$  http://domain.ext/cgi-bin/mde_apacheHandler.php/$1 [P,L,NC,QSA]



[^1]: Some features requires Apache from version 2.0.23 but may not render an error with
lower versions.

[^2]: The `P` flag asks the redirection to act like a *proxy* but can send error. If this is
your case, just remove the flag (your redirection will be visible in the address bar).
