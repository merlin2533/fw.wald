<?php
/**
 * Fahrzeuge-Routen
 */

function getFahrzeuge() {
    global $db;

    $stmt = $db->query("SELECT * FROM fahrzeuge ORDER BY sort_order ASC, name ASC");
    $fahrzeuge = $stmt->fetchAll();

    // Stelle sicher, dass Bilder existieren (erstelle Placeholder wenn nötig)
    foreach ($fahrzeuge as &$item) {
        if ($item['image']) {
            ensureImageExists($item['image']);
        }
    }

    jsonResponse($fahrzeuge);
}

function createFahrzeug() {
    global $db;
    requireAuth();

    try {
        $name = $_POST['name'] ?? '';
        $type = $_POST['type'] ?? '';
        $description = $_POST['description'] ?? '';
        $year = $_POST['year'] ?? null;
        $crew_capacity = $_POST['crew_capacity'] ?? null;
        $water_capacity = $_POST['water_capacity'] ?? null;
        $pump_capacity = $_POST['pump_capacity'] ?? '';
        $special_equipment = $_POST['special_equipment'] ?? '';
        $sort_order = $_POST['sort_order'] ?? 0;
        $image = handleFileUpload('image');

        $stmt = $db->prepare("
            INSERT INTO fahrzeuge (name, type, description, image, year, crew_capacity, water_capacity, pump_capacity, special_equipment, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$name, $type, $description, $image, $year, $crew_capacity, $water_capacity, $pump_capacity, $special_equipment, $sort_order]);

        $id = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
        $stmt->execute([$id]);
        $newFahrzeug = $stmt->fetch();

        jsonResponse(['success' => true, 'data' => $newFahrzeug]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateFahrzeug($id) {
    global $db;
    requireAuth();

    try {
        $stmt = $db->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();

        if (!$existing) {
            jsonResponse(['error' => 'Fahrzeug nicht gefunden'], 404);
        }

        $name = $_POST['name'] ?? $existing['name'];
        $type = $_POST['type'] ?? $existing['type'];
        $description = $_POST['description'] ?? $existing['description'];
        $year = $_POST['year'] ?? $existing['year'];
        $crew_capacity = $_POST['crew_capacity'] ?? $existing['crew_capacity'];
        $water_capacity = $_POST['water_capacity'] ?? $existing['water_capacity'];
        $pump_capacity = $_POST['pump_capacity'] ?? $existing['pump_capacity'];
        $special_equipment = $_POST['special_equipment'] ?? $existing['special_equipment'];
        $sort_order = $_POST['sort_order'] ?? $existing['sort_order'];
        $image = handleFileUpload('image') ?? $existing['image'];

        // Altes Bild löschen wenn neues hochgeladen
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $existing['image']) {
            deleteFile($existing['image']);
        }

        $stmt = $db->prepare("
            UPDATE fahrzeuge
            SET name = ?, type = ?, description = ?, image = ?, year = ?, crew_capacity = ?,
                water_capacity = ?, pump_capacity = ?, special_equipment = ?, sort_order = ?,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$name, $type, $description, $image, $year, $crew_capacity, $water_capacity, $pump_capacity, $special_equipment, $sort_order, $id]);

        $stmt = $db->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
        $stmt->execute([$id]);
        $updated = $stmt->fetch();

        jsonResponse(['success' => true, 'data' => $updated]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteFahrzeug($id) {
    global $db;
    requireAuth();

    $stmt = $db->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        jsonResponse(['error' => 'Fahrzeug nicht gefunden'], 404);
    }

    // Bild löschen
    if ($existing['image']) {
        deleteFile($existing['image']);
    }

    $stmt = $db->prepare("DELETE FROM fahrzeuge WHERE id = ?");
    $stmt->execute([$id]);

    jsonResponse(['success' => true, 'message' => 'Fahrzeug gelöscht']);
}
