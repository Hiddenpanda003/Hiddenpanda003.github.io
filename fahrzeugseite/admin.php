<?php
require 'db.php';
$now = date('Y-m-d H:i:s');
$pdo->prepare("UPDATE fahrzeuge SET status = 'available', vermietet_bis = NULL WHERE status = 'unavailable' AND vermietet_bis IS NOT NULL AND STR_TO_DATE(vermietet_bis, '%d.%m.%Y %H:%i') <= ?")
    ->execute([$now]);
if (!isset($_SESSION['nutzer']) || $_SESSION['admin'] != 1) {
  die("<h2 style='color: red; text-align:center;'>Zugriff verweigert – nur Admins dürfen diese Seite sehen.</h2>");
}

$fahrzeuge = $pdo->query("SELECT * FROM fahrzeuge ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <title>Adminbereich – Alle Fahrzeuge</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
  <div class="logo">J+T Rental</div>
  <nav>
    <ul class="nav-links">
      <li><a href="index.php">Startseite</a></li>
      <li><a href="admin.php" class="active">Adminbereich</a></li>
      <li><a href="aktive_reservierungen.php">Reservierungen</a></li>
    </ul>
  </nav>
  <div class="navbar-user">
    <?php if (isset($_SESSION['nutzer'])): ?>
      Willkommen, <?= htmlspecialchars($_SESSION['nutzer']) ?> |
      <a href="profil.php">Profil</a> |
      <a href="logout.php">Logout</a>
    <?php endif; ?>
  </div>
</header>

<section class="admin-panel">
  <h2>Alle Fahrzeuge (inkl. Halterdaten)</h2>
  <table class="admin-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Bild</th>
        <th>7 Tage</th>
        <th>30 Tage</th>
        <th>Status</th>
        <th>Bis</th>
        <th>Halter</th>
        <th>Telefon</th>
        <th>Kategorie</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($fahrzeuge as $f): ?>
        <tr>
          <td><?= $f['id'] ?></td>
          <td><?= htmlspecialchars($f['name']) ?></td>
          <td><img src="<?= $f['bild_url'] ?>" alt="Bild" height="40"></td>
          <td><?= htmlspecialchars($f['preis7']) ?></td>
          <td><?= htmlspecialchars($f['preis30']) ?></td>
          <td><?= htmlspecialchars($f['status']) ?></td>
          <td><?= htmlspecialchars($f['vermietet_bis']) ?></td>
          <td><?= htmlspecialchars($f['halter_name']) ?></td>
          <td><?= htmlspecialchars($f['halter_telefon']) ?></td>
          <td><?= htmlspecialchars($f['kategorie']) ?></td>
          <td>
  <form action="delete_vehicle.php" method="POST" onsubmit="return confirm('Fahrzeug wirklich löschen?');">
    <input type="hidden" name="id" value="<?= $f['id'] ?>">
    <button type="submit">🗑️</button>
  </form>
  <form action="edit_vehicle.php" method="GET" style="margin-top: 5px;">
    <input type="hidden" name="id" value="<?= $f['id'] ?>">
    <button type="submit">✏️</button>
  </form>
</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>

</body>
</html>
