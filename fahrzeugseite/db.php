<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO(
        'mysql:host=sql308.infinityfree.com;dbname=if0_39478841_fahrzeuge;charset=utf8',
        'if0_39478841', // Benutzername
        'FQIxfCF93l' // <== Passwort hier eintragen
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'DB-Verbindung fehlgeschlagen: ' . $e->getMessage()
    ]);
    exit;
}