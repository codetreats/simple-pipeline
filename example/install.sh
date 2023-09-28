#!/bin/bash
set -e
cd $(dirname "$0")
BASEDIR=$(pwd)

# remove old container
if [[ $(docker ps -q --filter "name=demo_pipeline"  | wc -l) -gt 0 ]]
then
     echo "Remove demo_pipeline"
     docker rm -f demo_pipeline
fi

docker image prune -f

# build image
../build.sh master-SNAPSHOT

docker-compose up --detach
