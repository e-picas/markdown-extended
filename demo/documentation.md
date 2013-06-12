# Library documentation

[*Bash shell library 0.0.4 wip edb977591c661a8bc735272a57c32889147669c5 - 2013-06-11*]

{% TOC %}

Package [**atelierspierrot/bash-library**] version [**0.0.4**]. 

Licensed under GPL-3.0 - Copyleft (c) Les Ateliers Pierrot <http://www.ateliers-pierrot.fr/> - Some rights reserved. 

For sources & updates, see <https://github.com/atelierspierrot/bash-library>.

For bug reports, see <https://github.com/atelierspierrot/bash-library/issues>. 

To read GPL-3.0 license conditions, see <http://www.gnu.org/licenses/gpl-3.0.html>.

----



## SETTINGS (line 10)

-    SCRIPT_INFOS = ( NAME VERSION DATE PRESENTATION LICENSE HOME )

-    MANPAGE_INFOS = ( SYNOPSIS DESCRIPTION OPTIONS FILES ENVIRONMENT BUGS AUTHOR SEE_ALSO )

-    LIB_FLAGS = ( VERBOSE QUIET DEBUG INTERACTIVE FORCED )

-    LIB_COLORS = ( COLOR_LIGHT COLOR_DARK COLOR_INFO COLOR_NOTICE COLOR_WARNING COLOR_ERROR COLOR_COMMENT )

-    LIBCOLORS = ( default black red green yellow blue magenta cyan grey white lightred lightgreen lightyellow lightblue lightmagenta lightcyan lightgrey )

-    LIBTEXTOPTIONS = ( normal bold small underline blink reverse hidden )


## COMMON OPTIONS (line 59)

-    INTERACTIVE = DEBUG = VERBOSE = QUIET = FORCED = false

-    WORKINGDIR = pwd

-    LOGFILE = bashlib.log

-    TEMPDIR = tmp

-    COMMON_OPTIONS_ARGS = "d:fhil:qvVx-:" | COMMON_OPTIONS_ARGS_MASK = REGEX mask that matches all common options


## LOREM IPSUM (line 79)

-    LOREMIPSUM , LOREMIPSUM_SHORT , LOREMIPSUM_MULTILINE


## SYSTEM (line 142)

-   **getsysteminfo ()**

-   **getmachinename ()**

-   **addpath ( path )**

    add a path to global environment PATH

-   **isgitclone ( path = pwd )**

    check if a path, or `pwd`, is a git clone

-   **getscriptpath ( script = $0 )**

    get the full real path of a script directory (passed as argument) or from current executed script

-   **realpath ( script = $0 )**

    get the real path of a script (passed as argument) or from current executed script

-   **setworkingdir ( path )**

    handles the '-d' option for instance

    throws an error if 'path' does not exist

-   **setlogfilename ( path )**

    handles the '-l' option for instance


## COLORIZED CONTENTS (line 211)

-   **gettextformattag ( code )**

    @param code must be one of the library colors or text-options codes

    echoes the terminal tag code for color: "\ 033[CODEm"

-   **getcolorcode ( name , background = false )**

    @param name must be in LIBCOLORS

-   **getcolortag ( name , background = false )**

    @param name must be in LIBCOLORS

-   **gettextoptioncode ( name )**

    @param name must be in LIBTEXTOPTIONS

-   **gettextoptiontag ( name )**

    @param name must be in LIBTEXTOPTIONS

-   **gettextoptiontagclose ( name )**

    @param name must be in LIBTEXTOPTIONS

-   **colorize ( string , text_option , foreground , background )**

    @param text_option must be in LIBTEXTOPTIONS

    @param foreground must be in LIBCOLORS

    @param background must be in LIBCOLORS

    echoes a colorized string ; all arguments are optional except `string`

-   **parsecolortags ( "string with <bold>tags</bold>" )**

    parse in-text tags like:

        ... <bold>my text</bold> ...     // "tag" in LIBTEXTOPTIONS

        ... <red>my text</red> ...       // "tag" in LIBCOLORS

        ... <bgred>my text</bgred> ...   // "tag" in LIBCOLORS, constructed as "bgTAG"

-   **stripcolors ( string )**


## ARRAY (line 351)

-   **array_search ( item , $array[@] )**

    @return the index of an array item

-   **in_array ( item , $array[@] )**

    @return 0 if item is found in array


## STRING (line 372)

-   **strlen ( string )**

    @return the number of characters in string

-   **getextension ( filename )**

    retrieve a file extension

-   **strtoupper ( string )**

-   **strtolower ( string )**

-   **ucfirst ( string )**


## BOOLEAN (line 395)

-   **onoffbit ( bool )**

    echoes 'on' if bool=true, 'off' if it is false


## UTILS (line 401)

-   **_echo ( string )**

    echoes the string with the true 'echo -e' command

    use this for colorization

-   **_necho ( string )**

    echoes the string with the true 'echo -en' command

    use this for colorization and no new line

-   **verbose_echo ( string )**

    echoes the string if "verbose" is "on"

