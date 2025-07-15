<?php
header('Content-Type: application/json');
require 'db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Nicht autorisiert.']);
    exit;
}

$input = json_decode(file_get_contents("php://input"), true);
$reservierung_id = $input['id'] ?? null;

if (!$reservierung_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Ungültige Anfrage.']);
    exit;
}

// Löschung durchführen
try {
    $stmt = $pdo->prepare("DELETE FROM reservierungen WHERE id = ?");
    $stmt->execute([$reservierung_id]);

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
