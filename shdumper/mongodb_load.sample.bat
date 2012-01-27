@echo off
call mongorestore --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mongorestore.exe
echo Please add your/mongo_path/bin into %PATH%
GOTO END

:Normal
mongo --eval "connect('localhost/achievement').dropDatabase();"
mongorestore --db achievement --drop mongodb_dump/achievement
mongo --eval "connect('localhost/app_component').dropDatabase();"
mongorestore --db app_component  --drop mongodb_dump/app_component
mongo --eval "connect('localhost/audit').dropDatabase();"
mongorestore --db audit --drop mongodb_dump/audit
mongo --eval "connect('localhost/campaign').dropDatabase();"
mongorestore --db campaign --drop mongodb_dump/campaign
mongo --eval "connect('localhost/get_started').dropDatabase();"
mongorestore --db get_started --drop mongodb_dump/get_started
mongo --eval "connect('localhost/invite').dropDatabase();"
mongorestore --db invite --drop mongodb_dump/invite
mongo --eval "connect('localhost/message').dropDatabase();"
mongorestore --db message --drop mongodb_dump/message
mongo --eval "connect('localhost/stat').dropDatabase();"
mongorestore --db stat --drop mongodb_dump/stat
mongo --eval "connect('localhost/socialhappen').dropDatabase();"
mongorestore --db socialhappen --drop mongodb_dump/socialhappen
echo Dump file loaded
GOTO END

:END
pause