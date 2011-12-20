@echo off
call mysql --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mysql.exe
echo Please add your/mysql_path/bin into %PATH%
GOTO END

:Normal
echo Loading dump file...
mysql -u root socialhappen < mysql_dump/sh.sql
echo Dump file loaded
GOTO END

:END
pause