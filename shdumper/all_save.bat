@echo OFF

@echo === Saving mysql ===
@echo | call mysql_save.bat

@echo === Saving mongo ===
@echo | call mongodb_save.bat

ping 1.1.1.1 -n 1 -w 2000 > nul