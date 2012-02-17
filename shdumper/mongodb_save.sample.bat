@echo off
call mongorestore --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mongorestore.exe
echo Please add your/mongo_path/bin into %PATH%
GOTO END

:Normal
mongodump --db socialhappen --out mongodb_dump
echo Dump file saved
GOTO END

:END
pause