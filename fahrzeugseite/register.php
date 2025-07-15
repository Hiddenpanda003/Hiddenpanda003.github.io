<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $benutzername = $_POST['benutzername'];
  $telefon = $_POST['telefon'];
  $passwort = password_hash($_POST['passwort'], PASSWORD_DEFAULT);

  $stmt = $pdo->prepare("SELECT * FROM nutzer WHERE benutzername = ?");
  $stmt->execute([$benutzername]);
  if ($stmt->rowCount() > 0) {
    $_SESSION['register_error'] = "Benutzername ist bereits vergeben.";
    header("Location: index.php");
    exit;
  }

  $stmt = $pdo->prepare("INSERT INTO nutzer (benutzername, telefon, passwort_hash, admin) VALUES (?, ?, ?, 0)");
  $stmt->execute([$benutzername, $telefon, $passwort]);

  $_SESSION['register_success'] = "Registrierung erfolgreich! Jetzt einloggen.";
  header("Location: index.php");
  exit;
}

?>

<form method="POST">
  <h2>Registrieren</h2>
  <input type="text" name="benutzername" placeholder="Benutzername" required><br>
  <input type="text" name="telefon" placeholder="Telefonnummer" required><br>
  <input type="password" name="passwort" placeholder="Passwort" required><br>
  <button type="submit">Registrieren</button>
</form>