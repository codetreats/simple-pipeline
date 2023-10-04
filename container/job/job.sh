#!/bin/bash
###############################################
RUNSTEP=/pipeline/src/run_step.sh $STATUS
###############################################
set -e
STATUS=$1

# Enter your pipeline steps below
# Syntax: $RUNSTEP $STATUS <DESCRIPTION> <COMMAND>
$RUNSTEP $STATUS "Step 1" "/job/my_important_step.sh 500"
$RUNSTEP $STATUS "Step 2" "/job/my_important_step.sh 2"
$RUNSTEP $STATUS "Step 3" "/job/my_important_step.sh 3"
$RUNSTEP $STATUS "Step 4" "/job/my_important_step.sh 4"