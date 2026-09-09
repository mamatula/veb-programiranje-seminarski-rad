<?php

require_once __DIR__ . '/includes/sesija.php';

header('Location: ' . (isset($_SESSION['korisnik_id']) ? 'pocetna.php' : 'prijava.php'));
exit;
