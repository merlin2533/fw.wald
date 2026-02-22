<?php
/**
 * Authentifizierungs-Routen
 */

function login() {
    global $db;

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['username']) || !isset($input['password'])) {
        jsonResponse(['error' => 'Benutzername und Passwort erforderlich'], 400);
    }

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$input['username']]);
    $user = $stmt->fetch();

    if ($user && password_verify($input['password'], $user['password'])) {
        $_SESSION['authenticated'] = true;
        $_SESSION['username'] = $user['username'];
        jsonResponse(['success' => true, 'message' => 'Login erfolgreich']);
    } else {
        jsonResponse(['error' => 'Ungültige Anmeldedaten'], 401);
    }
}

function logout() {
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Logout erfolgreich']);
}

function authStatus() {
    jsonResponse(['authenticated' => isAuthenticated()]);
}
