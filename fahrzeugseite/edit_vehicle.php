<?php
require 'db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
  die("Zugriff verweigert.");
}

$id = $_GET['id'] ?? null;
if (!$id) die("Fahrzeug nicht gefunden.");

$stmt = $pdo->prepare("SELECT * FROM fahrzeuge WHERE id = ?");
$stmt->execute([$id]);
$fahrzeug = $stmt->fetch();

if (!$fahrzeug) die("Fahrzeug existiert nicht.");
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Fahrzeug bearbeiten</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h2>Fahrzeug bearbeiten: <?= htmlspecialchars($fahrzeug['name']) ?></h2>
<form action="update_vehicle.php" method="POST">
  <input type="hidden" name="id" value="<?= $fahrzeug['id'] ?>">

  <label>Fahrzeugname</label>
  <input type="text" name="name" value="<?= htmlspecialchars($fahrzeug['name']) ?>" required>

  <label>Bild-URL</label>
  <input type="text" name="bild_url" value="<?= htmlspecialchars($fahrzeug['bild_url']) ?>" required>

  <label>Preis für 7 Tage</label>
  <input type="text" name="preis7" value="<?= htmlspecialchars($fahrzeug['preis7']) ?>" required>

  <label>Preis für 30 Tage</label>
  <input type="text" name="preis30" value="<?= htmlspecialchars($fahrzeug['preis30']) ?>" required>

  <label>Verfügbarkeit</label>
  <select name="status">
    <option value="available" <?= $fahrzeug['status'] == 'available' ? 'selected' : '' ?>>Verfügbar</option>
    <option value="unavailable" <?= $fahrzeug['status'] == 'unavailable' ? 'selected' : '' ?>>Nicht verfügbar</option>
  </select>

  <label>Vermietet bis (Datum/Zeit)</label>
  <input type="text" name="vermietet_bis" value="<?= htmlspecialchars($fahrzeug['vermietet_bis']) ?>">

  <label>Name des Fahrzeughalters</label>
  <input type="text" name="halter_name" value="<?= htmlspecialchars($fahrzeug['halter_name']) ?>" required>

  <label>Telefonnummer des Fahrzeughalters</label>
  <input type="text" name="halter_telefon" value="<?= htmlspecialchars($fahrzeug['halter_telefon']) ?>" required>

  <button type="submit">Speichern</button>
</form>

</body>
</html>