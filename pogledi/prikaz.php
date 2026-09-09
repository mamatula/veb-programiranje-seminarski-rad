<?php require __DIR__ . '/zaglavlje.php'; ?>

<div class="kartica">
  <h2>Obračunski list <?= htmlspecialchars($obracun->brojObracuna) ?></h2>

  <?php if (isset($_GET['sacuvano'])): ?>
    <div class="poruka poruka-uspeh">Obračunski list je uspešno sačuvan.</div>
  <?php endif; ?>

  <div class="mreza-polja" style="margin-bottom:20px; font-size:14px;">
    <div><strong>Zaposleni:</strong> <?= htmlspecialchars($obracun->imeZaposlenog . ' ' . $obracun->prezimeZaposlenog) ?></div>
    <div><strong>Radno mesto:</strong> <?= htmlspecialchars($obracun->radnoMesto) ?></div>
    <div><strong>JMBG:</strong> <?= htmlspecialchars($obracun->jmbg) ?></div>
    <div><strong>Broj radnih sati:</strong> <?= $obracun->brojRadnihSati ?></div>
    <div><strong>Period obračuna:</strong> <?= $nazivMeseca[$obracun->mesec] . ' ' . $obracun->godina ?></div>
    <div><strong>Datum isplate:</strong> <?= htmlspecialchars($obracun->datumIsplate) ?></div>
    <div><strong>Osnovna zarada:</strong> <?= number_format($obracun->osnovnaZarada, 2, ',', '.') ?> RSD</div>
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
      <?php foreach ($obracun->stavke as $s): ?>
      <tr>
        <td><?= $s->redniBroj ?></td>
        <td><?= htmlspecialchars($s->vrstaStavke->sifra) ?></td>
        <td><?= htmlspecialchars($s->vrstaStavke->naziv) ?></td>
        <td><?= $s->vrstaStavke->tip === 'uvecanje' ? 'Uvećanje' : 'Umanjenje' ?></td>
        <td class="desno"><?= number_format($s->iznos, 2, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="text-align:right; margin-top:16px; font-size:14px;">
    <div>Ukupno uvećanje: <strong><?= number_format($obracun->ukupnoUvecanje, 2, ',', '.') ?></strong> RSD</div>
    <div>Ukupno umanjenje: <strong><?= number_format($obracun->ukupnoUmanjenje, 2, ',', '.') ?></strong> RSD</div>
    <div style="font-size:16px; margin-top:4px;">Neto za isplatu: <strong><?= number_format($obracun->netoIznos, 2, ',', '.') ?></strong> RSD</div>
  </div>

  <div style="margin-top:20px;">
    <a href="izmena.php?id=<?= $obracun->idObracunskogLista ?>" class="dugme dugme-sivo">Izmeni</a>
    <a href="stampa_pojedinacna.php?id=<?= $obracun->idObracunskogLista ?>" class="dugme dugme-plavo" target="_blank">Štampaj</a>
    <a href="obracuni.php" class="dugme dugme-sivo">Nazad na listu</a>
  </div>
</div>

<?php require __DIR__ . '/podnozje.php'; ?>
