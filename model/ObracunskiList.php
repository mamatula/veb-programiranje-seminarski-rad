<?php

require_once __DIR__ . '/../tehnoloskeKlase/BaznaEntitetKlasa.php';
require_once __DIR__ . '/StavkaObracuna.php';

// ObracunskiList – CELINA (master).
class ObracunskiList extends BaznaEntitetKlasa
{
    public $idObracunskogLista;
    public $brojObracuna;
    public $mesec;
    public $godina;
    public $imeZaposlenog;
    public $prezimeZaposlenog;
    public $jmbg;
    public $radnoMesto;
    public $brojRadnihSati;
    public $osnovnaZarada;
    public $ukupnoUvecanje;
    public $ukupnoUmanjenje;
    public $netoIznos;
    public $datumIsplate;


    public $stavke = [];



    public function __construct(
        $idObracunskogLista = null,
        $brojObracuna = '',
        $mesec = 0,
        $godina = 0,
        $imeZaposlenog = '',
        $prezimeZaposlenog = '',
        $jmbg = '',
        $radnoMesto = '',
        $brojRadnihSati = 0,
        $osnovnaZarada = 0.0,
        $datumIsplate = '',
        $ukupnoUvecanje = 0.0,
        $ukupnoUmanjenje = 0.0,
        $netoIznos = 0.0
    ) {
        $this->idObracunskogLista = $idObracunskogLista;
        $this->brojObracuna = $brojObracuna;
        $this->mesec = $mesec;
        $this->godina = $godina;
        $this->imeZaposlenog = $imeZaposlenog;
        $this->prezimeZaposlenog = $prezimeZaposlenog;
        $this->jmbg = $jmbg;
        $this->radnoMesto = $radnoMesto;
        $this->brojRadnihSati = $brojRadnihSati;
        $this->osnovnaZarada = $osnovnaZarada;
        $this->datumIsplate = $datumIsplate;
        $this->ukupnoUvecanje = $ukupnoUvecanje;
        $this->ukupnoUmanjenje = $ukupnoUmanjenje;
        $this->netoIznos = $netoIznos;
    }

    public function dodajStavku($stavka)
    {
        $stavka->redniBroj = count($this->stavke) + 1;
        $this->stavke[] = $stavka;
    }




    private function validirajZajednickaPolja()
    {
        if (trim($this->imeZaposlenog) === '' || trim($this->prezimeZaposlenog) === '') {
            throw new InvalidArgumentException('Ime i prezime zaposlenog su obavezni.');
        }
        if (!preg_match('/^\d{13}$/', $this->jmbg)) {
            throw new InvalidArgumentException('JMBG mora sadržati tačno 13 cifara.');
        }
        if (trim($this->radnoMesto) === '') {
            throw new InvalidArgumentException('Radno mesto je obavezno.');
        }
        if ($this->mesec < 1 || $this->mesec > 12) {
            throw new InvalidArgumentException('Mesec mora biti broj između 1 i 12.');
        }
        if ($this->godina < 2000 || $this->godina > 2100) {
            throw new InvalidArgumentException('Godina nije u ispravnom opsegu.');
        }
        if ($this->brojRadnihSati <= 0) {
            throw new InvalidArgumentException('Broj radnih sati mora biti veći od 0.');
        }
        if ($this->osnovnaZarada <= 0) {
            throw new InvalidArgumentException('Osnovna zarada mora biti veća od 0.');
        }
        if (trim($this->datumIsplate) === '') {
            throw new InvalidArgumentException('Datum isplate je obavezan.');
        }
        if (count($this->stavke) === 0) {
            throw new InvalidArgumentException('Obračunski list mora imati bar jednu stavku.');
        }
    }

 

    
    private function validirajZaUnos()
    {
        $this->validirajZajednickaPolja();

        if (trim($this->brojObracuna) === '') {
            throw new InvalidArgumentException('Broj obračuna je obavezan.');
        }

        $postoji = self::izvrsiUpit(
            'SELECT COUNT(*) AS broj FROM obracunski_list WHERE broj_obracuna = ?',
            [$this->brojObracuna]
        )->fetch();
        if ((int) $postoji['broj'] > 0) {
            throw new InvalidArgumentException("Broj obračuna '{$this->brojObracuna}' već postoji (mora biti jedinstven).");
        }
    }

    



