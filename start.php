#!/usr/bin/env php
<?php
/**
 * Feuerwehr Walddorfhäslach - Server Starter
 *
 * Startet den Node.js Backend-Server
 */

echo "\n";
echo "╔═══════════════════════════════════════════════╗\n";
echo "║   FEUERWEHR WALDDORFHÄSLACH - SERVER          ║\n";
echo "╚═══════════════════════════════════════════════╝\n";
echo "\n";

// Prüfen ob node_modules existiert
if (!file_exists('node_modules')) {
    echo "[WARNUNG] Abhängigkeiten nicht installiert!\n";
    echo "\n";
    echo "Führe erst setup.php aus:\n";
    echo "  php setup.php\n";
    echo "\n";
    exit(1);
}

// Prüfen ob Node.js verfügbar ist
exec('node --version 2>&1', $output, $returnVar);
if ($returnVar !== 0) {
    echo "[FEHLER] Node.js ist nicht installiert!\n";
    exit(1);
}

// Verzeichnisse erstellen falls nicht vorhanden
$dirs = ['data', 'images/uploads'];
foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

echo "[INFO] Starte Server...\n";
echo "\n";
echo "Server läuft auf: http://localhost:3000\n";
echo "Admin-Panel: http://localhost:3000/admin.html\n";
echo "\n";
echo "Login-Daten:\n";
echo "  Benutzername: admin\n";
echo "  Passwort: Feuerwehr112!\n";
echo "\n";
echo "Drücke STRG+C zum Beenden\n";
echo "\n";
echo "═══════════════════════════════════════════════\n";
echo "\n";

// Server starten
passthru('npm start');
