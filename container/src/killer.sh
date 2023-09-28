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

while true; do
    if [ ! -f $CANCELLED_FILE ] 
    then
        inotifywait -t 600 $TRIGGER_DIR
    fi
    if [ -f $CANCELLED_FILE ]
    then
        rm $CANCELLED_FILE 
        killall job.sh
    fi
    sleep 1
done