#!/bin/bash
cd $(dirname "$0")
BASEDIR=$(pwd)

OK=$1
LOG=$2

if [[ $OK == "OK" ]] ;  then
    LEVEL=$MAIL_LEVEL_OK
else
    LEVEL=$MAIL_LEVEL_FAIL
fi


CONTENT=/tmp/maillogs.txt

if [ "$LEVEL" == "" ] || [ "$LEVEL" == "OFF" ] ; then
    exit
fi

true > $CONTENT

HAS_ERROR=$(cat $LOG | grep -e "\[ERROR\]" | wc -l)
HAS_WARNING=$(cat $LOG | grep -e "\[WARNING\]" -e "\[ERROR\]"  | wc -l)
HAS_INFO=$(cat $LOG | grep -e "\[INFO\]" -e "\[WARNING\]" -e "\[ERROR\]" | wc -l)

echo "LEVEL: $LEVEL"
echo "HAS_ERROR: $HAS_ERROR"
echo "HAS_WARNING: $HAS_WARNING"
echo "HAS_INFO: $HAS_INFO"

if [ "$LEVEL" == "ERROR" -a "$HAS_ERROR" -gt 0 ]; then
    cat $LOG | grep -e "\[ERROR\]" -e "\[STEP\]" | sed -e 's/\[STEP\] //g' > $CONTENT
fi

if [ "$LEVEL" == "WARNING" -a "$HAS_WARNING" -gt 0 ]; then
    cat $LOG | grep -e "\[WARNING\]" -e "\[ERROR\]" -e "\[STEP\]" | sed -e 's/\[STEP\] //g' > $CONTENT
fi

if [ "$LEVEL" == "INFO" -a "$HAS_INFO" -gt 0 ]; then
    cat $LOG | grep -e "\[INFO\]" -e "\[WARNING\]" -e "\[ERROR\]" -e "\[STEP\]" | sed -e 's/\[STEP\] //g' > $CONTENT
fi

if [ "$LEVEL" == "DEBUG" ] ; then
    cat $LOG | sed -e 's/\[STEP\] //g' > $CONTENT         
fi

if [[ $HOST_URL != "" ]] ; then
    FILENAME=$(basename $LOG)
    LINK="${HOST_URL}/pipeline/index.html"
fi

if [[ $(cat $CONTENT) != "" ]] ; then
    php mail.php "[$OK] Logs from $JOB_TITLE" "$LINK $(cat $CONTENT)"    
else
    if [[ "$LEVEL" == "LINK" ]] ; then
        php mail.php "[$OK] Logs from $JOB_TITLE" "$LINK"    
    fi
fi