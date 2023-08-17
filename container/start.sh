#!/bin/bash
chmod +x /pipeline/src/*.sh
chmod +x /job/job.sh
service apache2 start
echo $JOB_TITLE > /var/www/html/status/title.txt
/pipeline/src/runner.sh
