<?php
/**
 * Image Proxy - Erstellt automatisch Placeholder-Bilder wenn nicht vorhanden
 *
 * Usage: /api/image.php?path=/images/fahrzeuge/hlf.jpg
 */

$requestedPath = $_GET['path'] ?? '';

// Sicherheit: Nur Bilder in /images/ erlauben
if (!preg_match('#^/images/.*\.(jpg|jpeg|png|gif|webp)$#i', $requestedPath)) {
    http_response_code(400);
    die('Invalid image path');
}

$fullPath = __DIR__ . '/..' . $requestedPath;
$dir = dirname($fullPath);

// Prüfen ob Bild existiert
if (file_exists($fullPath)) {
    // Bild existiert - direkt ausliefern
    $mime = mime_content_type($fullPath);
    header('Content-Type: ' . $mime);
    header('Cache-Control: public, max-age=31536000');
    readfile($fullPath);
    exit();
}

// Bild existiert nicht - Placeholder erstellen
if (!file_exists($dir)) {
    mkdir($dir, 0755, true);
}

// Titel aus Dateinamen extrahieren
$filename = basename($requestedPath, '.jpg');
$filename = basename($filename, '.jpeg');
$filename = basename($filename, '.png');
$title = ucfirst(str_replace(['-', '_'], ' ', $filename));

// Placeholder-Bild erstellen
$width = 800;
$height = 600;
$image = imagecreatetruecolor($width, $height);

// Farbverlauf (Feuerwehr-Rot)
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

// Als JPEG speichern
imagejpeg($image, $fullPath, 85);

// Und ausliefern
header('Content-Type: image/jpeg');
header('Cache-Control: public, max-age=86400'); // 1 Tag Cache für Placeholders
imagejpeg($image, null, 85);

imagedestroy($image);
