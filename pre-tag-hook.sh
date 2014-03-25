#!/bin/sh
#
# An example hook script called before version tag creation.
# This will receive three arguments:
# - $1: the project path to work on
# - $2: the tag name to create 
# - $3: the branch name 
#
# This script is used by <http://github.com/atelierspierrot/dev-tools>
# when a version tag is built running from project root directory:
#
#       ./devtools.sh [-vi] version-tag
#
# To enable this hook, define the `DEFAULT_VERSIONTAG_HOOK` configuration variable on
# this script in your `.devtools` configuration file.
#
# -----------------------
#
# Replaces constants values in `src/MarkdownExtended/MarkdownExtended.php`
# and `docs/MANPAGE.md` with current `composer.json` values
#

PROJECT_PATH="$1"
TAG_NAME="$2"
BRANCH_NAME="$3"

# get a value from the current composer.json
get_value () {
    local MASK="\"$1\":.*?[^\\\]\","
#    echo $(cat "${COMPOSERJSON}" | grep -Po '"$1":.*?[^\\]",' | cut -s -f 4 -d '"')
    echo $(cat "${COMPOSERJSON}" | grep -Po "$MASK" | cut -s -f 4 -d '"')
}

# all necessary files
PHPBIN='php'
MDE_CONSOLE="bin/markdown-extended"
COMPOSERJSON="composer.json"
MARKDOWNEXTENDED="src/MarkdownExtended/MarkdownExtended.php"
MARKDOWNMANPAGE="docs/MANPAGE.md"
MARKDOWNMANPAGE_MAN="bin/markdown-extended.man"
_VERSION="${TAG_NAME/v/}"
_DATE=$(git log -1 --format="%ci" --date=short | cut -s -f 1 -d ' ')

# process
if [ -f "$COMPOSERJSON" ]; then
    if [ -f "$MARKDOWNEXTENDED" ]; then
        sed -i '' -e "s|const MDE_VERSION = '.*'|const MDE_VERSION = '${_VERSION}'|" "$MARKDOWNEXTENDED";
        git add "$MARKDOWNEXTENDED"
    else
        verecho "!! > Source file '${MARKDOWNEXTENDED}' not found! (can't update version number and date)"
    fi
    if [ -f "$MARKDOWNMANPAGE" ]; then
        sed -i '' -e "s|^Version: .*$|Version: ${_VERSION}|;s|^Date: .*$|Date: ${_DATE}|" "$MARKDOWNMANPAGE";
        git add "$MARKDOWNMANPAGE"
        "$PHPBIN" "$MDE_CONSOLE" -q -f man -o "$MARKDOWNMANPAGE_MAN" "$MARKDOWNMANPAGE"
        if [ -f "$MARKDOWNMANPAGE" ]; then
            git add "$MARKDOWNMANPAGE_MAN"
        else
            verecho "!! > Manpage file '${MARKDOWNMANPAGE_MAN}' not found!"
        fi
    else
        verecho "!! > Manpage md file '${MARKDOWNMANPAGE}' not found! (can't update version number and date)"
    fi
else
    verecho "!! > Composer file '${COMPOSERJSON}' not found! (can't update version number and date)"
fi

git commit -m "Automatic version number and date insertion" && \
    LASTSHA=`git log -1 --format="%H"` && \
    git checkout wip && git cherry-pick $LASTSHA && \
    git checkout dev && git cherry-pick $LASTSHA && \
    git checkout master && git push origin master wip dev;

# Endfile
