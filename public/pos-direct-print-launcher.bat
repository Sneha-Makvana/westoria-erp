@echo off
REM ============================================================
REM  POS Direct Print Launcher
REM  Opens Chrome/Edge with silent printing (no print popup).
REM  Set your thermal printer as Windows Default Printer first.
REM ============================================================

set "POS_URL=https://poojasaman-erp.fableadtech.in/add-sales?sale_type=sales"

set "CHROME="
if exist "%ProgramFiles%\Google\Chrome\Application\chrome.exe" set "CHROME=%ProgramFiles%\Google\Chrome\Application\chrome.exe"
if exist "%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe" set "CHROME=%ProgramFiles(x86)%\Google\Chrome\Application\chrome.exe"
if exist "%LocalAppData%\Google\Chrome\Application\chrome.exe" set "CHROME=%LocalAppData%\Google\Chrome\Application\chrome.exe"

set "EDGE="
if exist "%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles(x86)%\Microsoft\Edge\Application\msedge.exe"
if exist "%ProgramFiles%\Microsoft\Edge\Application\msedge.exe" set "EDGE=%ProgramFiles%\Microsoft\Edge\Application\msedge.exe"

if defined CHROME (
  start "" "%CHROME%" --kiosk-printing --disable-print-preview --new-window "%POS_URL%"
  exit /b 0
)

if defined EDGE (
  start "" "%EDGE%" --kiosk-printing --disable-print-preview --new-window "%POS_URL%"
  exit /b 0
)

echo Chrome or Edge not found. Install Google Chrome and try again.
pause
exit /b 1
