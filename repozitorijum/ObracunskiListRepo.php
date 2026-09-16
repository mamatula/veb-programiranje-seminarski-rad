<?php

require_once __DIR__ . '/../tehnoloskeKlase/BaznaEntitetKlasa.php';
require_once __DIR__ . '/../model/ObracunskiList.php';
require_once __DIR__ . '/../model/StavkaObracuna.php';
require_once __DIR__ . '/../model/VrstaStavke.php';

class ObracunskiListRepo extends BaznaEntitetKlasa
{




    private static function validirajZajednickaPolja(ObracunskiList $list)
    {
        if (trim($list->getImeZaposlenog()) === '' || trim($list->getPrezimeZaposlenog()) === '') {
            throw new InvalidArgumentException('Ime i prezime zaposlenog su obavezni.');
        }
        if (!preg_match('/^\d{13}$/', $list->getJmbg())) {
            throw new InvalidArgumentException('JMBG mora sadržati tačno 13 cifara.');
        }
        if (trim($list->getRadnoMesto()) === '') {
            throw new InvalidArgumentException('Radno mesto je obavezno.');
        }
        if ($list->getMesec() < 1 || $list->getMesec() > 12) {
            throw new InvalidArgumentException('Mesec mora biti broj između 1 i 12.');
        }
        if ($list->getGodina() < 2000 || $list->getGodina() > 2100) {
            throw new InvalidArgumentException('Godina nije u ispravnom opsegu.');
        }
        if ($list->getBrojRadnihSati() <= 0) {
            throw new InvalidArgumentException('Broj radnih sati mora biti veći od 0.');
        }
        if ($list->getOsnovnaZarada() <= 0) {
            throw new InvalidArgumentException('Osnovna zarada mora biti veća od 0.');
        }
        if (trim($list->getDatumIsplate()) === '') {
            throw new InvalidArgumentException('Datum isplate je obavezan.');
        }
        if (count($list->getStavke()) === 0) {
            throw new InvalidArgumentException('Obračunski list mora imati bar jednu stavku.');
        }
    }



    private static function validirajZaUnos(ObracunskiList $list)
    {
        self::validirajZajednickaPolja($list);

        if (trim($list->getBrojObracuna()) === '') {
            throw new InvalidArgumentException('Broj obračuna je obavezan.');
        }

        $postoji = self::izvrsiUpit(
            'SELECT COUNT(*) AS broj FROM obracunski_list WHERE broj_obracuna = ?',
            [$list->getBrojObracuna()]
        )->fetch();
        if ((int) $postoji['broj'] > 0) {
            throw new InvalidArgumentException("Broj obračuna '{$list->getBrojObracuna()}' već postoji (mora biti jedinstven).");
        }
    }



    private static function validirajStavku(StavkaObracuna $stavka)
    {
        if (!$stavka->getVrstaStavke()->idVrsteStavke) {
            throw new InvalidArgumentException('Vrsta stavke mora biti izabrana iz šifarnika.');
        }
        if ($stavka->getIznos() <= 0) {
            throw new InvalidArgumentException('Iznos stavke mora biti veći od 0.');
        }
    }





    private static function sacuvajStavku(StavkaObracuna $stavka, $idObracunskogLista)
    {
        self::validirajStavku($stavka);
        $stavka->setIdObracunskogLista($idObracunskogLista);

        self::izvrsiUpit(
            'INSERT INTO stavka_obracuna (id_obracunski_list, id_vrsta_stavke, redni_broj, iznos)
             VALUES (?, ?, ?, ?)',
            [$idObracunskogLista, $stavka->getVrstaStavke()->idVrsteStavke, $stavka->getRedniBroj(), $stavka->getIznos()]
        );

        $stavka->setIdStavke(self::poslednjiId());
    }



