@echo off
call mongorestore --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mongorestore.exe
echo Please add your/mongo_path/bin into %PATH%
GOTO END

:Normal
mongorestore --db achievement --drop mongodb_dump/achievement
mongorestore --db app_component  --drop mongodb_dump/app_component
mongorestore --db audit --drop mongodb_dump/audit
mongorestore --db campaign --drop mongodb_dump/campaign
mongorestore --db get_started --drop mongodb_dump/get_started
mongorestore --db invite --drop mongodb_dump/invite
mongorestore --db message --drop mongodb_dump/message
mongorestore --db stat --drop mongodb_dump/stat
echo Dump file loaded
GOTO END

:END
pause