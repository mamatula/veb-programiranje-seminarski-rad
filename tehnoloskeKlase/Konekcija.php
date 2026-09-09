<?php

class Konekcija
{
    private static $host = 'localhost';
    private static $baza = 'obracun_zarada';
    private static $korisnickoIme = 'root';
    private static $lozinka = '';

    private static $instanca = null;


    protected static function poveziSe()
    {
        if (self::$instanca === null) {
            $dsn = 'mysql:host=' . self::$host . ';dbname=' . self::$baza . ';charset=utf8mb4';

            try {
                self::$instanca = new PDO($dsn, self::$korisnickoIme, self::$lozinka, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);
            } catch (PDOException $e) {
                throw new RuntimeException('Greška pri povezivanju na bazu podataka: ' . $e->getMessage());
            }
        }

        return self::$instanca;
    }
}