    public static function sacuvajSaStavkama(ObracunskiList $list)
    {
        self::validirajZaUnos($list);

        self::pocniTransakciju();
        try {
            self::izvrsiUpit(
                'INSERT INTO obracunski_list
                    (broj_obracuna, mesec, godina, ime_zaposlenog, prezime_zaposlenog,
                     jmbg, radno_mesto, broj_radnih_sati, osnovna_zarada, datum_isplate)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    $list->getBrojObracuna(), $list->getMesec(), $list->getGodina(),
                    $list->getImeZaposlenog(), $list->getPrezimeZaposlenog(), $list->getJmbg(),
                    $list->getRadnoMesto(), $list->getBrojRadnihSati(), $list->getOsnovnaZarada(),
                    $list->getDatumIsplate(),
                ]
            );
            $list->setIdObracunskogLista(self::poslednjiId());

            foreach ($list->getStavke() as $stavka) {
                self::sacuvajStavku($stavka, $list->getIdObracunskogLista());
            }



            self::izvrsiProceduru('sp_izracunaj_ukupno', [$list->getIdObracunskogLista()]);

            self::potvrdiTransakciju();
        } catch (Throwable $e) {
            self::otkaziTransakciju();
            throw $e;
        }

        self::osveziUkupneIznose($list);
        return $list->getIdObracunskogLista();
    }



    public static function azurirajSaStavkama(ObracunskiList $list)
    {
        if (!$list->getIdObracunskogLista()) {
            throw new InvalidArgumentException('Nedostaje ID obračunskog lista koji se menja.');
        }
        self::validirajZajednickaPolja($list);

        self::pocniTransakciju();
        try {
            self::izvrsiUpit(
                'UPDATE obracunski_list
                    SET mesec = ?, godina = ?, ime_zaposlenog = ?, prezime_zaposlenog = ?,
                        jmbg = ?, radno_mesto = ?, broj_radnih_sati = ?, osnovna_zarada = ?, datum_isplate = ?
                 WHERE id_obracunski_list = ?',
                [
                    $list->getMesec(), $list->getGodina(), $list->getImeZaposlenog(), $list->getPrezimeZaposlenog(),
                    $list->getJmbg(), $list->getRadnoMesto(), $list->getBrojRadnihSati(), $list->getOsnovnaZarada(),
                    $list->getDatumIsplate(), $list->getIdObracunskogLista(),
                ]
            );

            self::izvrsiUpit('DELETE FROM stavka_obracuna WHERE id_obracunski_list = ?', [$list->getIdObracunskogLista()]);

            foreach ($list->getStavke() as $stavka) {
                self::sacuvajStavku($stavka, $list->getIdObracunskogLista());
            }

            self::izvrsiProceduru('sp_izracunaj_ukupno', [$list->getIdObracunskogLista()]);

            self::potvrdiTransakciju();
        } catch (Throwable $e) {
            self::otkaziTransakciju();
            throw $e;
        }

        self::osveziUkupneIznose($list);
    }



    public static function obrisi($idObracunskogLista)
    {
        self::izvrsiUpit('DELETE FROM obracunski_list WHERE id_obracunski_list = ?', [$idObracunskogLista]);
    }

    private static function osveziUkupneIznose(ObracunskiList $list)
    {
        $red = self::izvrsiUpit(
            'SELECT ukupno_uvecanje, ukupno_umanjenje, neto_iznos FROM obracunski_list WHERE id_obracunski_list = ?',
            [$list->getIdObracunskogLista()]
        )->fetch();

        $list->setUkupnoUvecanje((float) $red['ukupno_uvecanje']);
        $list->setUkupnoUmanjenje((float) $red['ukupno_umanjenje']);
        $list->setNetoIznos((float) $red['neto_iznos']);
    }





    public static function ucitajSaStavkama($id)
    {
        $rezultati = self::izvrsiProceduruViseRezultata('sp_obracun_sa_stavkama', [$id]);

        if (empty($rezultati[0])) {
            return null;
        }

        $obj = self::odReda($rezultati[0][0]);

        $stavke = [];
        foreach ($rezultati[1] ?? [] as $redStavke) {
            $stavke[] = self::stavkaOdReda($redStavke);
        }
        $obj->setStavke($stavke);

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



    private static function odReda($red)
    {
        return new ObracunskiList(
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



    private static function stavkaOdReda($red)
    {
        $vrsta = new VrstaStavke(
            null,
            $red['sifra'] ?? '',
            $red['naziv_stavke'] ?? '',
            $red['tip'] ?? 'uvecanje'
        );

        return new StavkaObracuna(
            $vrsta,
            (int) ($red['redni_broj'] ?? 0),
            (float) ($red['iznos'] ?? 0),
            isset($red['id_obracunski_list']) ? (int) $red['id_obracunski_list'] : null,
            isset($red['id_stavka']) ? (int) $red['id_stavka'] : null
        );
    }
}