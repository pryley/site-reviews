#!/bin/sh
# By Paul Ryley, based on work by Mike Jolley
# License: GPLv3
# Version: 2.3.0

# ----- START EDITING HERE -----

ASSETS_DIR="+/assets"
GIT_BRANCH="main"
PLUGIN_SLUG="site-reviews"

# ----- STOP EDITING HERE -----

set -e
clear

# VARS
ROOT_PATH=$(pwd)"/"
PLUGIN_VERSION=`grep "Version:" $ROOT_PATH$PLUGIN_SLUG.php | awk -F' ' '{print $NF}' | tr -d '\r'`
SVN_REPO="https://plugins.svn.wordpress.org/"${PLUGIN_SLUG}"/"
SVN_REPO_DIR=".svn"
TEMP_GITHUB_REPO=${PLUGIN_SLUG}"-git"

# BUILD, THEN CHECK AND CONFIRM THE CHECKLIST
make build:all
clear
php vendor/pryley/wp-plugin-tooling/bin/release-check \
	--ask="Updated the 'What's New' page for major/minor version updates?" \
	--ask="Updated the readme.txt changelog and prepended it to changelog.txt?" \
	--ask="Verified that all addons work correctly with the update?" \
	--ask="Committed all changes to the ${GIT_BRANCH} branch on GitHub?" \
	--ask="All CI checks have passed?"
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
RELEASE_COMMIT=`git rev-parse $GIT_BRANCH`
git archive $RELEASE_COMMIT | tar -x -f - -C $ROOT_PATH$TEMP_GITHUB_REPO || { echo "Unable to archive/copy branch."; exit 1; }

echo ""
read -p "PRESS [ENTER] TO DEPLOY BRANCH "$GIT_BRANCH

# MOVE INTO SVN DIR
cd $ROOT_PATH$SVN_REPO_DIR

# COPY ASSETS to SVN DIR
cp -R $ROOT_PATH$ASSETS_DIR/. $ROOT_PATH$SVN_REPO_DIR/assets/

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
svn commit -m "Release "${PLUGIN_VERSION}", see readme.txt for the changelog." --config-option servers:global:http-timeout=7200 || { echo "Unable to commit."; exit 1; }

# TAG THE RELEASE ON GITHUB
cd $ROOT_PATH
GIT_TAG="v${PLUGIN_VERSION}"
if git ls-remote --exit-code --tags origin "refs/tags/${GIT_TAG}" >/dev/null; then
	echo "The ${GIT_TAG} tag is already on GitHub."
elif git rev-parse -q --verify "refs/tags/${GIT_TAG}" >/dev/null; then
	echo "The ${GIT_TAG} tag exists here but not on GitHub. Check it and push it with: git push origin ${GIT_TAG}"
else
	echo "Tagging ${RELEASE_COMMIT} as ${GIT_TAG}"
	git tag -a "$GIT_TAG" "$RELEASE_COMMIT" -m "" || { echo "Unable to create the git tag."; exit 1; }
	git push origin "refs/tags/${GIT_TAG}" || { echo "Unable to push the git tag."; exit 1; }
fi

# REMOVE THE TEMP DIRS
echo "CLEANING UP"
rm -Rf $ROOT_PATH$TEMP_GITHUB_REPO

# DONE, BYE
echo "All DONE"
