<?php
require 'db.php';
if (!isset($_SESSION['nutzer_id'])) {
    header('Location: index.php');
    exit;
}

// Nutzerdaten laden
$stmt = $pdo->prepare("SELECT benutzername, telefon FROM nutzer WHERE id = ?");
$stmt->execute([$_SESSION['nutzer_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $telefon = trim($_POST['telefon']);
    $stmt = $pdo->prepare("UPDATE nutzer SET telefon = ? WHERE id = ?");
    $stmt->execute([$telefon, $_SESSION['nutzer_id']]);
    $success = true;
    $user['telefon'] = $telefon;
}

// Reservierungen des Nutzers laden
$stmt = $pdo->prepare("SELECT r.*, f.name AS fahrzeug_name FROM reservierungen r JOIN fahrzeuge f ON r.fahrzeug_id = f.id WHERE r.nutzer_id = ? ORDER BY r.erstellt_am DESC");
$stmt->execute([$_SESSION['nutzer_id']]);
$reservierungen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang=\"de\">
<head>
    <meta charset=\"UTF-8\">
    <title>Mein Profil – J+T Rental</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header class=\"navbar\">
  <div class=\"logo\">J+T Rental</div>
  <nav>
    <ul class=\"nav-links\">
      <li><a href=\"index.php\">Startseite</a></li>
      <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
        <li><a href=\"admin.php\">Adminbereich</a></li>
        <li><a href=\"aktive_reservierungen.php\">Reservierungen</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <div class=\"navbar-user\">
      Willkommen, <?= htmlspecialchars($_SESSION['nutzer']) ?> |
      <a href=\"profil.php\" class=\"active\">Profil</a> |
      <a href=\"logout.php\">Logout</a>
  </div>
</header>

<div class=\"profil-container\">
  <h2>Persönliche Daten</h2>
  <?php if ($success): ?>
    <p style=\"color:#7cff7c;\">Daten wurden gespeichert.</p>
  <?php endif; ?>
  <form method=\"POST\" class=\"profil-form\">
    <label>Benutzername</label>
    <input type=\"text\" value=\"<?= htmlspecialchars($user['benutzername']) ?>\" disabled>
    <label>Telefon</label>
    <input type=\"text\" name=\"telefon\" value=\"<?= htmlspecialchars($user['telefon']) ?>\" required>
    <button type=\"submit\">Speichern</button>
  </form>

  <h2>Meine Reservierungen</h2>
  <?php if (count($reservierungen) === 0): ?>
    <p>Keine Reservierungen vorhanden.</p>
  <?php else: ?>
    <table class=\"admin-table\">
      <thead>
        <tr>
          <th>Fahrzeug</th>
          <th>Mietbeginn</th>
          <th>Dauer</th>
          <th>Status</th>
          <th>Reserviert am</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($reservierungen as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['fahrzeug_name']) ?></td>
            <td><?= htmlspecialchars($r['startdatum']) ?></td>
            <td><?= $r['dauer'] ?> Tage</td>
            <td><?= htmlspecialchars($r['status']) ?></td>
            <td><?= htmlspecialchars($r['erstellt_am']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
