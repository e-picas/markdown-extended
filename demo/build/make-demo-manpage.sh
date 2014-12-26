#!/bin/bash

HERE=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
MDE_BIN="${HERE}/../../bin/markdown-extended"

#_source="${1:-${HERE}/../MD_syntax.md}"
_source="${1:-${HERE}/../../tests/test-suite.mde}"
_target="${2:-${HERE}/../man/test-suite-manpage.man}"

echo "> ${MDE_BIN} -f man -o '$_target' '$_source'"
"${MDE_BIN}" -f man -o "$_target" "$_source" \
    && echo "manpage generated in '${_target}'" \
    || echo "an error occurred!" ;

exit 0
