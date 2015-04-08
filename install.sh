#!/usr/bin/env bash
#
# This file is part of the PHP-MarkdownExtended package.
#
# (c) Pierre Cassat <me@e-piwi.fr> and contributors
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# This install script is largely inspired by <https://github.com/sstephenson/bats>
#
set -e

resolve_link() {
    $(type -p greadlink readlink | head -1) "$1"
}

abs_dirname() {
    local cwd="$(pwd)"
    local path="$1"
    while [ -n "$path" ]; do
        cd "${path%/*}"
        local name="${path##*/}"
        path="$(resolve_link "$name" || true)"
    done
    pwd
    cd "$cwd"
}

PREFIX="$1"
TYPE="${2:-true}"
ROOT_DIR="$(abs_dirname "$0")"
if [ -z "$1" ]; then
    {   echo "usage: $0 <prefix> [global=true]"
        echo "  e.g. $0 /usr/local"
        echo "       $0 ~/bin false"
        echo "       $0 my/path 0"
    } >&2
    exit 1
fi

# global install
if [ "$TYPE" == 'true' ]; then
    # install binaries
    mkdir -p "$PREFIX"/{bin,share/man/{man3,man7}}
    cp -R "$ROOT_DIR"/bin/markdown-extended* "$PREFIX"/bin
    chmod a+x "$PREFIX"/bin/markdown-extended*
    # install manpages
    cp "$ROOT_DIR"/man/markdown-extended.3.man "$PREFIX"/share/man/man3/markdown-extended.3
    cp "$ROOT_DIR"/man/markdown-extended.7.man "$PREFIX"/share/man/man7/markdown-extended.7
    # info
    cat <<MSG
markdown-extended is now installed at "${PREFIX}/bin/markdown-extended"
to begin, run:
    'markdown-extended --help'
for usage manpage, run:
    'man 3 markdown-extended'
for documentation manpage, run:
    'man 7 markdown-extended'
MSG

# local install
else
    # install binaries
    cp -R "$ROOT_DIR"/bin/markdown-extended* "$PREFIX"/
    chmod a+x "$PREFIX"/markdown-extended*
    # install manpages
    cp "$ROOT_DIR"/man/markdown-extended.3.man "$PREFIX"/
    cp "$ROOT_DIR"/man/markdown-extended.7.man "$PREFIX"/
    # info
    cat <<MSG
markdown-extended is now installed at "${PREFIX}/markdown-extended"
to begin, run:
    '${PREFIX}/markdown-extended --help'
for usage manpage, run:
    'man ${PREFIX}/markdown-extended.3.man'
for documentation manpage, run:
    'man ${PREFIX}/markdown-extended.7.man'
MSG

fi
