#!/bin/bash
chmod +x /pipeline/src/*.sh
chmod +x /job/*.sh
chmod +x /*.sh
chmod 644 /etc/cron.d/cronjob
sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
service apache2 start
echo $JOB_TITLE > /var/www/html/status/title.txt
dos2unix /etc/cron.d/cronjob
cron
crontab /etc/cron.d/cronjob
/pipeline/src/htaccess.sh
/pipeline/src/prepare.sh
/pipeline/src/runner.sh