#!/bin/bash
set -e

if [ -f /prepared.flag ] ; then
  exit
fi

if [ -f /job/setup.sh ] ; then
  chmod +x /job/setup.sh
  /job/setup.sh
fi
touch /prepared.flag