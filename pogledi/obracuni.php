<?php require __DIR__ . '/zaglavlje.php'; ?>

<div class="kartica">
  <h2>Lista obračuna</h2>

  <?php if ($poruka): ?>
    <div class="poruka poruka-uspeh"><?= htmlspecialchars($poruka) ?></div>
  <?php endif; ?>

  <form method="get" class="filter-traka">
    <div class="polje">
      <label>Prezime zaposlenog</label>
      <input type="text" name="prezime" value="<?= htmlspecialchars($prezime) ?>" placeholder="npr. Petrović">
    </div>
    <div class="polje">
      <label>Mesec</label>
      <select name="mesec">
        <option value="">Svi</option>
        <?php for ($m = 1; $m <= 12; $m++): ?>
          <option value="<?= $m ?>" <?= ($mesecParam === $m) ? 'selected' : '' ?>><?= $nazivMeseca[$m] ?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div class="polje">
      <label>Godina</label>
      <input type="text" name="godina" value="<?= htmlspecialchars($godina) ?>" placeholder="2026">
    </div>
    <button type="submit" class="dugme dugme-plavo">Traži</button>
    <a href="obracuni.php" class="dugme dugme-sivo">Svi</a>
    <a href="stampa_spisak.php?<?= $upitStampa ?>" class="dugme dugme-sivo" target="_blank">Štampaj spisak</a>
  </form>

  <table>
    <thead>
      <tr>
        <th>Broj obračuna</th>
        <th>Zaposleni</th>
        <th>Radno mesto</th>
        <th>Mesec/god.</th>
        <th class="desno">Osnovna</th>
        <th class="desno">Neto</th>
        <th class="centrirano">Akcija</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($obracuni)): ?>
        <tr><td colspan="7" class="centrirano opis">Nema pronađenih obračuna.</td></tr>
      <?php endif; ?>
      <?php foreach ($obracuni as $o): ?>
      <tr>
        <td><?= htmlspecialchars($o->brojObracuna) ?></td>
        <td><?= htmlspecialchars($o->imeZaposlenog . ' ' . $o->prezimeZaposlenog) ?></td>
        <td><?= htmlspecialchars($o->radnoMesto) ?></td>
        <td><?= str_pad((string) $o->mesec, 2, '0', STR_PAD_LEFT) . '/' . $o->godina ?></td>
        <td class="desno"><?= number_format($o->osnovnaZarada, 2, ',', '.') ?></td>
        <td class="desno"><?= number_format($o->netoIznos, 2, ',', '.') ?></td>
        <td class="centrirano">
          <a href="prikaz.php?id=<?= $o->idObracunskogLista ?>" class="dugme dugme-plavo dugme-malo">Prikaz</a>
          <a href="izmena.php?id=<?= $o->idObracunskogLista ?>" class="dugme dugme-sivo dugme-malo">Izmeni</a>
          <a href="obrisi.php?id=<?= $o->idObracunskogLista ?>" class="dugme dugme-crveno dugme-malo"
             onclick="return confirm('Da li sigurno želite da obrišete obračun <?= htmlspecialchars($o->brojObracuna) ?>?');">Obriši</a>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/podnozje.php'; ?>
