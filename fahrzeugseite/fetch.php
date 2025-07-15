<?php
$pdo = new PDO('mysql:host=localhost;dbname=fahrzeug_db;charset=utf8', 'root', '');

$res = $pdo->query("SELECT * FROM fahrzeuge ORDER BY id DESC");
echo json_encode($res->fetchAll(PDO::FETCH_ASSOC));
