#!/bin/bash
#
# This file is part of the PHP-MarkdownExtended package.
#
# (c) Pierre Cassat <me@e-piwi.fr> and contributors
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#

if [ "$(pwd)/build" == "$(dirname "$(realpath ${BASH_SOURCE[0]})")" ]
then
    source "$(dirname $0)/_settings.sh";
else
    echo "!! you must run shell builders from package's root directory !!"
    exit 1
fi

echo "> ${MDE_BIN} -f man -o '$MAN_FILE' '$MDMAN_FILE'"
"${MDE_BIN}" -f man -o "$MAN_FILE" "$MDMAN_FILE" \
    && echo "manpage generated in '${MAN_FILE}'" \
    || echo "an error occurred!" ;

echo "> ${MDE_BIN} -f man -o '$DOC_FILE' '$MDDOC_FILE'"
"${MDE_BIN}" -f man -o "$DOC_FILE" "$MDDOC_FILE" \
    && echo "documentation generated in '${DOC_FILE}'" \
    || echo "an error occurred!" ;

exit 0
