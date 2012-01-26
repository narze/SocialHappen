@echo off
call mongorestore --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mongorestore.exe
echo Please add your/mongo_path/bin into %PATH%
GOTO END

:Normal
mongodump --db achievement --out mongodb_dump 
mongodump --db app_component --out mongodb_dump  
mongodump --db audit --out mongodb_dump 
mongodump --db campaign --out mongodb_dump 
mongodump --db get_started --out mongodb_dump 
mongodump --db invite --out mongodb_dump 
mongodump --db message --out mongodb_dump 
mongodump --db stat --out mongodb_dump
mongodump --db socialhappen --out mongodb_dump
echo Dump file saved
GOTO END

:END
pause