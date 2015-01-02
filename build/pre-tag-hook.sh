#!/bin/sh
#
# This file is part of the PHP-MarkdownExtended package.
#
# (c) Pierre Cassat <me@e-piwi.fr> and contributors
#
# For the full copyright and license information, please view the LICENSE
# file that was distributed with this source code.
#

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

# all necessary files
HERE=$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)
PHPBIN='php'
MDE_CONSOLE="bin/markdown-extended"
MARKDOWNEXTENDED="src/MarkdownExtended/MarkdownExtended.php"
MARKDOWNMANPAGE="docs/MANPAGE.md"
MARKDOWNMANPAGE_MAN="man/markdown-extended.man"
_VERSION="${TAG_NAME/v/}"
_DATE=$(git log -1 --format="%ci" --date=short | cut -s -f 1 -d ' ')

# process
if [ ! -f "$MARKDOWNEXTENDED" ]; then
    simple_error "!! > Source file '${MARKDOWNEXTENDED}' not found! (can't update version number and date)"
fi
if [ ! -f "$MARKDOWNMANPAGE" ]; then
    simple_error "!! > Manpage md file '${MARKDOWNMANPAGE}' not found! (can't update version number and date)"
fi
${HERE}/make-version.sh "$_VERSION" "$_DATE"
if [ ! -f "$MARKDOWNMANPAGE" ]; then
    simple_error "!! > Manpage file '${MARKDOWNMANPAGE_MAN}' not found!"
fi

debecho "> commiting new files ..."
git add "$MARKDOWNEXTENDED" "$MARKDOWNMANPAGE" "$MARKDOWNMANPAGE_MAN"
git commit -m "Automatic version number and date insertion"
LASTSHA="$(git log -1 --format="%H")"
git checkout wip && git cherry-pick "$LASTSHA"
git checkout dev && git cherry-pick "$LASTSHA"
git checkout master && git push origin master wip dev;

# Endfile
