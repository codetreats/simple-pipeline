#!/bin/bash
DT=$(date "+%F_%T")
STATUS=$1
DESCRIPTION=$2
CMD=$3
echo "[STEP] "
echo "[STEP] #############################################"
echo "[STEP] $DT:$DESCRIPTION"
echo "[STEP] #############################################"
echo "$DT:$DESCRIPTION" >> $STATUS
$CMD