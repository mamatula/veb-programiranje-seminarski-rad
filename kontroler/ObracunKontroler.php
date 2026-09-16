<?php

require_once __DIR__ . '/../includes/zastita.php';
require_once __DIR__ . '/../repozitorijum/ObracunskiListRepo.php';
require_once __DIR__ . '/../model/ObracunskiList.php';
require_once __DIR__ . '/../model/StavkaObracuna.php';
require_once __DIR__ . '/../model/VrstaStavke.php';

class ObracunKontroler
{
    private static $nazivMeseca = [
        '', 'Januar', 'Februar', 'Mart', 'April', 'Maj', 'Jun',
        'Jul', 'Avgust', 'Septembar', 'Oktobar', 'Novembar', 'Decembar',
    ];

    public static function pocetna()
    {
        $naslovStranice = 'Početna — Obračun zarada';
        $aktivnaStranica = 'pocetna';
        require __DIR__ . '/../pogledi/pocetna.php';
    }

    public static function lista()
    {
        $prezime = trim($_GET['prezime'] ?? '');
        $mesec = trim($_GET['mesec'] ?? '');
        $godina = trim($_GET['godina'] ?? '');

        $prezimeParam = $prezime !== '' ? $prezime : null;
        $mesecParam = ($mesec !== '' && ctype_digit($mesec)) ? (int) $mesec : null;
        $godinaParam = ($godina !== '' && ctype_digit($godina)) ? (int) $godina : null;

        $obracuni = ObracunskiListRepo::pretraga($prezimeParam, $mesecParam, $godinaParam);

        $poruka = '';
        if (isset($_GET['obrisano'])) {
            $poruka = 'Obračunski list je uspešno obrisan.';
        }
        if (isset($_GET['sacuvano'])) {
            $poruka = 'Obračunski list je uspešno sačuvan.';
        }

        $nazivMeseca = self::$nazivMeseca;

        $parametriFiltera = [];
        if ($prezime !== '') {
            $parametriFiltera['prezime'] = $prezime;
        }
        if ($mesec !== '') {
            $parametriFiltera['mesec'] = $mesec;
        }
        if ($godina !== '') {
            $parametriFiltera['godina'] = $godina;
        }
        $upitStampa = http_build_query($parametriFiltera);

        $naslovStranice = 'Obračuni — Obračun zarada';
        $aktivnaStranica = 'obracuni';
        require __DIR__ . '/../pogledi/obracuni.php';
    }

    public static function formaUnos($greska = '', $uneseno = null, $unesenoStavke = [])
    {
        $vrsteStavki = VrstaStavke::sve();
        $nazivMeseca = self::$nazivMeseca;

        if ($uneseno === null) {
            $uneseno = [
                'broj_obracuna' => '',
                'mesec' => (int) date('n'),
                'godina' => (int) date('Y'),
                'ime' => '',
                'prezime' => '',
                'jmbg' => '',
                'radno_mesto' => '',
                'broj_sati' => '',
                'osnovna' => '',
                'datum_isplate' => date('Y-m-d'),
            ];
        }

        $naslovStranice = 'Novi obračun — Obračun zarada';
        $aktivnaStranica = 'unos';
        require __DIR__ . '/../pogledi/unos.php';
    }

