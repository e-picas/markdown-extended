Man:   Coffee Manual
Name:    COFFEE
Author:  Frederic Culot
Date:    February 20, 2012  
Version: 1.0

## NAME

Coffee - USB coffee-machine controller

## SYNOPSIS

**coffee** [**-h**|**-v**] [**-o** *origin*] [**s** *quantity*] [**-t** *temperature*] [**-c** *flavor*] 

## DESCRIPTION

**Coffee** is an USB coffee-machine controller. The coffee is
configurable, and one can choose between different origins, temperatures, sugar
quantity, and last but not least, you can have a crepe and even specify its flavor.

## OPTIONS

The following options are supported:

**-s** , **--sugar**
:   Specify the quantity of sugar you want. 

**-t** , **--temp**
:   Specify the temperature you want for your coffee.

:   *Note:* beware that choosing 'hot' could lead to severe injuries.

**-o** , **--origin**
:   You can choose where your coffee comes from. Possible choices are:
    *colombia*, *ethiopia*, *carrouf*.

**-c** , **--crepe**
:   Specify the crepe flavor you want. Available flavors are *rhum* and
    *whisky* for now on.

**-h**, **--help**
:   Print a short help text describing the supported command-line options,
    and then exit. 

**-v**, **--version**
:   Display **coffee** version and exit.

## LICENSE

Copyright (c) 2007 by Frederic Culot. 

This software is released under the GNU General Public License. Please
read the COPYING file for more information. 

## BUGS

No bugs.
Anyway, if you find any, please send a report to boss@estat.com so that the
author could be fired.

## AUTHOR

**Frederic Culot** . (Oups, better not mention this!)

## SEE ALSO

couscous(1), sugar(5), rhum(5)
