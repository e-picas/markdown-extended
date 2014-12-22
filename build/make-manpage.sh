#!/bin/bash

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

exit 0
