<?php
/**
 * Einsätze-Routen
 */

function getEinsaetze() {
    global $db;

    $year = $_GET['year'] ?? null;
    $limit = $_GET['limit'] ?? null;

    if ($year) {
        $stmt = $db->prepare("SELECT * FROM einsaetze WHERE strftime('%Y', date) = ? ORDER BY date DESC");
        $stmt->execute([$year]);
    } elseif ($limit) {
        $stmt = $db->prepare("SELECT * FROM einsaetze ORDER BY date DESC LIMIT ?");
        $stmt->execute([intval($limit)]);
    } else {
        $stmt = $db->query("SELECT * FROM einsaetze ORDER BY date DESC");
    }

    $einsaetze = $stmt->fetchAll();
    jsonResponse($einsaetze);
}

function getEinsaetzeStats($year) {
    global $db;

    $stmt = $db->prepare("
        SELECT
            COUNT(*) as total,
            SUM(CASE WHEN category = 'Brandeinsatz' THEN 1 ELSE 0 END) as brand,
            SUM(CASE WHEN category = 'Technische Hilfeleistung' THEN 1 ELSE 0 END) as techhilfe,
            SUM(CASE WHEN category = 'Sonstiges' THEN 1 ELSE 0 END) as sonstiges
        FROM einsaetze
        WHERE strftime('%Y', date) = ?
    ");
    $stmt->execute([$year]);
    $stats = $stmt->fetch();

    jsonResponse($stats);
}

function createEinsatz() {
    global $db;
    requireAuth();

    try {
        $title = $_POST['title'] ?? '';
        $date = $_POST['date'] ?? date('Y-m-d H:i:s');
        $category = $_POST['category'] ?? 'Sonstiges';
        $description = $_POST['description'] ?? '';
        $location = $_POST['location'] ?? '';
        $vehicles = $_POST['vehicles'] ?? '';
        $personnel = $_POST['personnel'] ?? '';
        $image = handleFileUpload('image');

        $stmt = $db->prepare("
            INSERT INTO einsaetze (title, date, category, description, image, location, vehicles, personnel)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$title, $date, $category, $description, $image, $location, $vehicles, $personnel]);

        $id = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM einsaetze WHERE id = ?");
        $stmt->execute([$id]);
        $newEinsatz = $stmt->fetch();

        jsonResponse(['success' => true, 'data' => $newEinsatz]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateEinsatz($id) {
    global $db;
    requireAuth();

    try {
        $stmt = $db->prepare("SELECT * FROM einsaetze WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();

        if (!$existing) {
            jsonResponse(['error' => 'Einsatz nicht gefunden'], 404);
        }

        $title = $_POST['title'] ?? $existing['title'];
        $date = $_POST['date'] ?? $existing['date'];
        $category = $_POST['category'] ?? $existing['category'];
        $description = $_POST['description'] ?? $existing['description'];
        $location = $_POST['location'] ?? $existing['location'];
        $vehicles = $_POST['vehicles'] ?? $existing['vehicles'];
        $personnel = $_POST['personnel'] ?? $existing['personnel'];
        $image = handleFileUpload('image') ?? $existing['image'];

        // Altes Bild löschen wenn neues hochgeladen
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $existing['image']) {
            deleteFile($existing['image']);
        }

        $stmt = $db->prepare("
            UPDATE einsaetze
            SET title = ?, date = ?, category = ?, description = ?, image = ?,
                location = ?, vehicles = ?, personnel = ?, updated_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ");
        $stmt->execute([$title, $date, $category, $description, $image, $location, $vehicles, $personnel, $id]);

        $stmt = $db->prepare("SELECT * FROM einsaetze WHERE id = ?");
        $stmt->execute([$id]);
        $updated = $stmt->fetch();

        jsonResponse(['success' => true, 'data' => $updated]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteEinsatz($id) {
    global $db;
    requireAuth();

    $stmt = $db->prepare("SELECT * FROM einsaetze WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        jsonResponse(['error' => 'Einsatz nicht gefunden'], 404);
    }

    // Bild löschen
    if ($existing['image']) {
        deleteFile($existing['image']);
    }

    $stmt = $db->prepare("DELETE FROM einsaetze WHERE id = ?");
    $stmt->execute([$id]);

    jsonResponse(['success' => true, 'message' => 'Einsatz gelöscht']);
}
