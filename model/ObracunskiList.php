<?php

require_once __DIR__ . '/StavkaObracuna.php';

class ObracunskiList
{
    private $idObracunskogLista;
    private $brojObracuna;
    private $mesec;
    private $godina;
    private $imeZaposlenog;
    private $prezimeZaposlenog;
    private $jmbg;
    private $radnoMesto;
    private $brojRadnihSati;
    private $osnovnaZarada;
    private $ukupnoUvecanje;
    private $ukupnoUmanjenje;
    private $netoIznos;
    private $datumIsplate;


    private $stavke = [];

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



    public function dodajStavku(StavkaObracuna $stavka)
    {
        $stavka->setRedniBroj(count($this->stavke) + 1);
        $this->stavke[] = $stavka;
    }



    public function getIdObracunskogLista()
    {
        return $this->idObracunskogLista;
    }

    public function setIdObracunskogLista($idObracunskogLista)
    {
        $this->idObracunskogLista = $idObracunskogLista;
    }

    public function getBrojObracuna()
    {
        return $this->brojObracuna;
    }

    public function setBrojObracuna($brojObracuna)
    {
        $this->brojObracuna = $brojObracuna;
    }

    public function getMesec()
    {
        return $this->mesec;
    }

    public function setMesec($mesec)
    {
        $this->mesec = $mesec;
    }

    public function getGodina()
    {
        return $this->godina;
    }

    public function setGodina($godina)
    {
        $this->godina = $godina;
    }

    public function getImeZaposlenog()
    {
        return $this->imeZaposlenog;
    }

    public function setImeZaposlenog($imeZaposlenog)
    {
        $this->imeZaposlenog = $imeZaposlenog;
    }

    public function getPrezimeZaposlenog()
    {
        return $this->prezimeZaposlenog;
    }

    public function setPrezimeZaposlenog($prezimeZaposlenog)
    {
        $this->prezimeZaposlenog = $prezimeZaposlenog;
    }

    public function getJmbg()
    {
        return $this->jmbg;
    }

    public function setJmbg($jmbg)
    {
        $this->jmbg = $jmbg;
    }

    public function getRadnoMesto()
    {
        return $this->radnoMesto;
    }

    public function setRadnoMesto($radnoMesto)
    {
        $this->radnoMesto = $radnoMesto;
    }

    public function getBrojRadnihSati()
    {
        return $this->brojRadnihSati;
    }

    public function setBrojRadnihSati($brojRadnihSati)
    {
        $this->brojRadnihSati = $brojRadnihSati;
    }

    public function getOsnovnaZarada()
    {
        return $this->osnovnaZarada;
    }

    public function setOsnovnaZarada($osnovnaZarada)
    {
        $this->osnovnaZarada = $osnovnaZarada;
    }

    public function getUkupnoUvecanje()
    {
        return $this->ukupnoUvecanje;
    }

    public function setUkupnoUvecanje($ukupnoUvecanje)
    {
        $this->ukupnoUvecanje = $ukupnoUvecanje;
    }

    public function getUkupnoUmanjenje()
    {
        return $this->ukupnoUmanjenje;
    }

    public function setUkupnoUmanjenje($ukupnoUmanjenje)
    {
        $this->ukupnoUmanjenje = $ukupnoUmanjenje;
    }

    public function getNetoIznos()
    {
        return $this->netoIznos;
    }

    public function setNetoIznos($netoIznos)
    {
        $this->netoIznos = $netoIznos;
    }

    public function getDatumIsplate()
    {
        return $this->datumIsplate;
    }

    public function setDatumIsplate($datumIsplate)
    {
        $this->datumIsplate = $datumIsplate;
    }


    public function getStavke()
    {
        return $this->stavke;
    }


    public function setStavke(array $stavke)
    {
        $this->stavke = $stavke;
    }
}