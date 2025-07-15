<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO('mysql:host=localhost;dbname=fahrzeug_db;charset=utf8', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'DB-Verbindung fehlgeschlagen: ' . $e->getMessage()
    ]);
    exit;
}