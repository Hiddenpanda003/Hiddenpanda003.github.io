<?php
require 'db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
  die("Zugriff verweigert.");
}

$data = $_POST;

$stmt = $pdo->prepare("UPDATE fahrzeuge SET
  name = ?, bild_url = ?, preis7 = ?, preis30 = ?, status = ?, vermietet_bis = ?, halter_name = ?, halter_telefon = ?
  WHERE id = ?");

$stmt->execute([
  $data['name'],
  $data['bild_url'],
  $data['preis7'],
  $data['preis30'],
  $data['status'],
  $data['vermietet_bis'],
  $data['halter_name'],
  $data['halter_telefon'],
  $data['id']
]);

header("Location: admin.php");
exit;