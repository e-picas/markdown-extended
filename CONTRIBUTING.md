Contribute to PHP MarkdownExtended
==================================


If you want to contribute to this project, first you are very welcome ;) Then, this documentation
file will introduce you the "dev-cycle" of PHP MarkdownExtended.


Inform about a bug or request for a new feature
-----------------------------------------------

### Bug ticketing

A bug is a *demonstrable problem caused by the code* (and not due to a special user behaviour).
Bugs are inevitable and exist in all software. If you find one and want to transmit it, first
**it is very helpful** as it will participate to build a robust software.

But ... a bug report is helpful as long as it can be understood, reproduced, and that it permits to
identify the error (and what caused it). A good bug report MUST follow these guidelines:

-   **first**: search in the issue tracker if your bug has not been transmitted yet ; if you find it,
    you can add a new comment to the appropriate thread with your experience if it seems different
    from the others ;
-   **then**: check if it exists right now: try to reproduce it with the current code to confirm it still exists ;
-   if you **finally** create a bug ticket, try to detail it as much as possible:
    -   what is your environment (application version, OS, device ...)?
    -   describe and comment the steps that brought you to that bug
    -   try to isolate the problem as much as possible
    -   what did you expect?


### Feature requests

If you want to ask for a new feature, please follow these guidelines:

-   the goal of this project is to be (and keep) relevant for a large public ; maybe your request
    is quite personal (you have a particular need) and can be discussed with me by email ; in this
    case please do not make a feature request (!)
-   if you think something is missing or have an idea to increase one of PHP MarkdownExtended's features, then
    you are ready for a "feature request" ; you can create an issue ticket beginning its name by
    "feature request: " ; please detail your request or your idea as much as possible, with a lot 
    of your experience.


Actually change the code
------------------------


First of all, you may do the following two things:

-   read the *How to contribute* section below to learn about forking, working and pulling,
-   from your fork of the repository, switch to the `dev` branch: this is where the dev things are done.


### How to contribute ?

If you want to correct a typo or update a feature of PHP MarkdownExtended, the first thing to do is
[create your own fork of the repository](http://help.github.com/articles/fork-a-repo).
You will need a (free) GitHub account to do this and your copy will appear in your forks list.
This is on THIS repository (your own fork) that you will work (you have no right to make 
direct `push` on the original repository).

Once your work seems finished, you'll have to commit it and push it on your fork (you may 
finally see your modifications on the sources view on GitHub). Then you'll have to make a 
"pull-request" to the original repository, commenting it with a description of your correction or
update, or anything you want me to know about ... Then, if your work seems ok for me 
(and it certainly will :) and when I'll have the time (!), your work will finally be 
"merged" in the original repository and you will be able to (eventually) close your fork. 
Note that the "merge" of a pull-request keeps your name and profile as the "commiter" 
(the one who made the stuff).

**BEFORE** you start a work on the code, please check that this has NOT been done yet, or part
of it, by giving a look at <http://github.com/piwi/markdown-extended/pulls>. If you 
find a pull-request that seems to be like the modification you were going to do, you can 
comment the request with your vision of the thing or your experience.


### Full installation of a fork

To prepare a development version of PHP MarkdownExtended, clone your fork of the repository and
put it on the "dev" branch:

    git clone http://github.com/<your-username>/markdown-extended.git
    cd markdown-extended
    git checkout dev

Then you can create your own branch with the name of your feature:

    git checkout -b <my-branch>

The development process of the package requires some external dependencies to work, loaded via
[Composer](http://getcomposer.org/). To install them, run:

    // install Composer if your don't have it
    curl -sS https://getcomposer.org/installer | php

    // install PHP dependencies
    php composer.phar install

Your clone is ready ;)

You can *synchronize* your fork with current original repository by defining a remote to it
and pulling new commits:

    // create an "upstream" remote to the original repo
    git remote add upstream http://github.com/piwi/markdown-extended.git

    // get last original remote commits
    git checkout dev
    git pull upstream dev


### Development life-cycle

As said above, all development MUST be done on the `dev` branch of the repository. Doing so we
can commit our development features to let users using a clone test and improve them.

When the work gets a stable stage, it seems to be time to build and publish a new release. This
is done by creating a tag named like `vX.Y.Z[-status]`[^1] from the "master" branch after having
merged the "dev" one in.


How-tos
-------

### Generate the "PHAR" archive

To automatically re-generate the "markdown-extended.phar" file from current version, you can use:

    ~$ php bin/mde-dev make-phar


### Generate the man-page

To automatically re-generate the manpages of the package, you can use:

    ~$ php bin/mde-dev make-manpage-3
    ~$ php bin/mde-dev make-manpage-7
    ~$ php bin/mde-dev make-manpages    # this will run both

To generate them manually, you can run:

    ~$ bin/markdown-extended -f man -o bin/markdown-extended.3.man doc/MANPAGE.md
    ~$ man ./bin/markdown-extended.3.man
    ~$ bin/markdown-extended -f man -o bin/markdown-extended.7.man doc/DOCUMENTATION.md
    ~$ man ./bin/markdown-extended.7.man


### Generate the documentation

You can (re-)generate a full PHP documentation, at any time, running:

    ~$ php bin/sami.php update sami.config.php

The documentation is built in the `phpdoc/` directory in the package, and requires a temporary
directory for its generation that is configured on:

    path/to/markdown-extended/../tmp/cache/markdown-extended/

You can modify this setting editing the `sami.config.php` file.


### Launch unit-tests

You can verify that your package passes all tests running:

    ~$ php bin/phpunit

All tests are stored in the `tests/` directory of the package.

Note that the package is integrated with [Travis CI](http://travis-ci.org/).


### Mess detection

You can check code mess running:

    ~$ php bin/phpmd src text codesize


Coding rules
------------

-   use space (no tab) ; 1 tab = 4 spaces ; this is valid for all languages
-   comment your work (just enough)
-   in case of error in a PHP script, ALWAYS throw one of the `MarkdownExtended\Exception`s with a message


[^1]: Please see the [Semantic Versioning](http://semver.org/) work by Tom Preston-Werner for
more info about the release version name construction rules.

----

If you have questions, you can (eventually) contact me at *me [at] e [dash] piwi [dot] fr*.
