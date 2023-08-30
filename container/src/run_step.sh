#!/bin/bash
DT=$(date "+%F_%T")
STATUS=$1
DESCRIPTION=$2
CMD=$3
echo ""
echo "#############################################"
echo "$DT:$DESCRIPTION"
echo "#############################################"
echo "$DT:$DESCRIPTION" >> $STATUS
$CMD