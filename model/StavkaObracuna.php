<?php

require_once __DIR__ . '/VrstaStavke.php';

class StavkaObracuna
{
    private $idStavke;
    private $idObracunskogLista;
    private $vrstaStavke;
    private $redniBroj;
    private $iznos;

    public function __construct($vrstaStavke = null, $redniBroj = 0, $iznos = 0.0, $idObracunskogLista = null, $idStavke = null)
    {
        $this->vrstaStavke = $vrstaStavke ?? new VrstaStavke();
        $this->redniBroj = $redniBroj;
        $this->iznos = $iznos;
        $this->idObracunskogLista = $idObracunskogLista;
        $this->idStavke = $idStavke;
    }



    public function getIdStavke()
    {
        return $this->idStavke;
    }

    public function setIdStavke($idStavke)
    {
        $this->idStavke = $idStavke;
    }

    public function getIdObracunskogLista()
    {
        return $this->idObracunskogLista;
    }

    public function setIdObracunskogLista($idObracunskogLista)
    {
        $this->idObracunskogLista = $idObracunskogLista;
    }

    public function getVrstaStavke()
    {
        return $this->vrstaStavke;
    }

    public function setVrstaStavke($vrstaStavke)
    {
        $this->vrstaStavke = $vrstaStavke;
    }

    public function getRedniBroj()
    {
        return $this->redniBroj;
    }

    public function setRedniBroj($redniBroj)
    {
        $this->redniBroj = $redniBroj;
    }

    public function getIznos()
    {
        return $this->iznos;
    }

    public function setIznos($iznos)
    {
        $this->iznos = $iznos;
    }
}