    public static function obradiUnos()
    {
        $uneseno = [
            'broj_obracuna' => trim($_POST['broj_obracuna'] ?? ''),
            'mesec' => (int) ($_POST['mesec'] ?? 0),
            'godina' => (int) ($_POST['godina'] ?? 0),
            'ime' => trim($_POST['ime'] ?? ''),
            'prezime' => trim($_POST['prezime'] ?? ''),
            'jmbg' => trim($_POST['jmbg'] ?? ''),
            'radno_mesto' => trim($_POST['radno_mesto'] ?? ''),
            'broj_sati' => trim($_POST['broj_sati'] ?? ''),
            'osnovna' => trim($_POST['osnovna'] ?? ''),
            'datum_isplate' => trim($_POST['datum_isplate'] ?? ''),
        ];

        $vrsteId = $_POST['stavka_vrsta'] ?? [];
        $iznosi = $_POST['stavka_iznos'] ?? [];
        $unesenoStavke = [];

        try {
            $list = new ObracunskiList(
                null,
                $uneseno['broj_obracuna'],
                $uneseno['mesec'],
                $uneseno['godina'],
                $uneseno['ime'],
                $uneseno['prezime'],
                $uneseno['jmbg'],
                $uneseno['radno_mesto'],
                (int) $uneseno['broj_sati'],
                (float) str_replace(',', '.', $uneseno['osnovna']),
                $uneseno['datum_isplate']
            );

            foreach ($vrsteId as $i => $vId) {
                $vId = (int) $vId;
                $iznosSirovo = $iznosi[$i] ?? '';
                if ($vId <= 0 || trim((string) $iznosSirovo) === '') {
                    continue;
                }
                $vrsta = VrstaStavke::poId($vId);
                if (!$vrsta) {
                    continue;
                }
                $unesenoStavke[] = ['vrsta_id' => $vId, 'iznos' => $iznosSirovo];
                $list->dodajStavku(new StavkaObracuna($vrsta, 0, (float) str_replace(',', '.', $iznosSirovo)));
            }

            $noviId = ObracunskiListRepo::sacuvajSaStavkama($list);
            header('Location: prikaz.php?id=' . $noviId . '&sacuvano=1');
            exit;
        } catch (InvalidArgumentException $e) {
            self::formaUnos($e->getMessage(), $uneseno, $unesenoStavke);
            return;
        } catch (Throwable $e) {
            self::formaUnos('Došlo je do greške prilikom snimanja: ' . $e->getMessage(), $uneseno, $unesenoStavke);
            return;
        }
    }

    public static function prikaz($id)
    {
        $obracun = ObracunskiListRepo::ucitajSaStavkama($id);

        if (!$obracun) {
            header('Location: obracuni.php');
            exit;
        }

        $nazivMeseca = self::$nazivMeseca;
        $naslovStranice = 'Prikaz obračuna — Obračun zarada';
        $aktivnaStranica = 'obracuni';
        require __DIR__ . '/../pogledi/prikaz.php';
    }

    public static function formaIzmena($id, $greska = '', $uneseno = null, $prikazStavke = null)
    {
        $postojeci = ObracunskiListRepo::ucitajSaStavkama($id);

        if (!$postojeci) {
            header('Location: obracuni.php');
            exit;
        }

        $vrsteStavki = VrstaStavke::sve();
        $nazivMeseca = self::$nazivMeseca;

        if ($uneseno === null) {
            $uneseno = [
                'mesec' => $postojeci->getMesec(),
                'godina' => $postojeci->getGodina(),
                'ime' => $postojeci->getImeZaposlenog(),
                'prezime' => $postojeci->getPrezimeZaposlenog(),
                'jmbg' => $postojeci->getJmbg(),
                'radno_mesto' => $postojeci->getRadnoMesto(),
                'broj_sati' => $postojeci->getBrojRadnihSati(),
                'osnovna' => $postojeci->getOsnovnaZarada(),
                'datum_isplate' => $postojeci->getDatumIsplate(),
            ];
        }

        if ($prikazStavke === null) {



            $mapaSifri = [];
            foreach ($vrsteStavki as $v) {
                $mapaSifri[$v->sifra] = $v->idVrsteStavke;
            }

            $prikazStavke = [];
            foreach ($postojeci->getStavke() as $s) {
                $prikazStavke[] = [
                    'vrsta_id' => $mapaSifri[$s->getVrstaStavke()->sifra] ?? null,
                    'iznos' => $s->getIznos(),
                ];
            }
        }

        $naslovStranice = 'Izmena obračuna — Obračun zarada';
        $aktivnaStranica = 'obracuni';
        require __DIR__ . '/../pogledi/izmena.php';
    }

