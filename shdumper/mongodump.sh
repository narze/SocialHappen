#!/bin/bash

#--
# CONFIG
DBS="socialhappen_beta socialhappen"
DUMP_LOCATION=../application/backup
LOGS_LOCATION=../application/backup
DIR_NAME=sh_mongo-`date -u +%Y%m%d_%H%M%S`
LOG_FILE=$LOGS_LOCATION/log-mongodump-`date -u +%Y%m%d_%H%M%S`
DIR_NAME_ABS=$DUMP_LOCATION/$DIR_NAME
COPY_SSH_DEST=server1:$DIR_NAME_ABS
# Uncomment and configure
echo "u"
read USERNAME
echo "p"
read PASSWORD
echo ": (27017)"
read PORT
##--

AUTH=""

if [ -n "$USERNAME" ]
	then
	AUTH=" -u $USERNAME -p $PASSWORD";
fi
echo "Starting backup `date`" >> $LOG_FILE

for DB in $DBS
do
	echo "Backup: $DB" >> $LOG_FILE
	mongodump --db $DB --port $PORT $AUTH --out $DIR_NAME_ABS >> $LOG_FILE
#	tar -czf $DUMP_LOCATION/$DB-`date +%Y%m%d`.tgz --remove-files --directory $DUMP_LOCATION $DIR_NAME >> $LOG_FILE
#	scp	$DUMP_LOCATION/$DB-`date +%Y%m%d`.tgz $COPY_SSH_DEST >> $LOG_FILE
done
echo "Backup Finished" >> $LOG_FILE