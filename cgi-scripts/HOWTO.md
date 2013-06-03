Markdown Extended Apache module - HOW TO
========================================

This document explains the usage of the `mde_apacheHandler.sh` shell script to use with
[Apache](http://www.apache.org/). This script is designed to handle Markdown syntax content
files and serve to a webserver a parsed HTML version of the original content.


## Prerequisite

To allow this script to work on your webserver, you need the following environment:

-   a webserver running a Linux/UNIX operating system,
-   your requests must be handled by [Apache 2](http://httpd.apache.org/) or higher
    (or at least, `.htaccess` files must be activated)[^1]
-   [PHP 5.3](http://php.net/) or higher.

As the package uses some internal Apache's features, you will need to enable the following
Apache modules (*see the [FAQ](#faq) section below fo an "how-to"*):

-   [mod_rewrite](http://httpd.apache.org/docs/2.2/en/mod/mod_rewrite.html)
-   [mod_actions](http://httpd.apache.org/docs/trunk/en/mod/mod_actions.html)
-   [mod_mime](http://httpd.apache.org/docs/2.2/en/mod/mod_mime.html)
-   [mod_cgi](http://httpd.apache.org/docs/2.2/en/mod/mod_cgi.html)
-   [mod_include](http://httpd.apache.org/docs/2.2/mod/mod_include.html)


## Apache configuration

### Sample `.htaccess` file

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


### Setting up the virtual host

For more infos about virtual hosts in Apache, how to define them and how to enable the related 
new domain see: <http://httpd.apache.org/docs/2.2/en/vhosts/>.

To allow the handler to work, you may have to define a new virtual host defining a directory for
web classic access to your `www/` directory and a CGI access to the `cgi-scripts/` directory.

Depending on your system and your version of Apache, the virtual host definition may be added
in the `/etc/apache2/httpd.conf` file or in a new file `/etc/apache/sites-available/your.domain`.
In this second case, after defining your host, you will need to enables it and restart the
Apache server on your system. See the [FAQ](#faq) section below for more infos.

This is a classic virtual host configuration:

    <VirtualHost *:80>
        ServerAdmin your@email
        ServerName your.domain
    
        DocumentRoot /your/document/root/path/www
        <Directory "/your/document/root/path/www">
            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>
    
        ScriptAlias /cgi-bin/ /your/document/root/path/cgi-scripts/
        <Directory "/your/document/root/path/cgi-scripts">
            AllowOverride All
            Order allow,deny
            allow from all
        </Directory>    

    </VirtualHost>

After that you will need to restart Apache with a command like (depending on your
system and your Apache's version):

    ~$ sudo /etc/init.d/apache2 restart

If you encountered errors when browsing to your installation, see the [FAQ](#faq) section below.


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
    <https://github.com/atelierspierrot/markdown-extended/issues>.


[^1]: Some features requires Apache from version 2.0.23 but may not render an error with
lower versions.