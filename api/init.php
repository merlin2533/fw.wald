<?php
/**
 * Initialisierungs-Script für Admin-Panel
 *
 * Erstellt Placeholder-Bilder und fügt Initialdaten ein
 */

header('Content-Type: text/plain; charset=utf-8');

$step = $_GET['step'] ?? 'all';

// ========== PLACEHOLDER-BILDER ==========
if ($step === 'placeholders' || $step === 'all') {
    echo "🎨 Erstelle Placeholder-Bilder...\n\n";

    // Verzeichnisse erstellen
    $dirs = [
        '../images/news',
        '../images/fahrzeuge',
        '../images/uploads'
    ];

    foreach ($dirs as $dir) {
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
            echo "✓ Verzeichnis erstellt: $dir\n";
        }
    }

    // Funktion zum Erstellen von Placeholder-Bildern
    function createPlaceholder($filepath, $title) {
        if (file_exists($filepath)) {
            echo "→ Existiert bereits: $filepath\n";
            return;
        }

        $width = 800;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        // Farbverlauf (Rot der Feuerwehr)
        for ($i = 0; $i < $height; $i++) {
            $ratio = $i / $height;
            $r = 200 - (40 * $ratio);
            $g = 16 - (3 * $ratio);
            $b = 46 - (9 * $ratio);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $i, $width, $i, $color);
        }

        $white = imagecolorallocate($image, 255, 255, 255);

        // Text hinzufügen (mit eingebauter Font, da TrueType möglicherweise nicht verfügbar)
        $centerY = $height / 2;
        imagestring($image, 5, 50, $centerY - 50, 'Feuerwehr Walddorfhaeslach', $white);
        imagestring($image, 4, 50, $centerY - 10, $title, $white);
        imagestring($image, 3, 50, $centerY + 30, 'Bild folgt in Kuerze', $white);

        imagejpeg($image, $filepath, 85);
        imagedestroy($image);
        echo "✓ Placeholder erstellt: $filepath\n";
    }

    // Placeholder-Bilder erstellen
    $placeholders = [
        ['path' => '../images/news/fasching.jpg', 'title' => 'Feuerwehrfasching'],
        ['path' => '../images/news/zugfuehrer.jpg', 'title' => 'Neuer Zugfuehrer'],
        ['path' => '../images/news/sandsaecke.jpg', 'title' => 'Sandsaecke'],
        ['path' => '../images/fahrzeuge/elw1.jpg', 'title' => 'ELW 1'],
        ['path' => '../images/fahrzeuge/gwt2.jpg', 'title' => 'GW-T 2'],
        ['path' => '../images/fahrzeuge/hlf.jpg', 'title' => 'HLF 16/12'],
        ['path' => '../images/fahrzeuge/lf20.jpg', 'title' => 'LF 20'],
        ['path' => '../images/fahrzeuge/mtw.jpg', 'title' => 'MTW'],
        ['path' => '../images/placeholder.jpg', 'title' => 'Platzhalter']
    ];

    foreach ($placeholders as $p) {
        createPlaceholder($p['path'], $p['title']);
    }

    echo "\n✅ Placeholder-Bilder erstellt!\n";

    if ($step === 'placeholders') {
        exit();
    }

    echo "\n" . str_repeat('=', 50) . "\n\n";
}

// ========== INITIALDATEN ==========
if ($step === 'seed' || $step === 'all') {
    echo "🌱 Füge Initialdaten ein...\n\n";

    require_once 'database.php';

    try {
        // Prüfen ob bereits Daten vorhanden
        $stmt = $db->query("SELECT COUNT(*) as count FROM aktuelles");
        $aktuellesCount = $stmt->fetch()['count'];

        $stmt = $db->query("SELECT COUNT(*) as count FROM einsaetze");
        $einsaetzeCount = $stmt->fetch()['count'];

        $stmt = $db->query("SELECT COUNT(*) as count FROM fahrzeuge");
        $fahrzeugeCount = $stmt->fetch()['count'];

        if ($aktuellesCount > 0 || $einsaetzeCount > 0 || $fahrzeugeCount > 0) {
            echo "ℹ️  Daten bereits vorhanden:\n";
            echo "   - Aktuelles: $aktuellesCount\n";
            echo "   - Einsätze: $einsaetzeCount\n";
            echo "   - Fahrzeuge: $fahrzeugeCount\n\n";
            echo "→ Überspringe Daten-Import (bereits initialisiert)\n";
            exit();
        }

        // Wenn leer, dann Initialdaten einfügen
        include 'seed-data.php';

    } catch (Exception $e) {
        echo "\n❌ Fehler: " . $e->getMessage() . "\n";
        http_response_code(500);
    }
}
