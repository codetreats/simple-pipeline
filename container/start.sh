#!/bin/bash
chmod +x /pipeline/src/*.sh
chmod +x /job/*.sh
chmod +x /*.sh
chmod 644 /etc/cron.d/cronjob
sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf
if [[ $APACHE_HTTP_PORT != "" ]] ; then
  sed -i "s/Listen 80/Listen $APACHE_HTTP_PORT/g" /etc/apache2/ports.conf
fi
if [[ $APACHE_HTTPS_PORT != "" ]] ; then
  sed -i "s/Listen 443/Listen $APACHE_HTTPS_PORT/g" /etc/apache2/ports.conf
fi

echo $JOB_TITLE > /var/www/html/pipeline/status/title.txt
service apache2 start
dos2unix /etc/cron.d/cronjob
cron
crontab /etc/cron.d/cronjob
/pipeline/src/htaccess.sh
/pipeline/src/prepare.sh
/pipeline/src/killer.sh &
/pipeline/src/runner.sh