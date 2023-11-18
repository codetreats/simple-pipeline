#!/bin/bash
LEVEL=$1
MSG=$2
if [[ $3 == "" ]] ; then
    SRC=$MONITOR_SRC
else
    SRC=$3
fi
SECRET=$MONITOR_SECRET

if [[ $LEVEL == "" ]] ; then
    exit
fi
if [[ $MSG == "" ]]  ; then
    exit
fi
if [[ $SRC == "" ]] ; then
    exit
fi
if [[ $SECRET == "" ]] ; then
    exit
fi
if [[ $MONITOR_URL == "" ]] ; then
    exit
fi

echo "Update Monitor: $LEVEL $MSG $SRC"

curl -G -d "src=$SRC" --data-urlencode "val=$MSG" -d "level=$LEVEL" -d "secret=$SECRET" $MONITOR_URL