    public function sacuvajSaStavkama()
    {
        $this->validirajZaUnos();

        self::pocniTransakciju();
        try {
            self::izvrsiUpit(
                'INSERT INTO obracunski_list
                    (broj_obracuna, mesec, godina, ime_zaposlenog, prezime_zaposlenog,
                     jmbg, radno_mesto, broj_radnih_sati, osnovna_zarada, datum_isplate)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $this->brojObracuna, $this->mesec, $this->godina,
                    $this->imeZaposlenog, $this->prezimeZaposlenog, $this->jmbg,
                    $this->radnoMesto, $this->brojRadnihSati, $this->osnovnaZarada,
                    $this->datumIsplate,
                ]
            );
            $this->idObracunskogLista = self::poslednjiId();

            foreach ($this->stavke as $stavka) {
                $stavka->sacuvaj($this->idObracunskogLista);
            }

            // Poslovno pravilo (neto = osnovna + uvećanja - umanjenja)
            // računa stored procedura, ne PHP kod:
            self::izvrsiProceduru('sp_izracunaj_ukupno', [$this->idObracunskogLista]);

            self::potvrdiTransakciju();
        } catch (Throwable $e) {
            self::otkaziTransakciju();
            throw $e;
        }

        $this->osveziUkupneIznose();
        return $this->idObracunskogLista;
    }




    
    public function azurirajSaStavkama()
    {
        if (!$this->idObracunskogLista) {
            throw new InvalidArgumentException('Nedostaje ID obračunskog lista koji se menja.');
        }
        $this->validirajZajednickaPolja();

        self::pocniTransakciju();
        try {
            self::izvrsiUpit(
                'UPDATE obracunski_list
                    SET mesec = ?, godina = ?, ime_zaposlenog = ?, prezime_zaposlenog = ?,
                        jmbg = ?, radno_mesto = ?, broj_radnih_sati = ?, osnovna_zarada = ?, datum_isplate = ?
                 WHERE id_obracunski_list = ?',
                [
                    $this->mesec, $this->godina, $this->imeZaposlenog, $this->prezimeZaposlenog,
                    $this->jmbg, $this->radnoMesto, $this->brojRadnihSati, $this->osnovnaZarada,
                    $this->datumIsplate, $this->idObracunskogLista,
                ]
            );

            self::izvrsiUpit('DELETE FROM stavka_obracuna WHERE id_obracunski_list = ?', [$this->idObracunskogLista]);

            foreach ($this->stavke as $stavka) {
                $stavka->sacuvaj($this->idObracunskogLista);
            }

            self::izvrsiProceduru('sp_izracunaj_ukupno', [$this->idObracunskogLista]);

            self::potvrdiTransakciju();
        } catch (Throwable $e) {
            self::otkaziTransakciju();
            throw $e;
        }

        $this->osveziUkupneIznose();
    }





    public function obrisi()
    {
        self::izvrsiUpit('DELETE FROM obracunski_list WHERE id_obracunski_list = ?', [$this->idObracunskogLista]);
    }

    private function osveziUkupneIznose()
    {
        $red = self::izvrsiUpit(
            'SELECT ukupno_uvecanje, ukupno_umanjenje, neto_iznos FROM obracunski_list WHERE id_obracunski_list = ?',
            [$this->idObracunskogLista]
        )->fetch();

        $this->ukupnoUvecanje = (float) $red['ukupno_uvecanje'];
        $this->ukupnoUmanjenje = (float) $red['ukupno_umanjenje'];
        $this->netoIznos = (float) $red['neto_iznos'];
    }




    public static function ucitajSaStavkama($id)
    {
        $rezultati = self::izvrsiProceduruViseRezultata('sp_obracun_sa_stavkama', [$id]);

        if (empty($rezultati[0])) {
            return null;
        }

        $obj = self::odReda($rezultati[0][0]);

        foreach ($rezultati[1] ?? [] as $redStavke) {
            $obj->stavke[] = StavkaObracuna::odReda($redStavke);
        }

        return $obj;
    }




    public static function pretraga($prezime = null, $mesec = null, $godina = null)
    {
        $redovi = self::izvrsiProceduru('sp_pretraga_obracuna', [$prezime, $mesec, $godina]);

        $obracuni = [];
        foreach ($redovi as $red) {
            $obracuni[] = self::odReda($red);
        }
        return $obracuni;
    }

    public static function odReda($red)
    {
        return new self(
            (int) $red['id_obracunski_list'],
            $red['broj_obracuna'],
            (int) $red['mesec'],
            (int) $red['godina'],
            $red['ime_zaposlenog'],
            $red['prezime_zaposlenog'],
            $red['jmbg'] ?? '',
            $red['radno_mesto'],
            (int) ($red['broj_radnih_sati'] ?? 0),
            (float) $red['osnovna_zarada'],
            $red['datum_isplate'],
            (float) $red['ukupno_uvecanje'],
            (float) $red['ukupno_umanjenje'],
            (float) $red['neto_iznos']
        );
    }
}
