@set root=%~dp0%..\..\

@echo Loading environment settings...
@call %~dp0%load-settings.bat

@echo Installing docker services...
@call %~dp0%docker-compose.bat build --pull 

@echo Waking the environment up...
@call %~dp0%docker-compose.bat up -d

@echo Installing project dependencies...
@call %~dp0%composer.bat install --no-interaction 

@echo Shutting the environment down...
@call %~dp0%docker-compose.bat down