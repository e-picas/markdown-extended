#!/usr/bin/env bash
#
# This file is part of the PHP-MarkdownExtended package.
#
# (c) Pierre Cassat <me@e-piwi.fr> and contributors
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#
# this install script is taken "as-is" from <https://github.com/sstephenson/bats>
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
if [ -z "$1" ]; then
    {   echo "usage: $0 <prefix>"
        echo "  e.g. $0 /usr/local"
    } >&2
    exit 1
fi

ROOT_DIR="$(abs_dirname "$0")"
mkdir -p "$PREFIX"/{bin,share/man/{man3,man7}}
cp -R "$ROOT_DIR"/bin/* "$PREFIX"/bin
echo "markdown-extended is now installed to $PREFIX/bin/markdown-extended"
echo "to begin, run: 'markdown-extended --help'"
cp "$ROOT_DIR"/man/markdown-extended.3.man "$PREFIX"/share/man/man3/markdown-extended.3
echo "for usage manpage, run: 'man 3 markdown-extended'"
cp "$ROOT_DIR"/man/markdown-extended.7.man "$PREFIX"/share/man/man3/markdown-extended.7
echo "for documentation manpage, run: 'man 7 markdown-extended'"
