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

echo "Build Image"
./build.sh $VERSION
echo "Push Image"
docker tag codetreats/simple-pipeline:$VERSION codetreats/simple-pipeline:latest
docker push codetreats/simple-pipeline:$VERSION
docker push codetreats/simple-pipeline:latest
