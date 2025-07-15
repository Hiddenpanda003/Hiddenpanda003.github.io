<?php
require 'db.php';

// Fehler & Erfolg vorbereiten (für Modal-Anzeige)
$loginError = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

$registerError = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);

$registerSuccess = $_SESSION['register_success'] ?? null;
unset($_SESSION['register_success']);
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>J+T Rental – Startseite</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<!-- NAVIGATION -->
<header class="navbar">
  <div class="logo">J+T Rental</div>
  <nav>
    <ul class="nav-links">
      <li><a href="index.php" class="active">Startseite</a></li>
      <li><a href="importler.php">Importler</a></li>
      <li><a href="supersportler.php">Supersportler</a></li>
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

<!-- HERO-BANNER -->
<section class="hero">
  <img src="https://cdn.pixabay.com/photo/2021/06/17/10/38/ferrari-6342699_1280.jpg" alt="Hero Car">
</section>

<!-- ADMIN-FORMULAR -->
<?php if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1): ?>
<section class="form-section">
  <h2>Neues Fahrzeug hinzufügen</h2>
  <form id="vehicleForm">
    <input type="text" id="name" placeholder="Fahrzeugname" required>
    <input type="url" id="image" placeholder="Bild-URL" required>
    <input type="text" id="price7" placeholder="7 Tage Preis" required>
    <input type="text" id="price30" placeholder="30 Tage Preis" required>
    <input type="text" id="kaution" placeholder="Kaution in €" required>
    <select id="status">
      <option value="available">Verfügbar</option>
      <option value="unavailable">Nicht verfügbar bis...</option>
    </select>
    <input type="text" id="untilDate" placeholder="Bis-Datum (z. B. 30.07.2025 12:00)">
    
    <!-- Halterdaten (nicht sichtbar auf Website) -->
    <input type="text" id="halterName" placeholder="Fahrzeughalter Name" required>
    <input type="text" id="halterTelefon" placeholder="Fahrzeughalter Telefonnummer" required>
    
    <label>Kategorie / Seite</label>
    <select id="kategorie" required>
    <option value="autos">Autos</option>
    <option value="bikes">Bikes</option>
    <option value="nutzfahrzeuge">Nutzfahrzeuge</option>
    <option value="supersportler">Supersportler</option>
    <option value="importler">Importler</option>
    <option value="fluggeraete">Fluggeräte</option>
    </select> 

    <button type="submit">Fahrzeug speichern</button>
  </form>
</section>
<?php endif; ?>

<!-- FAHRZEUG-ANZEIGE -->
<section class="vehicle-grid" id="vehicleGrid"></section>

<!-- MODAL: LOGIN & REGISTRIERUNG -->
<?php if (!isset($_SESSION['nutzer'])): ?>
<div id="authOverlay" class="auth-overlay hidden">
  <div class="auth-modal">
    <span class="auth-close" onclick="closeAuth()">×</span>

    <!-- LOGIN -->
    <div id="authLogin">
      <h2>Login</h2>
      <?php if ($loginError): ?>
        <div class="auth-error"><?= $loginError ?></div>
      <?php endif; ?>
      <form method="POST" action="login.php">
        <input type="text" name="benutzername" placeholder="Benutzername" required>
        <input type="password" name="passwort" placeholder="Passwort" required>
        <button type="submit">Einloggen</button>
        <p>Noch kein Konto? <a href="#" onclick="switchToRegister()">Registrieren</a></p>
      </form>
    </div>

    <!-- REGISTRIERUNG -->
    <div id="authRegister" class="hidden">
      <h2>Registrieren</h2>
      <?php if ($registerError): ?>
        <div class="auth-error"><?= $registerError ?></div>
      <?php endif; ?>
      <?php if ($registerSuccess): ?>
        <div class="auth-success"><?= $registerSuccess ?></div>
      <?php endif; ?>
      <form method="POST" action="register.php">
        <input type="text" name="benutzername" placeholder="Benutzername" required>
        <input type="text" name="telefon" placeholder="Telefonnummer" required>
        <input type="password" name="passwort" placeholder="Passwort" required>
        <button type="submit">Registrieren</button>
        <p>Schon registriert? <a href="#" onclick="switchToLogin()">Login</a></p>
      </form>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- SCRIPTS -->
<script>
function loadVehicles() {
  fetch('fetch.php')
    .then(res => res.json())
    .then(data => {
      const grid = document.getElementById('vehicleGrid');
      grid.innerHTML = '';
      data.forEach(v => {
        const card = document.createElement('div');
        card.className = 'vehicle-card';
        card.innerHTML = `
          <img src="${v.bild_url}" alt="${v.name}">
          <div class="vehicle-info">
            <h3>${v.name}</h3>
            <p>7– Tage ${v.preis7}<br>28– Tage ${v.preis28}</p>
            ${v.status === 'available'
              ? `<p class="available">Verfügbar</p>`
              : `<p class="unavailable">Vermietet bis<br>${v.vermietet_bis}</p>`}
          </div>
        `;
        grid.appendChild(card);
      });
    });
}

document.getElementById('vehicleForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const data = {
    name: document.getElementById('name').value,
    image: document.getElementById('image').value,
    price7: document.getElementById('price7').value,
    price30: document.getElementById('price30').value,
    status: document.getElementById('status').value,
    untilDate: document.getElementById('untilDate').value,
    halterName: document.getElementById('halterName').value,
    halterTelefon: document.getElementById('halterTelefon').value,
    kategorie: document.getElementById('kategorie').value,
    kaution: document.getElementById('kaution').value
  };
  fetch('insert.php', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify(data)
  }).then(() => {
    document.getElementById('vehicleForm').reset();
    loadVehicles();
  });
});

function openAuth() {
  document.getElementById('authOverlay')?.classList.remove('hidden');
  switchToLogin();
}

function closeAuth() {
  document.getElementById('authOverlay')?.classList.add('hidden');
}

function switchToRegister() {
  document.getElementById('authLogin')?.classList.add('hidden');
  document.getElementById('authRegister')?.classList.remove('hidden');
}

function switchToLogin() {
  document.getElementById('authRegister')?.classList.add('hidden');
  document.getElementById('authLogin')?.classList.remove('hidden');
}

// Beim Laden automatisch Modal öffnen bei Fehler
window.onload = () => {
  loadVehicles();
  <?php if ($loginError): ?> openAuth(); <?php endif; ?>
  <?php if ($registerError || $registerSuccess): ?>
    openAuth(); switchToRegister();
  <?php endif; ?>
};
</script>



</body>
</html>
