#!/bin/bash
###############################################
HTML_DIR=/var/www/html/pipeline
BEFORE=/job/before.sh
JOB=/job/job.sh
AFTER=/job/after.sh
TRIGGER_DIR=$HTML_DIR/trigger
LOG_DIR=$HTML_DIR/logs
STATUS_DIR=$HTML_DIR/status
TRIGGER_FILE=$TRIGGER_DIR/trigger.flag
ENABLED_FILE=$TRIGGER_DIR/enabled.flag
WWW_USER=www-data
# KEEP_LOGS=3 # set maximum days that logs are kept (by default it is set as environment variable)
###############################################

if [[ $KEEP_LOGS == "" ]] ; then
    KEEP_LOGS=3
fi

while true; do
    if [ ! -f $TRIGGER_FILE ] 
    then
        inotifywait -t 600 $TRIGGER_DIR
    fi
    if [ -f $TRIGGER_FILE ]
    then
        sleep 1 # wait until all params are written
        PARAMS=$(cat $TRIGGER_FILE | grep -v "HIDDEN_PARAM")
        OVERRIDE_MONITOR_SRC=$(cat $TRIGGER_FILE | grep "HIDDEN_PARAM_MONITOR_SRC" | cut -d "=" -f2)

        rm $TRIGGER_FILE
        ENABLED=1
        if [ -f $ENABLED_FILE ] ; then
            ENABLED=$(cat $ENABLED_FILE)
        fi
        
        if [ $ENABLED == "1" ] ; then
            ## prepare job
            DT=$(date "+%F_%T")
            STATUS=$STATUS_DIR/$DT.log
            LOG=$LOG_DIR/$DT.log

            echo "$DT"":START" > $STATUS
            touch $LOG
            chown $WWW_USER:$WWW_USER $STATUS
            chown $WWW_USER:$WWW_USER $LOG

            # run job
            RESULT=0
            if [[ -f $BEFORE ]] ; then
                echo "$DT"":BEFORE" >> $STATUS
                $BEFORE $PARAMS > $LOG 2>&1
                RESULT=$?
            fi
            if [[ $RESULT == 0 ]] ; then
                $JOB $STATUS $PARAMS >> $LOG 2>&1
                RESULT=$?
                if [[ $RESULT == 0 ]] ; then
                    if [[ -f $AFTER ]] ; then
                        DT=$(date "+%F_%T")
                        echo "$DT"":AFTER" >> $STATUS
                        $AFTER $RESULT $PARAMS >> $LOG 2>&1
                        RESULT=$?
                    fi
                fi
            fi            

            # evaluate result
            DT=$(date "+%F_%T")
            if [[ $RESULT -eq 0 ]] ; then        
                echo "$DT"":END" >> $STATUS
                /update.sh 0 "Pipeline finished" $OVERRIDE_MONITOR_SRC
            else
                echo "$DT"":FAILED" >> $STATUS
                /update.sh 3 "Pipeline failed" $OVERRIDE_MONITOR_SRC
            fi

            # remove old logs
            find $LOG_DIR -type f -mtime +$KEEP_LOGS ! -name "title.txt" -delete
            find $STATUS_DIR -type f -mtime +$KEEP_LOGS ! -name "title.txt" -delete
        fi
    fi
    sleep 10
done