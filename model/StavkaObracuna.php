<?php

require_once __DIR__ . '/../tehnoloskeKlase/BaznaEntitetKlasa.php';
require_once __DIR__ . '/VrstaStavke.php';

//StavkaObracuna – DEO (detail).

class StavkaObracuna extends BaznaEntitetKlasa
{
    public $idStavke;
    public $idObracunskogLista;
    public $vrstaStavke;   // asocijacija: objekat klase VrstaStavke
    public $redniBroj;
    public $iznos;

    public function __construct($vrstaStavke = null, $redniBroj = 0, $iznos = 0.0, $idObracunskogLista = null, $idStavke = null)
    {
        $this->vrstaStavke = $vrstaStavke ?? new VrstaStavke();
        $this->redniBroj = $redniBroj;
        $this->iznos = $iznos;
        $this->idObracunskogLista = $idObracunskogLista;
        $this->idStavke = $idStavke;
    }




    public function validiraj()
    {
        if (!$this->vrstaStavke->idVrsteStavke) {
            throw new InvalidArgumentException('Vrsta stavke mora biti izabrana iz šifarnika.');
        }
        if ($this->iznos <= 0) {
            throw new InvalidArgumentException('Iznos stavke mora biti veći od 0.');
        }
    }



    public function sacuvaj($idObracunskogLista)
    {
        $this->validiraj();
        $this->idObracunskogLista = $idObracunskogLista;

        self::izvrsiUpit(
            'INSERT INTO stavka_obracuna (id_obracunski_list, id_vrsta_stavke, redni_broj, iznos)
             VALUES (?, ?, ?, ?)',
            [$idObracunskogLista, $this->vrstaStavke->idVrsteStavke, $this->redniBroj, $this->iznos]
        );

        $this->idStavke = self::poslednjiId();
    }




    public static function odReda($red)
    {
        $vrsta = new VrstaStavke(
            null,
            $red['sifra'] ?? '',
            $red['naziv_stavke'] ?? '',
            $red['tip'] ?? 'uvecanje'
        );

        return new self(
            $vrsta,
            (int) ($red['redni_broj'] ?? 0),
            (float) ($red['iznos'] ?? 0),
            isset($red['id_obracunski_list']) ? (int) $red['id_obracunski_list'] : null,
            isset($red['id_stavka']) ? (int) $red['id_stavka'] : null
        );
    }
}
