<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<title>Štampa spiska obračuna</title>
<style>
  body { font-family: Arial, sans-serif; margin: 30px; color:#1f2937; }
  h1 { font-size: 18px; }
  table { width:100%; border-collapse: collapse; font-size: 13px; margin-top:16px; }
  th, td { border: 1px solid #94a3b8; padding: 6px 8px; text-align:left; }
  th { background:#f1f5f9; }
  .desno { text-align:right; }
  .traka-dugmadi { margin-bottom:20px; }
  .traka-dugmadi button {
    padding:8px 16px; background:#2563eb; color:#fff; border:none;
    border-radius:4px; cursor:pointer; font-size:14px;
  }
  @media print { .traka-dugmadi { display:none; } }
</style>
</head>
<body>
  <div class="traka-dugmadi">
    <button onclick="window.print()">Štampaj</button>
  </div>

  <h1>Spisak obračuna zarada<?= $imaFilter ? ' — filtrirano' : '' ?></h1>
  <?php if ($imaFilter): ?>
    <p>
      Filter:
      <?= $prezime !== '' ? 'Prezime: ' . htmlspecialchars($prezime) . '&nbsp;&nbsp;' : '' ?>
      <?= ($mesec !== '' && isset($nazivMeseca[(int) $mesec])) ? 'Mesec: ' . $nazivMeseca[(int) $mesec] . '&nbsp;&nbsp;' : '' ?>
      <?= $godina !== '' ? 'Godina: ' . htmlspecialchars($godina) : '' ?>
    </p>
  <?php endif; ?>

  <table>
    <thead>
      <tr>
        <th>Broj obračuna</th>
        <th>Zaposleni</th>
        <th>Radno mesto</th>
        <th>Mesec/god.</th>
        <th class="desno">Osnovna</th>
        <th class="desno">Neto</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($obracuni as $o): ?>
      <tr>
        <td><?= htmlspecialchars($o->brojObracuna) ?></td>
        <td><?= htmlspecialchars($o->imeZaposlenog . ' ' . $o->prezimeZaposlenog) ?></td>
        <td><?= htmlspecialchars($o->radnoMesto) ?></td>
        <td><?= str_pad((string) $o->mesec, 2, '0', STR_PAD_LEFT) . '/' . $o->godina ?></td>
        <td class="desno"><?= number_format($o->osnovnaZarada, 2, ',', '.') ?></td>
        <td class="desno"><?= number_format($o->netoIznos, 2, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
      <?php if (empty($obracuni)): ?>
        <tr><td colspan="6" style="text-align:center;">Nema podataka.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>
