<?php
require 'db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
  die("Zugriff verweigert.");
}

$id = $_POST['id'] ?? null;
if ($id) {
  $stmt = $pdo->prepare("DELETE FROM fahrzeuge WHERE id = ?");
  $stmt->execute([$id]);
}

header("Location: admin.php");
exit;
