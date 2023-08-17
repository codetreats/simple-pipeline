#!/bin/bash
###############################################
RUNSTEP=/pipeline/src/run_step.sh $STATUS
###############################################
set -e
STATUS=$1

# Enter your pipeline steps below
# Syntax: $RUNSTEP $STATUS <DESCRIPTION> <COMMAND>
$RUNSTEP $STATUS "Step 1" "sleep 3"
$RUNSTEP $STATUS "Step 2" "sleep 10"
$RUNSTEP $STATUS "Step 3" "sleep 8"
$RUNSTEP $STATUS "Step 4" "sleep 310"