<?php
require 'db.php';

if (!isset($_SESSION['admin']) || $_SESSION['admin'] != 1) {
  die("Zugriff verweigert");
}

// Alle Reservierungen mit Fahrzeug- & Nutzerdaten laden
$stmt = $pdo->query("
  SELECT r.*, 
         f.name AS fahrzeug_name,
         u.benutzername,
         u.telefon
  FROM reservierungen r
  JOIN fahrzeuge f ON r.fahrzeug_id = f.id
  JOIN nutzer u ON r.nutzer_id = u.id
  ORDER BY r.erstellt_am DESC
");
$reservierungen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Aktive Reservierungen – Admin</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .admin-reservierungen {
      max-width: 1000px;
      margin: 2rem auto;
      padding: 1rem;
      background: #1a1a1a;
      color: white;
      border-radius: 8px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      color: white;
    }
    th, td {
      padding: 12px;
      border-bottom: 1px solid #444;
      text-align: left;
    }
    th {
      background: #222;
    }
    tr:nth-child(even) {
      background-color: #2a2a2a;
    }
  </style>
</head>
<body>

  <header class="navbar">
  <div class="logo">J+T Rental</div>
  <nav>
    <ul class="nav-links">
      <li><a href="index.php">Startseite</a></li>
      <li><a href="admin.php">Adminbereich</a></li>
      <li><a href="aktive_reservierungen.php" class="active">Reservierungen</a></li>
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


<section class="admin-reservierungen">
  <h2>Aktive Reservierungen</h2>

  <?php if (count($reservierungen) === 0): ?>
    <p>Es liegen aktuell keine Reservierungen vor.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Fahrzeug</th>
          <th>Nutzer</th>
          <th>Telefon</th>
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
          <td><?= htmlspecialchars($r['benutzername']) ?></td>
          <td><?= htmlspecialchars($r['telefon']) ?></td>
          <td><?= htmlspecialchars($r['startdatum']) ?></td>
          <td><?= $r['dauer'] ?> Tage</td>
          <td><?= htmlspecialchars($r['status']) ?></td>
          <td><?= htmlspecialchars($r['erstellt_am']) ?></td>
          <td><button class="delete-btn" data-id="<?= $r['id'] ?>">🗑️</button></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</section>
<script>
document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const id = btn.dataset.id;
        if (!confirm("Reservierung wirklich löschen?")) return;

        fetch('reservierung_loeschen.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id: id})
        })
        .then(res => res.json())
        .then(json => {
            if (json.success) {
                const zeile = document.getElementById('res' + id);
                if (zeile) zeile.remove();
            } else {
                alert("Fehler: " + json.error);
            }
        })
        .catch(err => {
            alert("Technischer Fehler: " + err.message);
        });
    });
});
</script>

</body>
</html>