    public static function obradiIzmenu($id)
    {
        $postojeci = ObracunskiListRepo::ucitajSaStavkama($id);

        if (!$postojeci) {
            header('Location: obracuni.php');
            exit;
        }

        $uneseno = [
            'mesec' => (int) ($_POST['mesec'] ?? 0),
            'godina' => (int) ($_POST['godina'] ?? 0),
            'ime' => trim($_POST['ime'] ?? ''),
            'prezime' => trim($_POST['prezime'] ?? ''),
            'jmbg' => trim($_POST['jmbg'] ?? ''),
            'radno_mesto' => trim($_POST['radno_mesto'] ?? ''),
            'broj_sati' => trim($_POST['broj_sati'] ?? ''),
            'osnovna' => trim($_POST['osnovna'] ?? ''),
            'datum_isplate' => trim($_POST['datum_isplate'] ?? ''),
        ];

        $vrsteId = $_POST['stavka_vrsta'] ?? [];
        $iznosi = $_POST['stavka_iznos'] ?? [];
        $prikazStavke = [];

        try {
            $list = new ObracunskiList(
                $id,
                $postojeci->getBrojObracuna(),
                $uneseno['mesec'],
                $uneseno['godina'],
                $uneseno['ime'],
                $uneseno['prezime'],
                $uneseno['jmbg'],
                $uneseno['radno_mesto'],
                (int) $uneseno['broj_sati'],
                (float) str_replace(',', '.', $uneseno['osnovna']),
                $uneseno['datum_isplate']
            );

            foreach ($vrsteId as $i => $vId) {
                $vId = (int) $vId;
                $iznosSirovo = $iznosi[$i] ?? '';
                if ($vId <= 0 || trim((string) $iznosSirovo) === '') {
                    continue;
                }
                $vrsta = VrstaStavke::poId($vId);
                if (!$vrsta) {
                    continue;
                }
                $prikazStavke[] = ['vrsta_id' => $vId, 'iznos' => $iznosSirovo];
                $list->dodajStavku(new StavkaObracuna($vrsta, 0, (float) str_replace(',', '.', $iznosSirovo)));
            }

            ObracunskiListRepo::azurirajSaStavkama($list);
            header('Location: prikaz.php?id=' . $id . '&sacuvano=1');
            exit;
        } catch (InvalidArgumentException $e) {
            self::formaIzmena($id, $e->getMessage(), $uneseno, $prikazStavke);
            return;
        } catch (Throwable $e) {
            self::formaIzmena($id, 'Došlo je do greške prilikom snimanja: ' . $e->getMessage(), $uneseno, $prikazStavke);
            return;
        }
    }

    public static function obrisi($id)
    {
        $obracun = ObracunskiListRepo::ucitajSaStavkama($id);

        if ($obracun) {
            ObracunskiListRepo::obrisi($obracun->getIdObracunskogLista());
        }

        header('Location: obracuni.php?obrisano=1');
        exit;
    }

    public static function stampaSpisak()
    {
        $prezime = trim($_GET['prezime'] ?? '');
        $mesec = trim($_GET['mesec'] ?? '');
        $godina = trim($_GET['godina'] ?? '');

        $obracuni = ObracunskiListRepo::pretraga(
            $prezime !== '' ? $prezime : null,
            ($mesec !== '' && ctype_digit($mesec)) ? (int) $mesec : null,
            ($godina !== '' && ctype_digit($godina)) ? (int) $godina : null
        );

        $imaFilter = $prezime !== '' || $mesec !== '' || $godina !== '';
        $nazivMeseca = self::$nazivMeseca;

        require __DIR__ . '/../pogledi/stampa_spisak.php';
    }

    public static function stampaPojedinacna($id)
    {
        $obracun = ObracunskiListRepo::ucitajSaStavkama($id);

        if (!$obracun) {
            header('Location: obracuni.php');
            exit;
        }

        $nazivMeseca = self::$nazivMeseca;

        require __DIR__ . '/../pogledi/stampa_pojedinacna.php';
    }
}