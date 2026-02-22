#!/bin/bash

echo ""
echo "╔═══════════════════════════════════════════════╗"
echo "║   FEUERWEHR WALDDORFHÄSLACH - SETUP           ║"
echo "╚═══════════════════════════════════════════════╝"
echo ""

# Prüfen ob Node.js installiert ist
if ! command -v node &> /dev/null; then
    echo "[FEHLER] Node.js ist nicht installiert!"
    echo ""
    echo "Bitte Node.js von https://nodejs.org herunterladen und installieren."
    exit 1
fi

echo "[1/4] Node.js gefunden:"
node --version
echo ""

echo "[2/4] Installiere Abhängigkeiten..."
npm install
if [ $? -ne 0 ]; then
    echo "[FEHLER] Installation fehlgeschlagen!"
    exit 1
fi
echo "[OK] Abhängigkeiten installiert"
echo ""

echo "[3/4] Erstelle Verzeichnisse..."
mkdir -p data
mkdir -p images/uploads
echo "[OK] Verzeichnisse erstellt"
echo ""

echo "[4/4] Setup abgeschlossen!"
echo ""
echo "╔═══════════════════════════════════════════════╗"
echo "║   SETUP ERFOLGREICH ABGESCHLOSSEN             ║"
echo "╚═══════════════════════════════════════════════╝"
echo ""
echo "Jetzt kannst du den Server starten mit:"
echo ""
echo "   ./start.sh"
echo ""
echo "oder manuell:"
echo ""
echo "   npm start"
echo ""
