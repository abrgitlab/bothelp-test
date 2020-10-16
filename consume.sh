#!/bin/bash

while true
do
  QUEUE_LEN=`bin/console check-queue`

  if [ $QUEUE_LEN -eq 0 ]
  then
    exit 0
  fi

  NEW_THREADS_COUNT=$QUEUE_LEN;
  if [ $NEW_THREADS_COUNT -gt 300 ]
  then
    NEW_THREADS_COUNT=300
  fi

  for (( I = 0; I < $NEW_THREADS_COUNT; I++ ))
  do
	  bin/console consume-data &
  done
done

exit 0
