#!/bin/bash
set -e

if [[ -f /apache/apache.conf ]] ; then
    cat /apache/apache.conf > /etc/apache2/sites-enabled/000-default.conf
fi

a2enmod headers
a2enmod ssl
service apache2 start