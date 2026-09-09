<!DOCTYPE html>
<html lang="sr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Prijava — Obračun zarada</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="prijava-omot">
  <div class="prijava-kartica">
    <h1>Obračun zarada</h1>
    <div class="podnaslov">Prijava korisnika</div>

    <?php if ($greska): ?>
      <div class="poruka poruka-greska"><?= htmlspecialchars($greska) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="polje">
        <label>Korisničko ime</label>
        <input type="text" name="korisnicko_ime" value="<?= htmlspecialchars($_POST['korisnicko_ime'] ?? '') ?>" autofocus>
      </div>
      <div class="polje" style="margin-bottom:18px;">
        <label>Lozinka</label>
        <input type="password" name="lozinka">
      </div>
      <button type="submit" class="dugme dugme-plavo">Prijavi se</button>
    </form>
  </div>
</div>
</body>
</html>
