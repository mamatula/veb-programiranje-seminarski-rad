<?php

require_once __DIR__ . '/Konekcija.php';


abstract class BaznaEntitetKlasa extends Konekcija
{

    protected static function izvrsiUpit($sql, $parametri = [])
    {
        $konekcija = self::poveziSe();
        $iskaz = $konekcija->prepare($sql);
        $iskaz->execute($parametri);
        return $iskaz;
    }



    protected static function izvrsiProceduru($nazivProcedure, $parametri = [])
    {
        $konekcija = self::poveziSe();
        $upitnici = implode(',', array_fill(0, count($parametri), '?'));
        $iskaz = $konekcija->prepare("CALL {$nazivProcedure}({$upitnici})");
        $iskaz->execute($parametri);
        $rezultat = $iskaz->fetchAll();
        $iskaz->closeCursor();
        return $rezultat;
    }



    protected static function izvrsiProceduruViseRezultata($nazivProcedure, $parametri = [])
    {
        $konekcija = self::poveziSe();
        $upitnici = implode(',', array_fill(0, count($parametri), '?'));
        $iskaz = $konekcija->prepare("CALL {$nazivProcedure}({$upitnici})");
        $iskaz->execute($parametri);

        $sviRezultati = [];
        do {
            $sviRezultati[] = $iskaz->fetchAll();
        } while ($iskaz->nextRowset());
        $iskaz->closeCursor();

        return $sviRezultati;
    }

  
    protected static function poslednjiId()
    {
        return (int) self::poveziSe()->lastInsertId();
    }

    protected static function pocniTransakciju()
    {
        self::poveziSe()->beginTransaction();
    }

    protected static function potvrdiTransakciju()
    {
        self::poveziSe()->commit();
    }

    protected static function otkaziTransakciju()
    {
        self::poveziSe()->rollBack();
    }
}
