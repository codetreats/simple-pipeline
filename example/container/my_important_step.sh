#!/bin/bash
for i in $(seq 0 $1)
do
  echo "$i: Running"
  sleep 1
done