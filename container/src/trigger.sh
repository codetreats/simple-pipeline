#!/bin/bash
true > /var/www/html/pipeline/trigger/trigger.flag
for PARAM in "$@"
do
    echo "$PARAM" >> /var/www/html/pipeline/trigger/trigger.flag
done