#!/usr/bin/env bash

set -e

VERSION=$1
VERSION_REGEX="^v[0-9]+\.[0-9]+\.[0-9]+$"

if [[ ! $VERSION =~ $VERSION_REGEX ]]
then
    echo "Version must match the format vX.X.X"

    exit 1
fi

git tag $VERSION
git push origin --tags

for REMOTE in routing state phase
do
    echo ""
    echo ""
    echo "Releasing $REMOTE";

    TMP_DIR="/tmp/phased-php"
    REMOTE_URL="git@github.com:reed-jones/phase-$REMOTE-php.git"

    rm -rf $TMP_DIR;
    mkdir $TMP_DIR;

    (
        cd $TMP_DIR;

        git clone $REMOTE_URL .
        git checkout master
        git tag $VERSION
        git push origin --tags
    )
done
