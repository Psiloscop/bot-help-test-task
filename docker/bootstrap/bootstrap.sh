#!/bin/bash

# Waining Symfony initialization
sleep 10

# Generating OpenRC services for Symfony workers to start based on $MESSENGER_WORKER_COUNT
openrcConfPath="/etc/init.d/"
for (( num = 0; num < $MESSENGER_WORKER_COUNT; num++ ))
do
  qNum="queue$num"
  sed -e "s|<queue_name>|$qNum|;" ${openrcConfPath}symfony-worker-template > ${openrcConfPath}symfony-worker-${qNum}
  chmod +x ${openrcConfPath}symfony-worker-${qNum}
  rc-update add symfony-worker-${qNum} default
  rc-service symfony-worker-${qNum} start
done

# Starting PHP server
php -S 0.0.0.0:3910 -t /app/public