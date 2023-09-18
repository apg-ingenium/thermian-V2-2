@set docker=.docker
@set web=Thermian\Web
@set hotspots=src\Application\HotspotSystem\HotspotAI

@echo Removing duplicate .env files...
@if exist %docker%\.env rm %docker%\.env
@if exist %hotspots%\.env rm %hotspots%\.env

@echo Removing temporary files...
@if exist %web%\tmp rmdir /S /Q %web%\tmp

@echo Removing log files...
@if exist %web%\logs rmdir /S /Q %web%\logs

@echo Removing persisted files...
@if exist persistence rmdir /S /Q persistence

@echo Uninstalling dependencies...
@if exist vendor rmdir /S /Q vendor
@if exist %hotspots%\venv rmdir /S /Q %hotspots%\venv

@echo Uninstalling docker services...
@call %~dp0%docker-compose.bat down --rmi all --volumes