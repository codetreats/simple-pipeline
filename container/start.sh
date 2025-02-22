#!/bin/bash
set -e
chmod +x /pipeline/*.sh
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
dos2unix /bin/debug
dos2unix /bin/release
dos2unix /bin/menu
dos2unix /bin/crontrigger
dos2unix /etc/cron.d/cronjob

/pipeline/apache.sh
/pipeline/htaccess.sh
/pipeline/setup.sh
/pipeline/prepare.sh

cron
crontab /etc/cron.d/cronjob

/pipeline/killer.sh &
/pipeline/runner.sh