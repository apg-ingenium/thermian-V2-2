@set root=%~dp0%..\..\
@set hotspot_ai=%root%Thermian\Application\HotspotSystem\HotspotAI\
@set docker=%root%.docker\

@if not exist %root%.envi copy %root%.env.example %root%.env > NUL
@if not exist %hotspot_ai%.envi copy %root%.env %hotspot_ai%.env > NUL
@if not exist %docker%.envi copy %root%.env %docker%.env > NUL
