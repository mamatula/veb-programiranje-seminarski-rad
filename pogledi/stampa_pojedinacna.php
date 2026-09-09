<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Obračunski list <?= htmlspecialchars($obracun->brojObracuna) ?></title>
<style>
  body { font-family: "Times New Roman", serif; margin: 30px; color:#111; }
  .dugme-stampaj {
    margin-bottom:20px; padding:8px 16px; background:#2563eb; color:#fff; border:none;
    border-radius:4px; cursor:pointer; font-family: Arial, sans-serif; font-size:14px;
  }
  .dokument { border: 1px solid #000; padding: 16px 20px; max-width: 720px; }
  .zaglavlje-firme { text-align:center; margin-bottom:10px; }
  .zaglavlje-firme h2 { margin:0; font-size:18px; }
  .zaglavlje-firme p { margin:2px 0; font-size:12px; }
  .naslov-dokumenta { text-align:center; font-size:20px; font-weight:bold; margin: 14px 0; }
  table.podaci { width:100%; border-collapse: collapse; margin-bottom:14px; }
  table.podaci td { border: 1px solid #999; padding:6px 10px; font-size:14px; }
  table.stavke { width:100%; border-collapse: collapse; }
  table.stavke th, table.stavke td { border:1px solid #000; padding:6px 10px; font-size:13px; }
  table.stavke th { background:#e5e7eb; text-align:left; }
  .desno { text-align:right; }
  .ukupno-red td { font-weight:bold; }
  .potpisi { display:flex; justify-content:space-between; margin-top:40px; font-size:13px; font-family: Arial, sans-serif;}
  @media print { .dugme-stampaj { display:none; } }
</style>
</head>
<body>
  <button class="dugme-stampaj" onclick="window.print()">Štampaj</button>

  <div class="dokument">
    <div class="zaglavlje-firme">
      <h2>Tehnoplast d.o.o.</h2>
      <p>Bulevar Oslobođenja 45, Novi Sad &middot; PIB: 108452871 &middot; MB: 08123456</p>
    </div>
    <div class="naslov-dokumenta">OBRAČUNSKI LIST ZARADE</div>

    <table class="podaci">
      <tr>
        <td style="width:50%;"><strong>Broj obračunskog lista:</strong> <?= htmlspecialchars($obracun->brojObracuna) ?></td>
        <td><strong>Mesec/godina obračuna:</strong> <?= $nazivMeseca[$obracun->mesec] . ' ' . $obracun->godina ?></td>
      </tr>
      <tr>
        <td><strong>Zaposleni:</strong> <?= htmlspecialchars($obracun->imeZaposlenog . ' ' . $obracun->prezimeZaposlenog) ?></td>
        <td><strong>Radno mesto:</strong> <?= htmlspecialchars($obracun->radnoMesto) ?></td>
      </tr>
      <tr>
        <td><strong>JMBG:</strong> <?= htmlspecialchars($obracun->jmbg) ?></td>
        <td><strong>Broj radnih sati:</strong> <?= $obracun->brojRadnihSati ?></td>
      </tr>
      <tr>
        <td><strong>Osnovna zarada (RSD):</strong> <?= number_format($obracun->osnovnaZarada, 2, ',', '.') ?></td>
        <td><strong>Datum isplate:</strong> <?= htmlspecialchars($obracun->datumIsplate) ?></td>
      </tr>
    </table>

    <p><strong>Stavke obračuna:</strong></p>
    <table class="stavke">
      <thead>
        <tr><th>Rb</th><th>Šifra</th><th>Vrsta stavke</th><th>Tip</th><th class="desno">Iznos (RSD)</th></tr>
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
        <tr class="ukupno-red"><td colspan="4">Ukupno uvećanje</td><td class="desno"><?= number_format($obracun->ukupnoUvecanje, 2, ',', '.') ?></td></tr>
        <tr class="ukupno-red"><td colspan="4">Ukupno umanjenje</td><td class="desno"><?= number_format($obracun->ukupnoUmanjenje, 2, ',', '.') ?></td></tr>
        <tr class="ukupno-red"><td colspan="4">NETO ZA ISPLATU</td><td class="desno"><?= number_format($obracun->netoIznos, 2, ',', '.') ?></td></tr>
      </tbody>
    </table>

    <div class="potpisi">
      <div>Obračun izvršio(la): ____________________</div>
      <div>Rukovodilac: ____________________</div>
    </div>
  </div>
</body>
</html>
