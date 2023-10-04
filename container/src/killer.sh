#!/bin/bash
###############################################
HTML_DIR=/var/www/html
BEFORE=/job/before.sh
JOB=/job/job.sh
AFTER=/job/after.sh
TRIGGER_DIR=$HTML_DIR/trigger
LOG_DIR=$HTML_DIR/logs
STATUS_DIR=$HTML_DIR/status
TRIGGER_FILE=$TRIGGER_DIR/trigger.flag
CANCELLED_FILE=$TRIGGER_DIR/cancel.flag
WWW_USER=www-data
###############################################

kill_all()
{    
   local PID=$1
   local TASK_IDS=$(ls /proc/$PID/task)
   for TASK_ID in $TASK_IDS
   do
      if [ -e /proc/$PID/task/$TASK_ID/children ]
      then
         local CHILDREN=$(cat /proc/$PID/task/$TASK_ID/children)
         for CHILD in $CHILDREN
         do     
            kill_all $CHILD
         done
      fi
   done
   kill $PID
}


while true; do
    if [ ! -f $CANCELLED_FILE ] 
    then
        inotifywait -t 600 $TRIGGER_DIR
    fi
    if [ -f $CANCELLED_FILE ]
    then
        rm $CANCELLED_FILE 
        kill_all $(pgrep job.sh)
    fi
    sleep 1
done