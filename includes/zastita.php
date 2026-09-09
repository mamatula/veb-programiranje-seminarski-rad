<?php
//Uključuje se na početku SVAKE stranice koja zahteva prijavljenog korisnika. Ako korisnik nije prijavljen, preusmerava na prijava.php.
require_once __DIR__ . '/sesija.php';

if (!isset($_SESSION['korisnik_id'])) {
    header('Location: prijava.php');
    exit;
}
