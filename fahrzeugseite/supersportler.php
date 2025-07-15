<?php
require 'db.php';

// Nur Fahrzeuge mit Kategorie "autos" laden
$stmt = $pdo->prepare("SELECT * FROM fahrzeuge WHERE kategorie = ?");
$stmt->execute(['supersportler']);
$fahrzeuge = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Supersportler – J+T Rental</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- NAVIGATION -->
<header class="navbar">
  <div class="logo">J+T Rental</div>
  <nav>
    <ul class="nav-links">
      <li><a href="index.php">Startseite</a></li>
      <li><a href="importler.php">Importler</a></li>
      <li><a href="supersportler.php" class="active">Supersportler</a></li>
      <li><a href="nutzfahrzeuge.php">Nutzfahrzeuge</a></li>
      <li><a href="autos.php">Autos</a></li>
      <li><a href="bikes.php">Bikes</a></li>
      <li><a href="fluggeraete.php">Fluggeräte</a></li>
      <?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
        <li><a href="admin.php">Adminbereich</a></li>
        <li><a href="aktive_reservierungen.php">Reservierungen</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <div class="navbar-user">
    <?php if (isset($_SESSION['nutzer'])): ?>
      Willkommen, <?= htmlspecialchars($_SESSION['nutzer']) ?> |
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="#" onclick="openAuth()">Login / Registrieren</a>
    <?php endif; ?>
  </div>
</header>

<!-- HERO-BILD -->
<section class="hero">
  <img src="https://cdn.pixabay.com/photo/2020/02/03/12/30/bmw-4815353_1280.jpg" alt="Autos Hero">
</section>

<!-- FAHRZEUGE -->
<section class="vehicle-grid">
  <?php foreach ($fahrzeuge as $f): ?>
    <div class="vehicle-card">
      <img src="<?= htmlspecialchars($f['bild_url']) ?>" alt="<?= htmlspecialchars($f['name']) ?>">
      <div class="vehicle-info">
        <h3><?= htmlspecialchars($f['name']) ?></h3>
        <p>7– Tage <?= htmlspecialchars($f['preis7']) ?><br>
           30– Tage <?= htmlspecialchars($f['preis30']) ?></p>
        <?php if ($f['status'] === 'available'): ?>
          <p class="available">Verfügbar</p>
        <?php else: ?>
          <p class="unavailable">Vermietet bis<br><?= htmlspecialchars($f['vermietet_bis']) ?></p>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>

</body>
</html>
