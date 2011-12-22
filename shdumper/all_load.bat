@echo OFF

@echo === Loading mysql ===
@echo | call mysql_load.bat

@echo === Loading mongo ===
@echo | call mongodb_load.bat

ping 1.1.1.1 -n 1 -w 2000 > nul