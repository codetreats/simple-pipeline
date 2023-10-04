#!/bin/bash
set -e
FILE=/var/www/html/pipeline/logs/.htaccess

echo "HTUSERS=$HTUSERS"

if [[ -z "${HTUSERS}" ]] ; then
  exit 
fi

echo "Enable htaccess"
echo "AuthType Basic" > $FILE
echo "AuthName \"Restricted Files\"" >> $FILE
echo "AuthUserFile \"$HTUSERS\"" >> $FILE
echo "Require valid-user" >> $FILE
chown www-data:www-data $FILE