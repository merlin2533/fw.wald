<?php
/**
 * Datenbank-Verbindung und -Initialisierung
 */

define('DB_PATH', __DIR__ . '/../data/feuerwehr.db');
define('DATA_DIR', __DIR__ . '/../data');
define('UPLOAD_DIR', __DIR__ . '/../images/uploads');

// Verzeichnisse erstellen
if (!file_exists(DATA_DIR)) {
    mkdir(DATA_DIR, 0755, true);
}
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}

// Datenbank-Verbindung
try {
    $db = new PDO('sqlite:' . DB_PATH);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['error' => 'Datenbankverbindung fehlgeschlagen: ' . $e->getMessage()]));
}

// Datenbank initialisieren
function initDatabase() {
    global $db;

    // Users Tabelle
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Aktuelles Tabelle
    $db->exec("
        CREATE TABLE IF NOT EXISTS aktuelles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            content TEXT NOT NULL,
            image TEXT,
            date DATETIME DEFAULT CURRENT_TIMESTAMP,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Einsätze Tabelle
    $db->exec("
        CREATE TABLE IF NOT EXISTS einsaetze (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title TEXT NOT NULL,
            date DATETIME NOT NULL,
            category TEXT DEFAULT 'Sonstiges',
            description TEXT NOT NULL,
            image TEXT,
            location TEXT,
            vehicles TEXT,
            personnel TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Fahrzeuge Tabelle
    $db->exec("
        CREATE TABLE IF NOT EXISTS fahrzeuge (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            type TEXT NOT NULL,
            description TEXT NOT NULL,
            image TEXT,
            year INTEGER,
            crew_capacity INTEGER,
            water_capacity INTEGER,
            pump_capacity TEXT,
            special_equipment TEXT,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Standard-Admin-User erstellen
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();

    if ($result['count'] == 0) {
        $password = password_hash('Feuerwehr112!', PASSWORD_BCRYPT);
        $stmt = $db->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $stmt->execute(['admin', $password]);
    }
}

// Datenbank beim ersten Aufruf initialisieren
initDatabase();

// Helper-Funktionen
function isAuthenticated() {
    return isset($_SESSION['authenticated']) && $_SESSION['authenticated'] === true;
}

function requireAuth() {
    if (!isAuthenticated()) {
        http_response_code(401);
        echo json_encode(['error' => 'Nicht autorisiert']);
        exit();
    }
}

function jsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit();
}

function handleFileUpload($fieldName = 'image') {
    if (!isset($_FILES[$fieldName]) || $_FILES[$fieldName]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($_FILES[$fieldName]['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('Fehler beim Datei-Upload');
    }

    $file = $_FILES[$fieldName];

    // Validierung
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Nur Bilder sind erlaubt (JPEG, PNG, GIF, WebP)');
    }

    if ($file['size'] > 5 * 1024 * 1024) {
        throw new Exception('Datei zu groß (max. 5MB)');
    }

    // Eindeutigen Dateinamen generieren
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '-' . mt_rand(100000, 999999) . '.' . $extension;
    $uploadPath = UPLOAD_DIR . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        throw new Exception('Fehler beim Speichern der Datei');
    }

    return '/images/uploads/' . $filename;
}

function deleteFile($filePath) {
    if ($filePath && file_exists('.' . $filePath)) {
        unlink('.' . $filePath);
    }
}
