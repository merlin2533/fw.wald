@echo off
echo.
echo ╔═══════════════════════════════════════════════╗
echo ║   FEUERWEHR WALDDORFHÄSLACH - SETUP           ║
echo ╚═══════════════════════════════════════════════╝
echo.

REM Prüfen ob Node.js installiert ist
where node >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [FEHLER] Node.js ist nicht installiert!
    echo.
    echo Bitte Node.js von https://nodejs.org herunterladen und installieren.
    pause
    exit /b 1
)

echo [1/4] Node.js gefunden:
node --version
echo.

echo [2/4] Installiere Abhängigkeiten...
call npm install
if %ERRORLEVEL% NEQ 0 (
    echo [FEHLER] Installation fehlgeschlagen!
    pause
    exit /b 1
)
echo [OK] Abhängigkeiten installiert
echo.

echo [3/4] Erstelle Verzeichnisse...
if not exist "data" mkdir data
if not exist "images\uploads" mkdir images\uploads
echo [OK] Verzeichnisse erstellt
echo.

echo [4/4] Setup abgeschlossen!
echo.
echo ╔═══════════════════════════════════════════════╗
echo ║   SETUP ERFOLGREICH ABGESCHLOSSEN             ║
echo ╚═══════════════════════════════════════════════╝
echo.
echo Jetzt kannst du den Server starten mit:
echo.
echo    start.bat
echo.
echo oder manuell:
echo.
echo    npm start
echo.
pause
