@echo off
echo.
echo ╔═══════════════════════════════════════════════╗
echo ║   FEUERWEHR WALDDORFHÄSLACH - SERVER          ║
echo ╚═══════════════════════════════════════════════╝
echo.

REM Prüfen ob node_modules existiert
if not exist "node_modules" (
    echo [WARNUNG] Abhängigkeiten nicht installiert!
    echo.
    echo Führe erst setup.bat aus:
    echo    setup.bat
    echo.
    pause
    exit /b 1
)

REM Verzeichnisse erstellen falls nicht vorhanden
if not exist "data" mkdir data
if not exist "images\uploads" mkdir images\uploads

echo [INFO] Starte Server...
echo.
echo Server läuft auf: http://localhost:3000
echo Admin-Panel: http://localhost:3000/admin.html
echo.
echo Login-Daten:
echo   Benutzername: admin
echo   Passwort: Feuerwehr112!
echo.
echo Drücke STRG+C zum Beenden
echo.
echo ═══════════════════════════════════════════════
echo.

npm start
