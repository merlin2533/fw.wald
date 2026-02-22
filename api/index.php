<?php
/**
 * Feuerwehr Walddorfhäslach - PHP Backend API
 *
 * Dieses Backend läuft auf Apache und nutzt die SQLite-Datenbank
 */

// Fehlerbehandlung
error_reporting(E_ALL);
ini_set('display_errors', 0);

// CORS Headers für API
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Session starten
session_start();

// Datenbank-Verbindung
require_once __DIR__ . '/database.php';

// Router
$requestUri = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Request-URI bereinigen (Query-String entfernen)
$path = parse_url($requestUri, PHP_URL_PATH);

// API-Prefix entfernen
$path = preg_replace('#^/api#', '', $path);

// Router-Logik
try {
    // Auth-Endpunkte
    if ($path === '/login' && $requestMethod === 'POST') {
        require_once __DIR__ . '/routes/auth.php';
        login();
        exit();
    }

    if ($path === '/logout' && $requestMethod === 'POST') {
        require_once __DIR__ . '/routes/auth.php';
        logout();
        exit();
    }

    if ($path === '/auth/status' && $requestMethod === 'GET') {
        require_once __DIR__ . '/routes/auth.php';
        authStatus();
        exit();
    }

    // Aktuelles-Endpunkte
    if ($path === '/aktuelles' && $requestMethod === 'GET') {
        require_once __DIR__ . '/routes/aktuelles.php';
        getAktuelles();
        exit();
    }

    if ($path === '/aktuelles' && $requestMethod === 'POST') {
        require_once __DIR__ . '/routes/aktuelles.php';
        createAktuelles();
        exit();
    }

    if (preg_match('#^/aktuelles/(\d+)$#', $path, $matches) && $requestMethod === 'PUT') {
        require_once __DIR__ . '/routes/aktuelles.php';
        updateAktuelles($matches[1]);
        exit();
    }

    if (preg_match('#^/aktuelles/(\d+)$#', $path, $matches) && $requestMethod === 'DELETE') {
        require_once __DIR__ . '/routes/aktuelles.php';
        deleteAktuelles($matches[1]);
        exit();
    }

    // Einsätze-Endpunkte
    if ($path === '/einsaetze' && $requestMethod === 'GET') {
        require_once __DIR__ . '/routes/einsaetze.php';
        getEinsaetze();
        exit();
    }

    if (preg_match('#^/einsaetze/stats/(\d+)$#', $path, $matches) && $requestMethod === 'GET') {
        require_once __DIR__ . '/routes/einsaetze.php';
        getEinsaetzeStats($matches[1]);
        exit();
    }

    if ($path === '/einsaetze' && $requestMethod === 'POST') {
        require_once __DIR__ . '/routes/einsaetze.php';
        createEinsatz();
        exit();
    }

    if (preg_match('#^/einsaetze/(\d+)$#', $path, $matches) && $requestMethod === 'PUT') {
        require_once __DIR__ . '/routes/einsaetze.php';
        updateEinsatz($matches[1]);
        exit();
    }

    if (preg_match('#^/einsaetze/(\d+)$#', $path, $matches) && $requestMethod === 'DELETE') {
        require_once __DIR__ . '/routes/einsaetze.php';
        deleteEinsatz($matches[1]);
        exit();
    }

    // Fahrzeuge-Endpunkte
    if ($path === '/fahrzeuge' && $requestMethod === 'GET') {
        require_once __DIR__ . '/routes/fahrzeuge.php';
        getFahrzeuge();
        exit();
    }

    if ($path === '/fahrzeuge' && $requestMethod === 'POST') {
        require_once __DIR__ . '/routes/fahrzeuge.php';
        createFahrzeug();
        exit();
    }

    if (preg_match('#^/fahrzeuge/(\d+)$#', $path, $matches) && $requestMethod === 'PUT') {
        require_once __DIR__ . '/routes/fahrzeuge.php';
        updateFahrzeug($matches[1]);
        exit();
    }

    if (preg_match('#^/fahrzeuge/(\d+)$#', $path, $matches) && $requestMethod === 'DELETE') {
        require_once __DIR__ . '/routes/fahrzeuge.php';
        deleteFahrzeug($matches[1]);
        exit();
    }

    // 404 - Route nicht gefunden
    http_response_code(404);
    echo json_encode(['error' => 'Endpunkt nicht gefunden']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Serverfehler: ' . $e->getMessage()]);
}
