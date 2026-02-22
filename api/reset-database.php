<?php
/**
 * Datenbank zurücksetzen und neu initialisieren
 *
 * ACHTUNG: Dieses Script löscht alle vorhandenen Daten!
 *
 * Aufruf: php api/reset-database.php ODER über Browser: /api/reset-database.php
 */

header('Content-Type: text/plain; charset=utf-8');

echo "🔄 Datenbank zurücksetzen\n";
echo str_repeat('=', 50) . "\n\n";

// Sicherheitsabfrage (nur bei Kommandozeilen-Aufruf)
if (php_sapi_name() === 'cli') {
    echo "⚠️  WARNUNG: Alle Daten in der Datenbank werden gelöscht!\n";
    echo "Möchten Sie fortfahren? (yes/no): ";
    $handle = fopen("php://stdin", "r");
    $line = trim(fgets($handle));
    fclose($handle);

    if (strtolower($line) !== 'yes') {
        echo "\n❌ Abgebrochen.\n";
        exit();
    }
    echo "\n";
}

define('DB_PATH', __DIR__ . '/../data/feuerwehr.db');

// Alte Datenbank löschen
if (file_exists(DB_PATH)) {
    unlink(DB_PATH);
    echo "✓ Alte Datenbank gelöscht\n\n";
}

// Neue Datenbank initialisieren
echo "📦 Erstelle neue Datenbank...\n\n";
require_once 'database.php';

echo "✓ Datenbank-Schema erstellt\n";
echo "✓ Standard-Admin-User erstellt (admin / Feuerwehr112!)\n\n";

echo str_repeat('=', 50) . "\n\n";

// Placeholder-Bilder erstellen
echo "🎨 Erstelle Placeholder-Bilder...\n\n";

// Verzeichnisse erstellen
$dirs = [
    __DIR__ . '/../images/news',
    __DIR__ . '/../images/fahrzeuge',
    __DIR__ . '/../images/uploads'
];

foreach ($dirs as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
        echo "✓ Verzeichnis erstellt: " . basename(dirname($dir)) . "/" . basename($dir) . "\n";
    }
}

// Funktion zum Erstellen von Placeholder-Bildern
function createPlaceholder($filepath, $title) {
    if (file_exists($filepath)) {
        echo "→ Existiert bereits: " . basename($filepath) . "\n";
        return;
    }

    $width = 800;
    $height = 600;
    $image = @imagecreatetruecolor($width, $height);

    if (!$image) {
        echo "⚠️  GD Library nicht verfügbar - überspringe Bilder\n";
        return;
    }

    // Farbverlauf (Rot der Feuerwehr)
    for ($i = 0; $i < $height; $i++) {
        $ratio = $i / $height;
        $r = (int)(200 - (40 * $ratio));
        $g = (int)(16 - (3 * $ratio));
        $b = (int)(46 - (9 * $ratio));
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $i, $width, $i, $color);
    }

    $white = imagecolorallocate($image, 255, 255, 255);

    // Text hinzufügen
    $centerY = (int)($height / 2);
    imagestring($image, 5, 50, $centerY - 50, 'Feuerwehr Walddorfhaeslach', $white);
    imagestring($image, 4, 50, $centerY - 10, $title, $white);
    imagestring($image, 3, 50, $centerY + 30, 'Bild folgt in Kuerze', $white);

    imagejpeg($image, $filepath, 85);
    imagedestroy($image);
    echo "✓ Placeholder erstellt: " . basename($filepath) . "\n";
}

// Placeholder-Bilder erstellen
$placeholders = [
    ['path' => __DIR__ . '/../images/news/fasching.jpg', 'title' => 'Feuerwehrfasching'],
    ['path' => __DIR__ . '/../images/news/zugfuehrer.jpg', 'title' => 'Neuer Zugfuehrer'],
    ['path' => __DIR__ . '/../images/news/sandsaecke.jpg', 'title' => 'Sandsaecke'],
    ['path' => __DIR__ . '/../images/fahrzeuge/elw1.jpg', 'title' => 'ELW 1'],
    ['path' => __DIR__ . '/../images/fahrzeuge/gwt2.jpg', 'title' => 'GW-T 2'],
    ['path' => __DIR__ . '/../images/fahrzeuge/hlf.jpg', 'title' => 'HLF 16/12'],
    ['path' => __DIR__ . '/../images/fahrzeuge/lf20.jpg', 'title' => 'LF 20'],
    ['path' => __DIR__ . '/../images/fahrzeuge/mtw.jpg', 'title' => 'MTW']
];

foreach ($placeholders as $p) {
    createPlaceholder($p['path'], $p['title']);
}

echo "\n✅ Placeholder-Bilder erstellt!\n\n";
echo str_repeat('=', 50) . "\n\n";

// Initialdaten einfügen
echo "🌱 Füge Initialdaten ein...\n\n";

try {
    include 'seed-data.php';
} catch (Exception $e) {
    echo "\n❌ Fehler beim Daten-Import:\n";
    echo $e->getMessage() . "\n";
    exit(1);
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "✅ Datenbank erfolgreich zurückgesetzt!\n";
echo str_repeat('=', 50) . "\n";
