#!/bin/sh
# By Paul Ryley, based on work by Mike Jolley
# License: GPLv3
# Version: 2.1.0

# ----- START EDITING HERE -----

ASSETS_DIR="+/assets"
GIT_BRANCH="main"
PLUGIN_SLUG="site-reviews"

# ----- STOP EDITING HERE -----

set -e
clear

# VARS
ROOT_PATH=$(pwd)"/"
MIN_PHP_VERSION=`perl -lne 'm{Requires PHP:?\s+(.+)} and print $1' ${ROOT_PATH}${PLUGIN_SLUG}.php`
MIN_WORDPRESS_VERSION=`perl -lne 'm{Requires at least:?\s+(.+)} and print $1' ${ROOT_PATH}${PLUGIN_SLUG}.php`
PLUGIN_VERSION=`grep "Version:" $ROOT_PATH$PLUGIN_SLUG.php | awk -F' ' '{print $NF}' | tr -d '\r'`
STABLE_VERSION=`grep "^Stable tag:" ${ROOT_PATH}readme.txt | awk -F' ' '{print $NF}' | tr -d '\r'`
SVN_REPO="https://plugins.svn.wordpress.org/"${PLUGIN_SLUG}"/"
SVN_REPO_DIR=".svn"
TEMP_GITHUB_REPO=${PLUGIN_SLUG}"-git"
TESTED_UP_TO_VERSION=`perl -lne 'm{Tested up to:?\s+(.+)} and print $1' ${ROOT_PATH}readme.txt`
CHANGELOG_DATE=`grep -e "^= ${PLUGIN_VERSION} (.*) =" ${ROOT_PATH}readme.txt | grep -o '....-..-..' | awk -F' ' '{print $NF}' | tr -d '\r'`

# CHECK GIT STATUS
make build
clear
if [[ ! -z $(git status --porcelain=v2 2>/dev/null) ]]; then
	echo "\n❌ \033[0;31mYou forgot to commit changes.\033[0m\n"
	exit 1;
fi

# ASK INFO
echo "--------------------------------------------"
echo "Deploy to WordPress.org SVN                 "
echo "--------------------------------------------"
echo "Plugin version: $PLUGIN_VERSION             "
echo "Stable version: $STABLE_VERSION             "
echo "--------------------------------------------"
echo ""

if [[ "$PLUGIN_VERSION" != "$STABLE_VERSION" && "$STABLE_VERSION" != "trunk" ]]; then
	echo "Version mismatch. Exiting..."
	echo ""
	exit 1;
else
	echo "Before continuing, confirm that you have done the following:"
fi

echo ""
read -p " - Updated the 'Minimum plugin requirements' in readme.txt?"
read -p " - Updated the 'Requires at least: ${MIN_WORDPRESS_VERSION}' in ${PLUGIN_SLUG}.php?"
read -p " - Updated the 'Requires PHP: ${MIN_PHP_VERSION}' in ${PLUGIN_SLUG}.php?"
read -p " - Updated the 'Tested up to: ${TESTED_UP_TO_VERSION}' in readme.txt?"
read -p " - Updated the 'What's New' page for major/minor version updates?"
read -p " - Updated the readme.txt changelog and prepended it to changelog.txt?"
read -p " - Updated the screenshots?"
read -p " - Updated wpml-config.xml?"
read -p " - Verified compatibility with PHP v${MIN_PHP_VERSION} -> latest?"
read -p " - Verified compatibility with Wordpress v${MIN_WORDPRESS_VERSION} -> latest?"
read -p " - Verified the changelog release date ($CHANGELOG_DATE)?"
read -p " - Verified that all addons work correctly with the update?"
read -p " - Committed all changes to the ${GIT_BRANCH} branch on GITHUB?"
read -p " - PHPUnit and PHPStan has passed all inspections?"
read -p " - Scrutinizer has passed all inspections?"
echo ""
read -p "PRESS [ENTER] TO BEGIN RELEASING "${PLUGIN_VERSION}
clear

# DELETE OLD TEMP DIRS
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO

# CHECKOUT SVN DIR IF NOT EXISTS
if [[ ! -d $SVN_REPO_DIR ]]; then
	echo "Checking out WordPress.org plugin repository"
	mkdir -p $ROOT_PATH$SVN_REPO_DIR
	cd $ROOT_PATH$SVN_REPO_DIR
	svn checkout $SVN_REPO . || { echo "Unable to checkout repo."; exit 1; }
	cd $ROOT_PATH
fi

# LIST BRANCHES
clear
git fetch origin

# Switch Branch
echo "Switching to main branch"
mkdir -p $ROOT_PATH$TEMP_GITHUB_REPO
git archive $GIT_BRANCH | tar -x -f - -C $ROOT_PATH$TEMP_GITHUB_REPO || { echo "Unable to archive/copy branch."; exit 1; }

echo ""
read -p "PRESS [ENTER] TO DEPLOY BRANCH "$GIT_BRANCH

# MOVE INTO SVN DIR
cd $ROOT_PATH$SVN_REPO_DIR

# COPY ASSETS to SVN DIR
cp $ROOT_PATH/$ASSETS_DIR/* $ROOT_PATH$SVN_REPO_DIR/assets/

# UPDATE SVN
echo "Updating SVN"
svn update || { echo "Unable to update SVN."; exit 1; }

# DELETE TRUNK
echo "Replacing trunk"
rm -Rf $ROOT_PATH$SVN_REPO_DIR/trunk/

# COPY GIT DIR TO TRUNK
cp -R $ROOT_PATH$TEMP_GITHUB_REPO $ROOT_PATH$SVN_REPO_DIR/trunk/

# DO THE ADD ALL NOT KNOWN FILES UNIX COMMAND
svn add --force * --auto-props --parents --depth infinity -q

# DO THE REMOVE ALL DELETED FILES UNIX COMMAND
MISSING_PATHS=$( svn status | sed -e '/^!/!d' -e 's/^!//' )

# iterate over filepaths
for MISSING_PATH in $MISSING_PATHS; do
    svn rm --force "$MISSING_PATH"
done

# COPY TRUNK TO TAGS/$PLUGIN_VERSION
echo "Copying trunk to new tag"
svn copy trunk tags/${PLUGIN_VERSION} || { echo "Unable to create tag."; exit 1; }

# DO SVN COMMIT
clear
echo "Showing SVN status"
svn status

# PROMPT USER
echo ""
read -p "PRESS [ENTER] TO COMMIT RELEASE "${PLUGIN_VERSION}" TO WORDPRESS.ORG SVN"
echo ""

# DEPLOY
echo ""
echo "Committing to WordPress.org...this may take a while."
svn commit -m "Release "${PLUGIN_VERSION}", see readme.txt for the changelog." || { echo "Unable to commit."; exit 1; }

# REMOVE THE TEMP DIRS
echo "CLEANING UP"
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO

# DONE, BYE
echo "All DONE"
