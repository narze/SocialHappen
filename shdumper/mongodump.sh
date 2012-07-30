#!/bin/bash

#--
# CONFIG
USERNAME=$1
PASSWORD=$2
PORT=$3
TIMESTAMP=$4

PHYS_DIR=`pwd -P`
DBS="socialhappen socialhappen_beta"
DUMP_LOCATION=$PHYS_DIR/../application/backup
LOGS_LOCATION=$PHYS_DIR/../application/backup
DIR_NAME=sh_mongo-$TIMESTAMP
LOG_FILE=$LOGS_LOCATION/log-mongodump-$TIMESTAMP
DIR_NAME_ABS=$DUMP_LOCATION/$DIR_NAME
COPY_SSH_DEST=server1:$DIR_NAME_ABS

AUTH=""

if [ -n "$USERNAME" ]
	then
	AUTH=" -u $USERNAME -p $PASSWORD";
fi
echo "Starting backup `date`" >> $LOG_FILE

for DB in $DBS
do
	echo "Backup: $DB" >> $LOG_FILE
  echo "Command : mongodump --db $DB --port $PORT $AUTH --out $DIR_NAME_ABS >> $LOG_FILE"
	./mongodump --db $DB --port $PORT $AUTH --out $DIR_NAME_ABS
#	tar -czf $DUMP_LOCATION/$DB-`date +%Y%m%d`.tgz --remove-files --directory $DUMP_LOCATION $DIR_NAME >> $LOG_FILE
#	scp	$DUMP_LOCATION/$DB-`date +%Y%m%d`.tgz $COPY_SSH_DEST >> $LOG_FILE
done
echo "Backup Finished" >> $LOG_FILE
