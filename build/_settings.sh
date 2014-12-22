#!/bin/bash

# builders settings

export HERE=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
export DATE=$(git log -1 --format="%ci" --date=short | cut -s -f 1 -d ' ')

export MDMAN_SOURCE="docs/MANPAGE.md"
export MDMAN_FILE="${HERE}/../${MDMAN_SOURCE}"
export MAN_SOURCE="bin/markdown-extended.man"
export MAN_FILE="${HERE}/../${MAN_SOURCE}"
export MDE_BIN="${HERE}/../bin/markdown-extended"

# checks

if [ ! -f "$MDE_BIN" ]; then
    echo "!! '${MDE_BIN}' not found!" ;
    exit 1
fi

if [ ! -f "$MDMAN_FILE" ]; then
    echo "!! '${MDMAN_FILE}' not found!" ;
    exit 1
fi
