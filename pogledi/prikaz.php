<?php require __DIR__ . '/zaglavlje.php'; ?>

<div class="kartica">
  <h2>Obračunski list <?= htmlspecialchars($obracun->getBrojObracuna()) ?></h2>

  <?php if (isset($_GET['sacuvano'])): ?>
    <div class="poruka poruka-uspeh">Obračunski list je uspešno sačuvan.</div>
  <?php endif; ?>

  <div class="mreza-polja" style="margin-bottom:20px; font-size:14px;">
    <div><strong>Zaposleni:</strong> <?= htmlspecialchars($obracun->getImeZaposlenog() . ' ' . $obracun->getPrezimeZaposlenog()) ?></div>
    <div><strong>Radno mesto:</strong> <?= htmlspecialchars($obracun->getRadnoMesto()) ?></div>
    <div><strong>JMBG:</strong> <?= htmlspecialchars($obracun->getJmbg()) ?></div>
    <div><strong>Broj radnih sati:</strong> <?= $obracun->getBrojRadnihSati() ?></div>
    <div><strong>Period obračuna:</strong> <?= $nazivMeseca[$obracun->getMesec()] . ' ' . $obracun->getGodina() ?></div>
    <div><strong>Datum isplate:</strong> <?= htmlspecialchars($obracun->getDatumIsplate()) ?></div>
    <div><strong>Osnovna zarada:</strong> <?= number_format($obracun->getOsnovnaZarada(), 2, ',', '.') ?> RSD</div>
  </div>

  <h2 style="font-size:14px; color:#64748b;">Stavke obračuna</h2>
  <table>
    <thead>
      <tr>
        <th style="width:40px;">Rb</th>
        <th style="width:70px;">Šifra</th>
        <th>Vrsta stavke</th>
        <th>Tip</th>
        <th class="desno">Iznos</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($obracun->getStavke() as $s): ?>
      <tr>
        <td><?= $s->getRedniBroj() ?></td>
        <td><?= htmlspecialchars($s->getVrstaStavke()->sifra) ?></td>
        <td><?= htmlspecialchars($s->getVrstaStavke()->naziv) ?></td>
        <td><?= $s->getVrstaStavke()->tip === 'uvecanje' ? 'Uvećanje' : 'Umanjenje' ?></td>
        <td class="desno"><?= number_format($s->getIznos(), 2, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="text-align:right; margin-top:16px; font-size:14px;">
    <div>Ukupno uvećanje: <strong><?= number_format($obracun->getUkupnoUvecanje(), 2, ',', '.') ?></strong> RSD</div>
    <div>Ukupno umanjenje: <strong><?= number_format($obracun->getUkupnoUmanjenje(), 2, ',', '.') ?></strong> RSD</div>
    <div style="font-size:16px; margin-top:4px;">Neto za isplatu: <strong><?= number_format($obracun->getNetoIznos(), 2, ',', '.') ?></strong> RSD</div>
  </div>

  <div style="margin-top:20px;">
    <a href="izmena.php?id=<?= $obracun->getIdObracunskogLista() ?>" class="dugme dugme-sivo">Izmeni</a>
    <a href="stampa_pojedinacna.php?id=<?= $obracun->getIdObracunskogLista() ?>" class="dugme dugme-plavo" target="_blank">Štampaj</a>
    <a href="obracuni.php" class="dugme dugme-sivo">Nazad na listu</a>
  </div>
</div>

<?php require __DIR__ . '/podnozje.php'; ?>