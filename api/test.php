<?php
/**
 * Test-Script zum Überprüfen der PHP-Umgebung
 */

header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 PHP Backend Test</h1>";

// PHP-Version
echo "<h2>✓ PHP-Version</h2>";
echo "<p>" . phpversion() . "</p>";

// Erforderliche Module
echo "<h2>PHP-Module</h2>";
$requiredModules = ['pdo_sqlite', 'session', 'json'];
foreach ($requiredModules as $module) {
    $loaded = extension_loaded($module);
    $icon = $loaded ? '✓' : '✗';
    $color = $loaded ? 'green' : 'red';
    echo "<p style='color: $color'>$icon $module</p>";
}

// Verzeichnisse
echo "<h2>Verzeichnisse</h2>";
$dirs = [
    '../data' => 'Data-Verzeichnis',
    '../images/uploads' => 'Upload-Verzeichnis'
];

foreach ($dirs as $dir => $name) {
    $exists = file_exists($dir);
    $writable = is_writable($dir);
    $icon = ($exists && $writable) ? '✓' : '✗';
    $color = ($exists && $writable) ? 'green' : 'red';

    $status = [];
    if (!$exists) $status[] = 'existiert nicht';
    if ($exists && !$writable) $status[] = 'nicht beschreibbar';
    if (empty($status)) $status[] = 'OK';

    echo "<p style='color: $color'>$icon $name: " . implode(', ', $status) . "</p>";
}

// Verzeichnisse erstellen falls nötig
if (!file_exists('../data')) {
    mkdir('../data', 0755, true);
    echo "<p style='color: orange'>→ Data-Verzeichnis erstellt</p>";
}
if (!file_exists('../images/uploads')) {
    mkdir('../images/uploads', 0755, true);
    echo "<p style='color: orange'>→ Upload-Verzeichnis erstellt</p>";
}

// Datenbank-Test
echo "<h2>Datenbank</h2>";
try {
    $dbPath = '../data/feuerwehr.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green'>✓ SQLite-Verbindung erfolgreich</p>";
    echo "<p>Datenbank: $dbPath</p>";

    // Tabellen prüfen
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);
    if (count($tables) > 0) {
        echo "<p>Tabellen: " . implode(', ', $tables) . "</p>";
    } else {
        echo "<p style='color: orange'>⚠ Keine Tabellen gefunden - werden beim ersten API-Aufruf erstellt</p>";
    }
} catch (PDOException $e) {
    echo "<p style='color: red'>✗ Datenbank-Fehler: " . $e->getMessage() . "</p>";
}

// .htaccess Test
echo "<h2>Apache-Konfiguration</h2>";
if (file_exists('../.htaccess')) {
    echo "<p style='color: green'>✓ .htaccess vorhanden</p>";

    // Prüfe ob mod_rewrite aktiv ist
    if (function_exists('apache_get_modules')) {
        $modules = apache_get_modules();
        $hasRewrite = in_array('mod_rewrite', $modules);
        $icon = $hasRewrite ? '✓' : '✗';
        $color = $hasRewrite ? 'green' : 'red';
        echo "<p style='color: $color'>$icon mod_rewrite</p>";
    } else {
        echo "<p style='color: orange'>⚠ Kann mod_rewrite-Status nicht prüfen (apache_get_modules nicht verfügbar)</p>";
    }
} else {
    echo "<p style='color: red'>✗ .htaccess nicht gefunden</p>";
}

// API-Test
echo "<h2>API-Test</h2>";
echo "<p><a href='/api/auth/status'>Test API-Endpunkt: /api/auth/status</a></p>";

// Upload-Test
echo "<h2>Upload-Einstellungen</h2>";
echo "<p>Max Upload: " . ini_get('upload_max_filesize') . "</p>";
echo "<p>Max POST: " . ini_get('post_max_size') . "</p>";

echo "<hr>";
echo "<p><strong>Wenn alles grün ist, sollte das Backend funktionieren!</strong></p>";
echo "<p><a href='../admin.html'>→ Zum Admin-Panel</a></p>";