-   **verecho ( string )**

    alias of 'verbose_echo'

-   **quiet_echo ( string )**

    echoes the string if "quiet" is "off"

-   **quietecho ( string )**

    alias of 'quiet_echo'

-   **interactive_exec ( command , debug_exec = true )**

    executes the command after user confirmation if "interactive" is "on"

-   **iexec ( command , debug_exec = true )**

    alias of 'interactive_exec'

-   **debug_exec ( command )**

    execute the command if "debug" is "off", just write it on screen otherwise

-   **debexec ( command )**

    alias of 'debug_exec'

-   **prompt ( string , default = y , options = Y/n )**

    prompt user a string proposing different response options and selecting a default one

    final user fill is loaded in USERRESPONSE

-   **info ( string, bold = true )**

    writes the string on screen and return

-   **warning ( string , funcname = FUNCNAME[1] , line = BASH_LINENO[1] , tab='    ' )**

    writes the error string on screen and return

-   **error ( string , status = 90 , funcname = FUNCNAME[1] , line = BASH_LINENO[1] , tab='   ' )**

    writes the error string on screen and then exit with an error status

    @error default status is E_ERROR (90)

-   **nooptionerror ()**

    no script option error

    @error exits with status E_OPTS (81)

-   **commanderror ( cmd )**

    command not found error

    @error exits with status E_CMD (82)

-   **patherror ( path )**

    path not found error

    @error exits with status E_PATH (83)


## TEMPORARY FILES (line 570)

-   **gettempdirpath ( dirname = "LIB_TEMPDIR" )**

    @param dirname The name of the directory to create (default is `tmp/`)

    creates a default temporary dir with fallback: first in current dir then in system '/tmp/'

    the real temporary directory path is loaded in the global `TEMPDIR`

-   **gettempfilepath ( filename , dirname = "LIB_TEMPDIR" )**

    @param filename The temporary filename to use

    @param dirname The name of the directory to create (default is `tmp/`)

    this will echoes a unique new temporary file path

-   **createtempdir ( dirname = "LIB_TEMPDIR" )**

    @param dirname The name of the directory to create (default is `tmp/`)

    this will create a temporary directory in the working directory with full rights

    use this method to over-write an existing temporary directory

-   **cleartempdir ( dirname = "LIB_TEMPDIR" )**

    @param dirname The name of the directory (default is `tmp/`)

    this will deletes the temporary directory

-   **cleartempfiles ( dirname = "LIB_TEMPDIR" )**

    @param dirname The name of the directory (default is `tmp/`)

    this will deletes the temporary directory contents (not the directory itself)


## LOG FILES (line 657)

-   **getlogfilepath ()**

    creates a default placed log file with fallback: first in '/var/log' then in current dir

    the real log file path is loaded in the global `LOGFILEPATH

-   **log ( message , type='' )**

    this will add an entry in LOGFILEPATH

-   **readlog ()**

    this will read the LOGFILEPATH content


## CONFIGURATION FILES (line 686)

-   **getglobalconfigfile ( file_name )**

-   **getuserconfigfile ( file_name )**

-   **readconfig ( file_name )**

    read a default placed config file with fallback: first in 'etc/' then in '~/'

-   **readconfigfile ( file_path )**

    read a config file

-   **writeconfigfile ( file_path , array_keys , array_values )**

    array params must be passed as "array[@]" (no dollar sign)

-   **setconfigval ( file_path , key , value )**

-   **getconfigval ( file_path , key )**

-   **buildconfigstring ( array_keys , array_values )**

    params must be passed as "array[@]" (no dollar sign)


## SCRIPT OPTIONS / ARGUMENTS (line 835)

-   **getscriptoptions ( "$@" )**

    this will stop options treatment at '--'

-   **getlongoptionarg ( "$x" )**

    echoes the argument of a long option

-   **getlastargument ( "$x" )**

    echoes the last argument that is not an option

    for instance 'script.sh --options action' will echo "action"

-   **parsecomonoptions ( "$@" )**

    parse common script options as described in $LIB_OPTIONS

    this will stop options treatment at '--'


## SCRIPT INFOS (line 917)

-   **version ()**

-   **title ( lib = false )**

    this function must echo an information about script NAME and VERSION

    setting `$lib` on true will add the library infos

-   **usage ( lib_info = true )**

    this function must echo the usage information USAGE (with option "-h")

-   **script_version ()**


## LIBRARY INFOS (line 991)

-   **library_info ()**

-   **library_usage ()**

    this function must echo the usage information of the library itself (with option "--libhelp")

-   **library_version ()**

    this function must echo an information about library name & version (with option "--libvers")

-   **libdebug ( "$*" )**

    see all common options flags values & some debug infos

-   **libdoc ()**

    get the library functions list (with option "--libdoc")

    expend the doc with option '-v'

-    LIBRARY_REALPATH

----

[*Doc generated at 12-6-2013 22:48:19 from path '/Applications/MAMP/htdocs/GitHub_projects/test_bashlib/bin/bash-library.sh'*]
