@echo off
call mongorestore --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mongorestore.exe
echo Please add your/mongo_path/bin into %PATH%
GOTO END

:Normal
mongo --eval "connect('localhost/socialhappen').dropDatabase();"
mongorestore --db socialhappen --drop mongodb_dump/socialhappen
echo Dump file loaded
GOTO END

:END
pause