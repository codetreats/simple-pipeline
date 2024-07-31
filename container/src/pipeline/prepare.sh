#!/bin/bash
set -e
if [ -f /job/prepare.sh ] ; then
  chmod +x /job/prepare.sh
  /job/prepare.sh
fi