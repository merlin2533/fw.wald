<?php
/**
 * Media Management API
 * Handles listing, uploading, and deleting media files
 */

session_start();

// Helper functions
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
    header('Content-Type: application/json');
    echo json_encode($data);
    exit();
}

// Get action
$action = $_GET['action'] ?? '';

// List files in a directory
if ($action === 'list') {
    $directory = $_GET['directory'] ?? 'images';

    // Validate directory (security: only allow specific directories)
    $allowedDirs = ['images', 'images/news', 'images/fahrzeuge', 'images/uploads'];
    if (!in_array($directory, $allowedDirs)) {
        jsonResponse(['error' => 'Ungültiges Verzeichnis'], 400);
    }

    $fullPath = __DIR__ . '/../' . $directory;

    if (!is_dir($fullPath)) {
        jsonResponse(['files' => []]);
    }

    $files = [];
    $items = scandir($fullPath);

    foreach ($items as $item) {
        if ($item === '.' || $item === '..') continue;

        $itemPath = $fullPath . '/' . $item;

        if (is_file($itemPath)) {
            // Only include image files
            $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                $files[] = [
                    'name' => $item,
                    'path' => $directory . '/' . $item,
                    'size' => filesize($itemPath),
                    'modified' => filemtime($itemPath)
                ];
            }
        }
    }

    // Sort by modified time (newest first)
    usort($files, function($a, $b) {
        return $b['modified'] - $a['modified'];
    });

    jsonResponse(['files' => $files]);
}

// Upload files
if ($action === 'upload') {
    requireAuth();

    try {
        $directory = $_POST['directory'] ?? 'images';

        // Validate directory
        $allowedDirs = ['images', 'images/news', 'images/fahrzeuge', 'images/uploads'];
        if (!in_array($directory, $allowedDirs)) {
            jsonResponse(['error' => 'Ungültiges Verzeichnis'], 400);
        }

        $uploadDir = __DIR__ . '/../' . $directory;

        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!isset($_FILES['images'])) {
            jsonResponse(['error' => 'Keine Dateien hochgeladen'], 400);
        }

        $files = $_FILES['images'];
        $uploadedCount = 0;
        $errors = [];

        // Handle multiple files
        $fileCount = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < $fileCount; $i++) {
            $fileName = is_array($files['name']) ? $files['name'][$i] : $files['name'];
            $fileTmpName = is_array($files['tmp_name']) ? $files['tmp_name'][$i] : $files['tmp_name'];
            $fileError = is_array($files['error']) ? $files['error'][$i] : $files['error'];
            $fileSize = is_array($files['size']) ? $files['size'][$i] : $files['size'];
            $fileType = is_array($files['type']) ? $files['type'][$i] : $files['type'];

            // Check for upload errors
            if ($fileError !== UPLOAD_ERR_OK) {
                $errors[] = "$fileName: Upload-Fehler";
                continue;
            }

            // Validate file type
            $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes)) {
                $errors[] = "$fileName: Nur Bilder erlaubt (JPEG, PNG, GIF, WebP)";
                continue;
            }

            // Validate file size (max 5MB)
            if ($fileSize > 5 * 1024 * 1024) {
                $errors[] = "$fileName: Datei zu groß (max. 5MB)";
                continue;
            }

            // Generate safe filename
            $extension = pathinfo($fileName, PATHINFO_EXTENSION);
            $baseName = pathinfo($fileName, PATHINFO_FILENAME);
            $baseName = preg_replace('/[^a-zA-Z0-9-_]/', '-', $baseName);
            $safeName = $baseName . '.' . $extension;

            // Check if file exists and append number if needed
            $targetPath = $uploadDir . '/' . $safeName;
            $counter = 1;
            while (file_exists($targetPath)) {
                $safeName = $baseName . '-' . $counter . '.' . $extension;
                $targetPath = $uploadDir . '/' . $safeName;
                $counter++;
            }

            // Move uploaded file
            if (move_uploaded_file($fileTmpName, $targetPath)) {
                $uploadedCount++;
            } else {
                $errors[] = "$fileName: Fehler beim Speichern";
            }
        }

        if ($uploadedCount > 0) {
            $message = $uploadedCount . ' Bild(er) erfolgreich hochgeladen';
            if (count($errors) > 0) {
                $message .= ', aber ' . count($errors) . ' Fehler: ' . implode(', ', $errors);
            }
            jsonResponse(['success' => true, 'uploaded' => $uploadedCount, 'message' => $message]);
        } else {
            jsonResponse(['error' => 'Keine Dateien hochgeladen. Fehler: ' . implode(', ', $errors)], 400);
        }

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

// Delete file
if ($action === 'delete') {
    requireAuth();

    try {
        $input = json_decode(file_get_contents('php://input'), true);
        $path = $input['path'] ?? '';

        if (empty($path)) {
            jsonResponse(['error' => 'Kein Pfad angegeben'], 400);
        }

        // Security: Prevent directory traversal
        if (strpos($path, '..') !== false) {
            jsonResponse(['error' => 'Ungültiger Pfad'], 400);
        }

        // Only allow deletion in specific directories
        $allowedPrefixes = ['images/', 'images/news/', 'images/fahrzeuge/', 'images/uploads/'];
        $isAllowed = false;
        foreach ($allowedPrefixes as $prefix) {
            if (strpos($path, $prefix) === 0) {
                $isAllowed = true;
                break;
            }
        }

        if (!$isAllowed) {
            jsonResponse(['error' => 'Löschen in diesem Verzeichnis nicht erlaubt'], 403);
        }

        $fullPath = __DIR__ . '/../' . $path;

        if (!file_exists($fullPath)) {
            jsonResponse(['error' => 'Datei nicht gefunden'], 404);
        }

        if (!is_file($fullPath)) {
            jsonResponse(['error' => 'Keine Datei'], 400);
        }

        if (unlink($fullPath)) {
            jsonResponse(['success' => true, 'message' => 'Datei gelöscht']);
        } else {
            jsonResponse(['error' => 'Fehler beim Löschen'], 500);
        }

    } catch (Exception $e) {
        jsonResponse(['error' => $e->getMessage()], 500);
    }
}

// Invalid action
jsonResponse(['error' => 'Ungültige Aktion'], 400);
