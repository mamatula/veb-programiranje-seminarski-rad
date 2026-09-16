<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Obračunski list <?= htmlspecialchars($obracun->getBrojObracuna()) ?></title>
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
        <td style="width:50%;"><strong>Broj obračunskog lista:</strong> <?= htmlspecialchars($obracun->getBrojObracuna()) ?></td>
        <td><strong>Mesec/godina obračuna:</strong> <?= $nazivMeseca[$obracun->getMesec()] . ' ' . $obracun->getGodina() ?></td>
      </tr>
      <tr>
        <td><strong>Zaposleni:</strong> <?= htmlspecialchars($obracun->getImeZaposlenog() . ' ' . $obracun->getPrezimeZaposlenog()) ?></td>
        <td><strong>Radno mesto:</strong> <?= htmlspecialchars($obracun->getRadnoMesto()) ?></td>
      </tr>
      <tr>
        <td><strong>JMBG:</strong> <?= htmlspecialchars($obracun->getJmbg()) ?></td>
        <td><strong>Broj radnih sati:</strong> <?= $obracun->getBrojRadnihSati() ?></td>
      </tr>
      <tr>
        <td><strong>Osnovna zarada (RSD):</strong> <?= number_format($obracun->getOsnovnaZarada(), 2, ',', '.') ?></td>
        <td><strong>Datum isplate:</strong> <?= htmlspecialchars($obracun->getDatumIsplate()) ?></td>
      </tr>
    </table>

    <p><strong>Stavke obračuna:</strong></p>
    <table class="stavke">
      <thead>
        <tr><th>Rb</th><th>Šifra</th><th>Vrsta stavke</th><th>Tip</th><th class="desno">Iznos (RSD)</th></tr>
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
        <tr class="ukupno-red"><td colspan="4">Ukupno uvećanje</td><td class="desno"><?= number_format($obracun->getUkupnoUvecanje(), 2, ',', '.') ?></td></tr>
        <tr class="ukupno-red"><td colspan="4">Ukupno umanjenje</td><td class="desno"><?= number_format($obracun->getUkupnoUmanjenje(), 2, ',', '.') ?></td></tr>
        <tr class="ukupno-red"><td colspan="4">NETO ZA ISPLATU</td><td class="desno"><?= number_format($obracun->getNetoIznos(), 2, ',', '.') ?></td></tr>
      </tbody>
    </table>

    <div class="potpisi">
      <div>Obračun izvršio(la): ____________________</div>
      <div>Rukovodilac: ____________________</div>
    </div>
  </div>
</body>
</html>