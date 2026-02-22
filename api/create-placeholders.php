<?php
/**
 * Erstellt Placeholder-Bilder für fehlende Bilder
 */

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
    $width = 800;
    $height = 600;

    $image = imagecreatetruecolor($width, $height);

    // Farbverlauf (Rot der Feuerwehr)
    $startColor = imagecolorallocate($image, 200, 16, 46);   // #C8102E
    $endColor = imagecolorallocate($image, 160, 13, 37);     // #A00D25
    $white = imagecolorallocate($image, 255, 255, 255);
    $whiteTransparent = imagecolorallocate($image, 255, 255, 255);
    imagecolortransparent($image, $whiteTransparent);

    // Gradient von oben nach unten
    for ($i = 0; $i < $height; $i++) {
        $r = $startColor[0] + ($endColor[0] - $startColor[0]) * ($i / $height);
        $g = $startColor[1] + ($endColor[1] - $startColor[1]) * ($i / $height);
        $b = $startColor[2] + ($endColor[2] - $startColor[2]) * ($i / $height);
        $color = imagecolorallocate($image, $r, $g, $b);
        imageline($image, 0, $i, $width, $i, $color);
    }

    // Verwende eine einfachere Schriftart, die auf dem Server verfügbar ist
    $fontFile = null;
    $possibleFonts = [
        '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
        '/usr/share/fonts/truetype/liberation/LiberationSans-Bold.ttf',
        '/usr/share/fonts/TTF/DejaVuSans-Bold.ttf',
        __DIR__ . '/../fonts/arial.ttf'
    ];

    foreach ($possibleFonts as $font) {
        if (file_exists($font)) {
            $fontFile = $font;
            break;
        }
    }

    // Text hinzufügen
    if ($fontFile) {
        // Mit TrueType-Font
        imagettftext($image, 36, 0, 50, $height/2 - 40, $white, $fontFile, 'Feuerwehr');
        imagettftext($image, 36, 0, 50, $height/2 + 20, $white, $fontFile, 'Walddorfhäslach');
        imagettftext($image, 20, 0, 50, $height/2 + 80, $white, $fontFile, $title);
    } else {
        // Fallback: Nur mit eingebauter Font
        imagestring($image, 5, 50, $height/2 - 40, 'Feuerwehr Walddorfhaeslach', $white);
        imagestring($image, 4, 50, $height/2 + 20, $title, $white);
        imagestring($image, 3, 50, $height/2 + 60, 'Bild folgt in Kuerze', $white);
    }

    // Bild speichern
    imagejpeg($image, $filepath, 85);
    imagedestroy($image);

    echo "✓ Placeholder erstellt: $filepath\n";
}

// Placeholder-Bilder erstellen
echo "🎨 Erstelle Placeholder-Bilder...\n\n";

$placeholders = [
    // News
    ['path' => '../images/news/fasching.jpg', 'title' => 'Feuerwehrfasching'],
    ['path' => '../images/news/zugfuehrer.jpg', 'title' => 'Neuer Zugfuehrer'],
    ['path' => '../images/news/sandsaecke.jpg', 'title' => 'Sandsaecke'],

    // Fahrzeuge
    ['path' => '../images/fahrzeuge/elw1.jpg', 'title' => 'ELW 1'],
    ['path' => '../images/fahrzeuge/gwt2.jpg', 'title' => 'GW-T 2'],
    ['path' => '../images/fahrzeuge/hlf.jpg', 'title' => 'HLF 16/12'],
    ['path' => '../images/fahrzeuge/lf20.jpg', 'title' => 'LF 20'],
    ['path' => '../images/fahrzeuge/mtw.jpg', 'title' => 'MTW'],

    // Standard
    ['path' => '../images/placeholder.jpg', 'title' => 'Platzhalter']
];

foreach ($placeholders as $p) {
    if (!file_exists($p['path'])) {
        createPlaceholder($p['path'], $p['title']);
    } else {
        echo "→ Existiert bereits: {$p['path']}\n";
    }
}

echo "\n✅ Alle Placeholder-Bilder wurden erstellt!\n";
echo "\n🔄 Führe nun das Seed-Script aus:\n";
echo "   /api/seed-data.php\n\n";
