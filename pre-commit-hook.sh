#!/bin/bash
#
# Replaces constants values in `src/MarkdownExtended/MarkdownExtended.php`
# and `docs/MANPAGE.md` with current `composer.json` values
#

# get a value from the current composer.json
get_value () {
    local MASK="\"$1\":.*?[^\\\]\","
#    echo $(cat "${COMPOSERJSON}" | grep -Po '"$1":.*?[^\\]",' | cut -s -f 4 -d '"')
    echo $(cat "${COMPOSERJSON}" | grep -Po "$MASK" | cut -s -f 4 -d '"')
}

# all necessary files
declare -x MDE_CONSOLE="`pwd`/bin/markdown_extended"
if [ ! -f "$MDE_CONSOLE" ]; then
    MDE_CONSOLE="`pwd`/../../bin/markdown_extended"
fi
declare -x COMPOSERJSON="`pwd`/composer.json"
if [ ! -f "$COMPOSERJSON" ]; then
    COMPOSERJSON="`pwd`/../../composer.json"
fi
declare -x MARKDOWNEXTENDED="`pwd`/src/MarkdownExtended/MarkdownExtended.php"
if [ ! -f "$MARKDOWNEXTENDED" ]; then
    MARKDOWNEXTENDED="`pwd`/../../src/MarkdownExtended/MarkdownExtended.php"
fi
declare -x MARKDOWNMANPAGE="`pwd`/docs/MANPAGE.md"
declare -x MARKDOWNMANPAGE_MAN="`pwd`/bin/markdown_extended.man"
if [ ! -f "$MARKDOWNMANPAGE" ]; then
    MARKDOWNMANPAGE="`pwd`/../../docs/MANPAGE.md"
    MARKDOWNMANPAGE_MAN="`pwd`/../../bin/markdown_extended.man"
fi

# process
if [ -f "$COMPOSERJSON" ]; then
    _version=$(get_value "version")
    _title=$(get_value "title")
    _homepage=$(get_value "homepage")
    _date=$(git log -1 --format="%ci" --date=short | cut -s -f 1 -d ' ')
    if [ -f "$MARKDOWNEXTENDED" ]; then
        sed -i '' -e "s|const MDE_VERSION = '.*'|const MDE_VERSION = '${_version}'|;s|const MDE_NAME = '.*'|const MDE_NAME = '${_title}'|;s|const MDE_SOURCES = '.*'|const MDE_SOURCES = '${_homepage}'|" "$MARKDOWNEXTENDED";
        git add "$MARKDOWNEXTENDED"
    fi
    if [ -f "$MARKDOWNMANPAGE" ]; then
        sed -i '' -e "s|^Version: .*$|Version: ${_version}|;s|^Date: .*$|Date: ${_date}|" "$MARKDOWNMANPAGE";
        git add "$MARKDOWNMANPAGE"
        php "$MDE_CONSOLE" -q -f man -o "$MARKDOWNMANPAGE_MAN" "$MARKDOWNMANPAGE"
        if [ -f "$MARKDOWNMANPAGE" ]; then
            git add "$MARKDOWNMANPAGE_MAN"
        fi
    fi
fi

exit 0

# Endfile
