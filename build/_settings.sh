#!/bin/bash
#
# This file is part of the PHP-MarkdownExtended package.
#
# (c) Pierre Cassat <me@e-piwi.fr> and contributors
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#

# builders settings

export HERE=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
export DATE=$(git log -1 --format="%ci" --date=short | cut -s -f 1 -d ' ')

export MDMAN_SOURCE="docs/MANPAGE.md"
export MDMAN_FILE="${HERE}/../${MDMAN_SOURCE}"
export MAN_SOURCE="man/markdown-extended.man"
export MAN_FILE="${HERE}/../${MAN_SOURCE}"
export MDDOC_SOURCE="docs/DOCUMENTATION.md"
export MDDOC_FILE="${HERE}/../${MDDOC_SOURCE}"
export DOC_SOURCE="man/markdown-extended-documentation.man"
export DOC_FILE="${HERE}/../${DOC_SOURCE}"
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

if [ ! -f "$MDDOC_FILE" ]; then
    echo "!! '${MDDOC_FILE}' not found!" ;
    exit 1
fi
