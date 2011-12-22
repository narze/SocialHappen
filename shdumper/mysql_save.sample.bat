@echo off
call mysql --version
if ERRORLEVEL 0 GOTO Normal
GOTO Abort

:Abort
echo Not found mysql.exe
echo Please add your/mysql_path/bin into %PATH%
GOTO END

:Normal
mysqldump -u root --add-drop-table -v socialhappen > mysql_dump.sql
echo Dump file saved
GOTO END

:END
pause