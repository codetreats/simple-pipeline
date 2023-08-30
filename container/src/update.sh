#!/bin/bash
SRC=$1
LEVEL=$2
MSG=$3
SECRET=$MONITOR_SECRET

curl -G -d "src=$SRC" --data-urlencode "val=$MSG" -d "level=$LEVEL" -d "secret=$SECRET" $MONITOR_URL 