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
cd $BASEDIR/container
docker build -t codetreats/simple-pipeline:$VERSION .