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

ACTUAL_VERSION="$("$MDE_BIN" -qV)"
echo "current version is: ${ACTUAL_VERSION}"

if [ "$#" -eq 0 ]; then
    echo "usage: $0 <version-number> [<date>]"
    exit 1
fi

VERSION="$1"
_DATE="${2:-$(git log -1 --format="%ci" --date=short | cut -s -f 1 -d ' ')}"

sed -i -e "s|const MDE_VERSION.*= '.*'|const MDE_VERSION = '${VERSION}'|;s|const MDE_DATE.*= '.*'|const MDE_DATE = '${_DATE}'|" "$MDE_PHP" \
    && sed -i -e "s|^Version: .*$|Version: ${VERSION}|;s|^Date: .*$|Date: ${_DATE}|" "$MDMAN_FILE" \
    && sed -i -e "s|^Version: .*$|Version: ${VERSION}|;s|^Date: .*$|Date: ${_DATE}|" "$MDDOC_FILE" \
    && ${HERE}/make-manpage.sh \
    && echo "version number updated in '${MDE_PHP}', '${MDDOC_FILE}' and '${MDMAN_FILE}' and manpages regenerated" \
    || echo "an error occurred!" ;

exit 0
