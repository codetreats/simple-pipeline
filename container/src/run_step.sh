#!/bin/bash
DT=$(date "+%F_%T")
STATUS=$1
DESCRIPTION=$2
CMD=$3
echo "$DT:$DESCRIPTION" >> $STATUS
$CMD