<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= htmlspecialchars($naslovStranice ?? 'Obračun zarada') ?></title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="navbar">
  <div class="naziv">Obračun zarada</div>
  <ul class="meni">
    <li><a href="pocetna.php" class="<?= ($aktivnaStranica ?? '') === 'pocetna' ? 'aktivno' : '' ?>">Početna</a></li>
    <li><a href="obracuni.php" class="<?= ($aktivnaStranica ?? '') === 'obracuni' ? 'aktivno' : '' ?>">Obračuni</a></li>
    <li><a href="unos.php" class="<?= ($aktivnaStranica ?? '') === 'unos' ? 'aktivno' : '' ?>">Novi obračun</a></li>
    <li><a href="stampa_spisak.php" class="<?= ($aktivnaStranica ?? '') === 'stampa' ? 'aktivno' : '' ?>">Štampa</a></li>
  </ul>
  <div class="korisnik">
    <?= htmlspecialchars(($_SESSION['korisnik_ime'] ?? '') . ' ' . ($_SESSION['korisnik_prezime'] ?? '')) ?>
    <a href="odjava.php">Odjava</a>
  </div>
</div>
<div class="sadrzaj">
