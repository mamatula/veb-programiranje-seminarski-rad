<?php

require_once __DIR__ . '/../tehnoloskeKlase/BaznaEntitetKlasa.php';

//VrstaStavke – ŠIFARNIK.

class VrstaStavke extends BaznaEntitetKlasa
{
    public $idVrsteStavke;
    public $sifra;
    public $naziv;
    public $tip; // uvecanje ili umanjenje

    public function __construct($idVrsteStavke = null, $sifra = '', $naziv = '', $tip = 'uvecanje')
    {
        $this->idVrsteStavke = $idVrsteStavke;
        $this->sifra = $sifra;
        $this->naziv = $naziv;
        $this->tip = $tip;
    }



    
    public static function sve()
    {
        $redovi = self::izvrsiUpit('SELECT * FROM sifarnik_vrsta_stavke ORDER BY sifra')->fetchAll();

        $vrste = [];
        foreach ($redovi as $red) {
            $vrste[] = self::odReda($red);
        }
        return $vrste;
    }

    public static function poId($id)
    {
        $red = self::izvrsiUpit('SELECT * FROM sifarnik_vrsta_stavke WHERE id_vrsta_stavke = ?', [$id])->fetch();
        return $red ? self::odReda($red) : null;
    }

    public static function odReda($red)
    {
        return new self(
            isset($red['id_vrsta_stavke']) ? (int) $red['id_vrsta_stavke'] : null,
            $red['sifra'] ?? '',
            $red['naziv'] ?? ($red['naziv_stavke'] ?? ''),
            $red['tip'] ?? 'uvecanje'
        );
    }
}
