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
WWW_USER=www-data
# KEEP_LOGS=30 # set maximum days that logs are kept (by default it is set as environment variable)
###############################################

while true; do
    if [ ! -f $TRIGGER_FILE ] 
    then
        inotifywait -t 600 $TRIGGER_DIR
    fi
    if [ -f $TRIGGER_FILE ]
    then
        PARAMS=$(cat $TRIGGER_FILE)
        rm $TRIGGER_FILE
        
        ## prepare job
        DT=$(date "+%F_%T")
        STATUS=$STATUS_DIR/$DT.log
        LOG=$LOG_DIR/$DT.log

        echo "$DT"":START" > $STATUS
        touch $LOG
        chown $WWW_USER:$WWW_USER $STATUS
        chown $WWW_USER:$WWW_USER $LOG

        # run job
        $BEFORE $PARAMS
        $JOB $STATUS $PARAMS > $LOG 2>&1
        RESULT=$?
        $AFTER $RESULT $PARAMS

        # evaluate result
        DT=$(date "+%F_%T")
        if [[ $RESULT -eq 0 ]] ; then        
            echo "$DT"":END" >> $STATUS
        else
            echo "$DT"":FAILED" >> $STATUS
        fi

        # remove old logs
        find $LOG_DIR -type f -mtime +$KEEP_LOGS ! -name "title.txt" -exec rm {} \;
        find $STATUS_DIR -type f -mtime +$KEEP_LOGS ! -name "title.txt" -exec rm {} \;
    fi
    sleep 10
done