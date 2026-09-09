<?php require __DIR__ . '/zaglavlje.php'; ?>

<div class="kartica">
  <h2>Izmena obračunskog lista <?= htmlspecialchars($postojeci->brojObracuna) ?></h2>

  <?php if ($greska): ?>
    <div class="poruka poruka-greska"><?= htmlspecialchars($greska) ?></div>
  <?php endif; ?>

  <form method="post" id="forma-obracuna" novalidate>
    <h2 style="font-size:14px; color:#64748b; margin-top:0;">Zaglavlje obračuna</h2>
    <div class="mreza-polja">
      <div class="polje">
        <label>Broj obračuna</label>
        <input type="text" id="polje-broj-obracuna" value="<?= htmlspecialchars($postojeci->brojObracuna) ?>" readonly style="background:#f1f5f9;">
      </div>
      <div class="polje">
        <label>Datum isplate *</label>
        <input type="date" id="polje-datum-isplate" name="datum_isplate" value="<?= htmlspecialchars($uneseno['datum_isplate']) ?>">
        <div class="greska"></div>
      </div>
      <div class="polje">
        <label>Mesec obračuna *</label>
        <select name="mesec">
          <?php for ($m = 1; $m <= 12; $m++): ?>
            <option value="<?= $m ?>" <?= $uneseno['mesec'] == $m ? 'selected' : '' ?>><?= $nazivMeseca[$m] ?></option>
          <?php endfor; ?>
        </select>
      </div>
      <div class="polje">
        <label>Godina obračuna *</label>
        <input type="number" name="godina" value="<?= htmlspecialchars((string) $uneseno['godina']) ?>">
      </div>
      <div class="polje">
        <label>Ime zaposlenog *</label>
        <input type="text" id="polje-ime" name="ime" value="<?= htmlspecialchars($uneseno['ime']) ?>">
        <div class="greska"></div>
      </div>
      <div class="polje">
        <label>Prezime zaposlenog *</label>
        <input type="text" id="polje-prezime" name="prezime" value="<?= htmlspecialchars($uneseno['prezime']) ?>">
        <div class="greska"></div>
      </div>
      <div class="polje">
        <label>JMBG *</label>
        <input type="text" id="polje-jmbg" name="jmbg" maxlength="13" value="<?= htmlspecialchars($uneseno['jmbg']) ?>">
        <div class="greska"></div>
      </div>
      <div class="polje">
        <label>Radno mesto *</label>
        <input type="text" id="polje-radno-mesto" name="radno_mesto" value="<?= htmlspecialchars($uneseno['radno_mesto']) ?>">
        <div class="greska"></div>
      </div>
      <div class="polje">
        <label>Broj radnih sati *</label>
        <input type="text" id="polje-broj-sati" name="broj_sati" value="<?= htmlspecialchars((string) $uneseno['broj_sati']) ?>">
        <div class="greska"></div>
      </div>
      <div class="polje">
        <label>Osnovna zarada (RSD) *</label>
        <input type="text" id="polje-osnovna" name="osnovna" value="<?= htmlspecialchars((string) $uneseno['osnovna']) ?>">
        <div class="greska"></div>
      </div>
    </div>

    <h2 style="font-size:14px; color:#64748b;">Stavke obračuna</h2>
    <table style="margin-bottom:10px;">
      <thead>
        <tr>
          <th style="width:40px;">Rb</th>
          <th>Vrsta stavke</th>
          <th style="width:160px;">Iznos (RSD)</th>
          <th style="width:40px;"></th>
        </tr>
      </thead>
      <tbody id="telo-stavki"></tbody>
    </table>
    <button type="button" id="dugme-dodaj-stavku" class="dugme dugme-sivo dugme-malo">+ Dodaj stavku</button>

    <div style="text-align:right; margin-top:16px; font-size:14px;">
      <div>Ukupno uvećanje: <strong id="prikaz-uvecanje">0.00</strong> RSD</div>
      <div>Ukupno umanjenje: <strong id="prikaz-umanjenje">0.00</strong> RSD</div>
      <div style="font-size:16px; margin-top:4px;">Neto za isplatu: <strong id="prikaz-neto">0.00</strong> RSD</div>
    </div>

    <div style="margin-top:20px;">
      <button type="submit" class="dugme dugme-plavo">Sačuvaj izmene</button>
      <a href="prikaz.php?id=<?= $id ?>" class="dugme dugme-sivo">Otkaži</a>
    </div>
  </form>
</div>

<?php
$vrsteZaJs = [];
foreach ($vrsteStavki as $v) {
    $vrsteZaJs[] = ['id' => $v->idVrsteStavke, 'sifra' => $v->sifra, 'naziv' => $v->naziv, 'tip' => $v->tip];
}
?>
<script>
const VRSTE_STAVKI = <?= json_encode($vrsteZaJs, JSON_UNESCAPED_UNICODE) ?>;
const POSTOJECE_STAVKE = <?= json_encode($prikazStavke, JSON_UNESCAPED_UNICODE) ?>;
</script>
<script src="js/validacija.js"></script>

<?php require __DIR__ . '/podnozje.php'; ?>
