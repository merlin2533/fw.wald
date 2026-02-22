#!/usr/bin/env php
<?php
/**
 * Feuerwehr Walddorfhäslach - Backend Setup
 *
 * Dieses Script richtet das Backend automatisch ein.
 */

echo "\n";
echo "╔═══════════════════════════════════════════════╗\n";
echo "║   FEUERWEHR WALDDORFHÄSLACH - SETUP           ║\n";
echo "╚═══════════════════════════════════════════════╝\n";
echo "\n";

// Schritt 1: Node.js prüfen
echo "[1/5] Prüfe Node.js Installation...\n";
exec('node --version 2>&1', $nodeOutput, $nodeReturnVar);

if ($nodeReturnVar !== 0) {
    echo "[FEHLER] Node.js ist nicht installiert!\n";
    echo "\n";
    echo "Installation auf Linux:\n";
    echo "  Ubuntu/Debian: sudo apt-get install nodejs npm\n";
    echo "  CentOS/RHEL:   sudo yum install nodejs npm\n";
    echo "  Arch:          sudo pacman -S nodejs npm\n";
    echo "\n";
    echo "Oder von https://nodejs.org herunterladen\n";
    exit(1);
}

echo "[OK] Node.js gefunden: " . trim($nodeOutput[0]) . "\n";
echo "\n";

// Schritt 2: npm prüfen
echo "[2/5] Prüfe npm Installation...\n";
exec('npm --version 2>&1', $npmOutput, $npmReturnVar);

if ($npmReturnVar !== 0) {
    echo "[FEHLER] npm ist nicht installiert!\n";
    exit(1);
}

echo "[OK] npm gefunden: " . trim($npmOutput[0]) . "\n";
echo "\n";

// Schritt 3: Verzeichnisse erstellen
echo "[3/5] Erstelle benötigte Verzeichnisse...\n";
$dirs = ['data', 'images/uploads'];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "[OK] Verzeichnis erstellt: $dir\n";
        } else {
            echo "[FEHLER] Konnte Verzeichnis nicht erstellen: $dir\n";
            exit(1);
        }
    } else {
        echo "[OK] Verzeichnis existiert bereits: $dir\n";
    }
}
echo "\n";

// Schritt 4: Abhängigkeiten installieren
echo "[4/5] Installiere Node.js Abhängigkeiten...\n";
echo "Dies kann einige Minuten dauern...\n";
passthru('npm install', $installReturnVar);

if ($installReturnVar !== 0) {
    echo "\n[FEHLER] Installation der Abhängigkeiten fehlgeschlagen!\n";
    exit(1);
}
echo "\n[OK] Abhängigkeiten erfolgreich installiert\n";
echo "\n";

// Schritt 5: Berechtigungen setzen
echo "[5/5] Setze Berechtigungen...\n";
if (file_exists('start.sh')) {
    chmod('start.sh', 0755);
    echo "[OK] start.sh ausführbar gemacht\n";
}
if (file_exists('setup.sh')) {
    chmod('setup.sh', 0755);
    echo "[OK] setup.sh ausführbar gemacht\n";
}
echo "\n";

// Abschluss
echo "╔═══════════════════════════════════════════════╗\n";
echo "║   SETUP ERFOLGREICH ABGESCHLOSSEN             ║\n";
echo "╚═══════════════════════════════════════════════╝\n";
echo "\n";
echo "Nächste Schritte:\n";
echo "\n";
echo "1. Server starten:\n";
echo "   php start.php\n";
echo "   oder: npm start\n";
echo "\n";
echo "2. Admin-Panel öffnen:\n";
echo "   http://localhost:3000/admin.html\n";
echo "\n";
echo "3. Login-Daten:\n";
echo "   Benutzername: admin\n";
echo "   Passwort: Feuerwehr112!\n";
echo "\n";

exit(0);
