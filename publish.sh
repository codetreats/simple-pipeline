#!/bin/bash
set -e
cd $(dirname "$0")
BASEDIR=$(pwd)

VERSION=$1
if [[ $VERSION == "" ]]
then
    echo "VERSION not set"
    exit 1
fi

# build image
./build.sh $VERSION

git commit
git tag $VERSION
docker push codetreats/simple-pipeline:$VERSION