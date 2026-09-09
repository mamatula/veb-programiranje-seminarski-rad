<?php

require_once __DIR__ . '/../includes/sesija.php';
require_once __DIR__ . '/../model/Korisnik.php';

class PrijavaKontroler
{
    public static function prikaziFormu()
    {
        if (isset($_SESSION['korisnik_id'])) {
            header('Location: pocetna.php');
            exit;
        }

        $greska = '';
        require __DIR__ . '/../pogledi/prijava.php';
    }

    public static function obradiPrijavu()
    {
        if (isset($_SESSION['korisnik_id'])) {
            header('Location: pocetna.php');
            exit;
        }

        $korisnickoIme = trim($_POST['korisnicko_ime'] ?? '');
        $lozinka = $_POST['lozinka'] ?? '';
        $greska = '';

        if ($korisnickoIme === '' || $lozinka === '') {
            $greska = 'Unesite korisničko ime i lozinku.';
        } else {
            $korisnik = Korisnik::prijava($korisnickoIme, $lozinka);
            if ($korisnik) {
                $_SESSION['korisnik_id'] = $korisnik->idKorisnika;
                $_SESSION['korisnik_ime'] = $korisnik->ime;
                $_SESSION['korisnik_prezime'] = $korisnik->prezime;
                header('Location: pocetna.php');
                exit;
            }
            $greska = 'Pogrešno korisničko ime ili lozinka.';
        }

        require __DIR__ . '/../pogledi/prijava.php';
    }

    public static function odjavi()
    {
        $_SESSION = [];
        session_destroy();
        header('Location: prijava.php');
        exit;
    }
}
