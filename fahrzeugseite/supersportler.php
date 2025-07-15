<?php
require 'db.php';
$now = date('Y-m-d H:i:s');
$pdo->prepare("UPDATE fahrzeuge SET status = 'available', vermietet_bis = NULL WHERE status = 'unavailable' AND vermietet_bis IS NOT NULL AND STR_TO_DATE(vermietet_bis, '%d.%m.%Y %H:%i') <= ?")
    ->execute([$now]);
// Fahrzeuge mit Kategorie "importler"
$stmt = $pdo->prepare("SELECT * FROM fahrzeuge WHERE kategorie = ?");
$stmt->execute(['supersportler']);
$fahrzeuge = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Supersportler – J+T Rental</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .modal {
      display: flex;
      justify-content: center;
      align-items: center;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-color: rgba(0,0,0,0.6);
      z-index: 999;
    }
    .modal.hidden { display: none; }
    .modal-content {
      background: #1a1a1a;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
      color: white;
    }
    .modal-content input,
    .modal-content select {
      width: 100%;
      margin: 10px 0;
      padding: 8px;
      background: #2a2a2a;
      color: white;
      border: 1px solid #444;
      border-radius: 4px;
    }
    .close {
      float: right;
      font-size: 20px;
      cursor: pointer;
    }
    .vehicle-card {
      cursor: pointer;
    }
  </style>
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
      <a href="profil.php">Profil</a> |
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="index.php" onclick="openAuth()">Login / Registrieren</a>
    <?php endif; ?>
  </div>
</header>

<!-- FAHRZEUG-GRID -->
<section class="vehicle-grid" id="vehicleGrid">
  <?php foreach ($fahrzeuge as $f): ?>
    <div class="vehicle-card" 
         data-fahrzeug-id="<?= $f['id'] ?>"
         data-preis7="<?= $f['preis7'] ?>"
         data-preis30="<?= $f['preis30'] ?>"
         data-kaution="<?= $f['kaution'] ?>">
      <img src="<?= htmlspecialchars($f['bild_url']) ?>" alt="<?= htmlspecialchars($f['name']) ?>">
      <div class="vehicle-info">
        <h3><?= htmlspecialchars($f['name']) ?></h3>
        <p>7– Tage <?= htmlspecialchars($f['preis7']) ?>$<br>
           28– Tage <?= htmlspecialchars($f['preis30']) ?>$</p>
        <?php if ($f['status'] === 'available'): ?>
          <p class="available">Verfügbar</p>
        <?php else: ?>
          <p class="unavailable">Vermietet bis<br><?= htmlspecialchars($f['vermietet_bis']) ?></p>
        <?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</section>

<!-- RESERVIERUNGSMODAL -->
<div id="reservierungModal" class="modal hidden">
  <div class="modal-content">
    <span onclick="closeReservierung()" class="close">&times;</span>
    <h2>Fahrzeug reservieren</h2>
    <form id="reservierungForm">
      <input type="hidden" id="fahrzeugId">

      <label>Mietbeginn</label>
      <input type="datetime-local" id="startdatum" required>

      <label>Mietdauer</label>
      <select id="dauer" required>
        <option value="7">7 Tage</option>
        <option value="30">28 Tage</option>
      </select>

      <button type="submit">Reservieren</button>
      <button type="button" onclick="closeReservierung()" style="margin-top: 10px;">Abbrechen</button>
    </form>

    <div id="reservierungsBestaetigung" class="hidden" style="margin-top: 20px; color: #7cff7c;"></div>
  </div>
</div>

<!-- SCRIPT -->
<script>
function closeReservierung() {
  document.getElementById('reservierungModal').classList.add('hidden');
  document.getElementById('reservierungsBestaetigung').classList.add('hidden');
  document.getElementById('reservierungsBestaetigung').innerHTML = '';
  document.getElementById('reservierungForm').style.display = '';
}

document.querySelectorAll('.vehicle-card').forEach(card => {
  card.addEventListener('click', () => {
    document.getElementById('fahrzeugId').value = card.dataset.fahrzeugId;
    document.getElementById('reservierungModal').classList.remove('hidden');
  });
});

document.getElementById('reservierungForm').addEventListener('submit', function(e) {
  e.preventDefault();

  const fahrzeugId = document.getElementById('fahrzeugId').value;
  const startdatum = document.getElementById('startdatum').value;
  const dauer = parseInt(document.getElementById('dauer').value);
  const card = document.querySelector(`.vehicle-card[data-fahrzeug-id="${fahrzeugId}"]`);

  const preis = dauer === 7 ? parseFloat(card.dataset.preis7 || "0") : parseFloat(card.dataset.preis30 || "0");
  const kaution = parseFloat(card.dataset.kaution || "0");

  const data = {
    fahrzeug_id: fahrzeugId,
    startdatum: startdatum,
    dauer: dauer
  };

  fetch('reservieren.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(res => res.json())
  .then(json => {
    if (json.success) {
      document.getElementById('reservierungForm').style.display = 'none';
      const bestaetigung = document.getElementById('reservierungsBestaetigung');
      bestaetigung.classList.remove('hidden');
      bestaetigung.innerHTML = `
       <strong>${json.message}</strong><br><br>
        Ihre Kosten belaufen sich auf: <strong>${json.kosten}</strong><br>
        Sie haben für den Zeitraum <strong>${json.dauer}</strong> reserviert.<br>
        Ihre Kaution beträgt: <strong>${json.kaution}</strong><br><br>
        <a href="supersportler.php" style="color:#fff; text-decoration:underline;">Zurück zur Übersicht</a>
      `;
    } else {
      alert("Fehler: " + (json.error ?? "Unbekannt"));
    }
  })
  .catch(err => {
    alert("Technischer Fehler: " + err.message);
  });
});
</script>

</body>
</html>