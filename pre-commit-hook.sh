#!/bin/bash
#
# Replaces constants values in `src/MarkdownExtended/MarkdownExtended.php`
# with current `composer.json` values
#

get_value () {
    local MASK="\"$1\":.*?[^\\\]\","
#    echo $(cat "${COMPOSERJSON}" | grep -Po '"$1":.*?[^\\]",' | cut -s -f 4 -d '"')
    echo $(cat "${COMPOSERJSON}" | grep -Po "$MASK" | cut -s -f 4 -d '"')
}

declare -x COMPOSERJSON="`pwd`/composer.json"
if [ ! -f "$COMPOSERJSON" ]; then
    COMPOSERJSON="`pwd`/../../composer.json"
fi
declare -x MARKDOWNEXTENDED="`pwd`/src/MarkdownExtended/MarkdownExtended.php"
if [ ! -f "$MARKDOWNEXTENDED" ]; then
    MARKDOWNEXTENDED="`pwd`/../../src/MarkdownExtended/MarkdownExtended.php"
fi
if [ -f "$COMPOSERJSON" -a -f "$MARKDOWNEXTENDED" ]; then
    _version=$(get_value "version")
    _title=$(get_value "title")
    _homepage=$(get_value "homepage")
    sed -i '' -e "s|const MDE_VERSION = '.*'|const MDE_VERSION = '${_version}'|;s|const MDE_NAME = '.*'|const MDE_NAME = '${_title}'|;s|const MDE_SOURCES = '.*'|const MDE_SOURCES = '${_homepage}'|" "$MARKDOWNEXTENDED";
fi
exit 0

# Endfile
