<?php

require_once __DIR__ . '/../tehnoloskeKlase/BaznaEntitetKlasa.php';

// Korisnik – NEZAVISNA klasa/tabela.
class Korisnik extends BaznaEntitetKlasa
{
    public $idKorisnika;
    public $korisnickoIme;
    public $ime;
    public $prezime;

    public function __construct($idKorisnika = null, $korisnickoIme = '', $ime = '', $prezime = '')
    {
        $this->idKorisnika = $idKorisnika;
        $this->korisnickoIme = $korisnickoIme;
        $this->ime = $ime;
        $this->prezime = $prezime;
    }

    // login korisnika
    public static function prijava($korisnickoIme, $lozinka)
    {
        $red = self::izvrsiUpit(
            'SELECT * FROM korisnik WHERE korisnicko_ime = ?',
            [$korisnickoIme]
        )->fetch();

        if (!$red) {
            return null; // korisnik ne postoji
        }
        if (!password_verify($lozinka, $red['lozinka'])) {
            return null; // pogresna lozinka
        }

        return new self((int) $red['id_korisnik'], $red['korisnicko_ime'], $red['ime'], $red['prezime']);
    }
}
