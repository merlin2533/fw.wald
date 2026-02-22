<?php
/**
 * Aktuelles-Routen
 */

function getAktuelles() {
    global $db;

    $stmt = $db->query("SELECT * FROM aktuelles ORDER BY date DESC LIMIT 3");
    $aktuelles = $stmt->fetchAll();

    // Stelle sicher, dass Bilder existieren (erstelle Placeholder wenn nötig)
    foreach ($aktuelles as &$item) {
        if ($item['image']) {
            ensureImageExists($item['image']);
        }
    }

    jsonResponse($aktuelles);
}

function createAktuelles() {
    global $db;
    requireAuth();

    // Prüfen ob bereits 3 Einträge vorhanden
    $stmt = $db->query("SELECT COUNT(*) as count FROM aktuelles");
    $count = $stmt->fetch();

    if ($count['count'] >= 3) {
        jsonResponse(['error' => 'Maximal 3 Aktuelles-Einträge erlaubt. Bitte löschen Sie zuerst einen bestehenden Eintrag.'], 400);
    }

    try {
        $title = $_POST['title'] ?? '';
        $content = $_POST['content'] ?? '';
        $image = handleFileUpload('image');
        $date = date('Y-m-d H:i:s');

        $stmt = $db->prepare("INSERT INTO aktuelles (title, content, image, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $content, $image, $date]);

        $id = $db->lastInsertId();
        $stmt = $db->prepare("SELECT * FROM aktuelles WHERE id = ?");
        $stmt->execute([$id]);
        $newEntry = $stmt->fetch();

        jsonResponse(['success' => true, 'data' => $newEntry]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function updateAktuelles($id) {
    global $db;
    requireAuth();

    try {
        $stmt = $db->prepare("SELECT * FROM aktuelles WHERE id = ?");
        $stmt->execute([$id]);
        $existing = $stmt->fetch();

        if (!$existing) {
            jsonResponse(['error' => 'Eintrag nicht gefunden'], 404);
        }

        $title = $_POST['title'] ?? $existing['title'];
        $content = $_POST['content'] ?? $existing['content'];
        $image = handleFileUpload('image') ?? $existing['image'];

        // Altes Bild löschen wenn neues hochgeladen
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK && $existing['image']) {
            deleteFile($existing['image']);
        }

        $stmt = $db->prepare("UPDATE aktuelles SET title = ?, content = ?, image = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        $stmt->execute([$title, $content, $image, $id]);

        $stmt = $db->prepare("SELECT * FROM aktuelles WHERE id = ?");
        $stmt->execute([$id]);
        $updated = $stmt->fetch();

        jsonResponse(['success' => true, 'data' => $updated]);
    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

function deleteAktuelles($id) {
    global $db;
    requireAuth();

    $stmt = $db->prepare("SELECT * FROM aktuelles WHERE id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();

    if (!$existing) {
        jsonResponse(['error' => 'Eintrag nicht gefunden'], 404);
    }

    // Bild löschen
    if ($existing['image']) {
        deleteFile($existing['image']);
    }

    $stmt = $db->prepare("DELETE FROM aktuelles WHERE id = ?");
    $stmt->execute([$id]);

    jsonResponse(['success' => true, 'message' => 'Eintrag gelöscht']);
}
