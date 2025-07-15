<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("INSERT INTO fahrzeuge 
  (name, bild_url, preis7, preis30, status, vermietet_bis, halter_name, halter_telefon, kategorie, kaution)
  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

$stmt->execute([
  $data['name'],
  $data['image'],
  $data['price7'],
  $data['price30'],
  $data['status'],
  $data['untilDate'],
  $data['halterName'],
  $data['halterTelefon'],
  $data['kategorie'],
  $data['kaution']
]);