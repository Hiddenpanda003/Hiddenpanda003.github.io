<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $benutzername = $_POST['benutzername'];
  $passwort = $_POST['passwort'];

  $stmt = $pdo->prepare("SELECT * FROM nutzer WHERE benutzername = ?");
  $stmt->execute([$benutzername]);
  $nutzer = $stmt->fetch();

  if ($nutzer && password_verify($passwort, $nutzer['passwort_hash'])) {
  session_start(); // ← Falls nicht schon im db.php
  $_SESSION['nutzer'] = $nutzer['benutzername'];
  $_SESSION['admin'] = $nutzer['admin'];
  $_SESSION['nutzer_id'] = $nutzer['id']; // ✅ HINZUGEFÜGT
  header("Location: index.php");
  exit;
} else {
    $_SESSION['login_error'] = "Benutzername oder Passwort ist falsch.";
    header("Location: index.php");
    exit;
  }
}
?>

<form method="POST">
  <h2>Login</h2>
  <input type="text" name="benutzername" placeholder="Benutzername" required><br>
  <input type="password" name="passwort" placeholder="Passwort" required><br>
  <button type="submit">Login</button>
</form>