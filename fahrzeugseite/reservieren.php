<?php
ob_start(); // Alles puffern, um HTML-Ausgabe abzufangen
header('Content-Type: application/json');
session_start();
require 'db.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    // Eingabe lesen
    $input = json_decode(file_get_contents("php://input"), true);

    // Prüfe Login
    if (!isset($_SESSION['nutzer'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Nicht eingeloggt.']);
        exit;
    }

    // Nutzer-ID holen
    $stmt = $pdo->prepare("SELECT id FROM nutzer WHERE benutzername = ?");
    $stmt->execute([$_SESSION['nutzer']]);
    $nutzer = $stmt->fetch();

    if (!$nutzer) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Nutzer nicht gefunden.']);
        exit;
    }

    $fahrzeug_id = $input['fahrzeug_id'];
    $startdatum = $input['startdatum'];
    $dauer = (int)$input['dauer'];
    $nutzer_id = $nutzer['id'];

    // Fahrzeugdaten laden
    $stmt = $pdo->prepare("SELECT preis7, preis30, kaution FROM fahrzeuge WHERE id = ?");
    $stmt->execute([$fahrzeug_id]);
    $fahrzeug = $stmt->fetch();

    if (!$fahrzeug) {
        http_response_code(404);
        echo json_encode(['success' => false, 'error' => 'Fahrzeug nicht gefunden.']);
        exit;
    }

    $kosten = (float) ($dauer === 7 ? $fahrzeug['preis7'] : $fahrzeug['preis30']);
$kaution = (float) $fahrzeug['kaution'];

    // Reservierung eintragen
    $stmt = $pdo->prepare("INSERT INTO reservierungen (fahrzeug_id, nutzer_id, startdatum, dauer, status, erstellt_am) VALUES (?, ?, ?, ?, 'offen', NOW())");
    $stmt->execute([$fahrzeug_id, $nutzer_id, $startdatum, $dauer]);

    // Erfolgreiche Antwort
    echo json_encode([
        'success' => true,
        'message' => 'Reservierung ist eingegangen.',
        'kosten' => number_format($kosten, 2) . ' €',
        'dauer' => $dauer . ' Tage',
        'kaution' => number_format($kaution, 2) . ' €'
    ]);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    $errorOutput = ob_get_clean(); // Pufferausgabe sichern
    echo json_encode([
        'success' => false,
        'error' => 'Fehler: ' . $e->getMessage(),
        'output' => $errorOutput
    ]);
    exit;
}
