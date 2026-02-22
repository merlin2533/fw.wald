#!/bin/bash

echo ""
echo "╔═══════════════════════════════════════════════╗"
echo "║   FEUERWEHR WALDDORFHÄSLACH - SERVER          ║"
echo "╚═══════════════════════════════════════════════╝"
echo ""

# Prüfen ob node_modules existiert
if [ ! -d "node_modules" ]; then
    echo "[WARNUNG] Abhängigkeiten nicht installiert!"
    echo ""
    echo "Führe erst setup.sh aus:"
    echo "   ./setup.sh"
    echo ""
    exit 1
fi

# Verzeichnisse erstellen falls nicht vorhanden
mkdir -p data
mkdir -p images/uploads

echo "[INFO] Starte Server..."
echo ""
echo "Server läuft auf: http://localhost:3000"
echo "Admin-Panel: http://localhost:3000/admin.html"
echo ""
echo "Login-Daten:"
echo "  Benutzername: admin"
echo "  Passwort: Feuerwehr112!"
echo ""
echo "Drücke STRG+C zum Beenden"
echo ""
echo "═══════════════════════════════════════════════"
echo ""

npm start
