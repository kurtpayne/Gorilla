@echo off
REM Gorilla Windows Command Line Runner

REM if "%PHPBIN%" == "" set PHPBIN=php.exe
if not exist "%PHPBIN%" if "%PHP_PEAR_PHP_BIN%" neq "" goto USE_PEAR_PATH
GOTO RUN
:USE_PEAR_PATH
set PHPBIN=%PHP_PEAR_PHP_BIN%
:RUN
"%PHPBIN%" ".\gorilla" --win %*