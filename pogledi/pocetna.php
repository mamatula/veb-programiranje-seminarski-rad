<?php require __DIR__ . '/zaglavlje.php'; ?>

<div class="kartica">
  <h1>Dobrodošli, <?= htmlspecialchars($_SESSION['korisnik_ime']) ?>!</h1>
  <p class="opis">
    Evidencija obračuna zarada zaposlenih u preduzeću. Svaki obračunski list sadrži zaglavlje
    sa podacima o zaposlenom i stavke obračuna (dodaci i odbici), na osnovu kojih se automatski
    izračunava neto iznos za isplatu.
  </p>
  <a href="obracuni.php" class="dugme dugme-plavo">Pregled obračuna</a>
  <a href="unos.php" class="dugme dugme-sivo">+ Novi obračun</a>
  <a href="stampa_spisak.php" class="dugme dugme-sivo">Štampa</a>
</div>

<?php require __DIR__ . '/podnozje.php'; ?>
