SET NAMES utf8mb4;

DROP DATABASE IF EXISTS obracun_zarada;
CREATE DATABASE obracun_zarada CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE obracun_zarada;

CREATE TABLE korisnik (
    id_korisnik     INT AUTO_INCREMENT PRIMARY KEY,
    korisnicko_ime  VARCHAR(50) NOT NULL UNIQUE,
    lozinka         VARCHAR(255) NOT NULL,
    ime             VARCHAR(50) NOT NULL,
    prezime         VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE sifarnik_vrsta_stavke (
    id_vrsta_stavke INT AUTO_INCREMENT PRIMARY KEY,
    sifra           VARCHAR(10) NOT NULL UNIQUE,
    naziv           VARCHAR(100) NOT NULL,
    tip             ENUM('uvecanje','umanjenje') NOT NULL
) ENGINE=InnoDB;

CREATE TABLE obracunski_list (
    id_obracunski_list   INT AUTO_INCREMENT PRIMARY KEY,
    broj_obracuna        VARCHAR(20) NOT NULL UNIQUE,
    mesec                TINYINT NOT NULL CHECK (mesec BETWEEN 1 AND 12),
    godina               SMALLINT NOT NULL,
    ime_zaposlenog       VARCHAR(50) NOT NULL,
    prezime_zaposlenog   VARCHAR(50) NOT NULL,
    jmbg                 CHAR(13) NOT NULL,
    radno_mesto          VARCHAR(100) NOT NULL,
    broj_radnih_sati     SMALLINT NOT NULL,
    osnovna_zarada       DECIMAL(12,2) NOT NULL,
    ukupno_uvecanje      DECIMAL(12,2) NOT NULL DEFAULT 0,
    ukupno_umanjenje     DECIMAL(12,2) NOT NULL DEFAULT 0,
    neto_iznos           DECIMAL(12,2) NOT NULL DEFAULT 0,
    datum_isplate        DATE NOT NULL
) ENGINE=InnoDB;

CREATE TABLE stavka_obracuna (
    id_stavka            INT AUTO_INCREMENT PRIMARY KEY,
    id_obracunski_list   INT NOT NULL,
    id_vrsta_stavke      INT NOT NULL,
    redni_broj           SMALLINT NOT NULL,
    iznos                DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_stavka_obracun
        FOREIGN KEY (id_obracunski_list) REFERENCES obracunski_list(id_obracunski_list)
        ON DELETE CASCADE,
    CONSTRAINT fk_stavka_sifarnik
        FOREIGN KEY (id_vrsta_stavke) REFERENCES sifarnik_vrsta_stavke(id_vrsta_stavke)
) ENGINE=InnoDB;

CREATE VIEW pogled_obracuni AS
SELECT
    ol.id_obracunski_list,
    ol.broj_obracuna,
    ol.mesec,
    ol.godina,
    ol.ime_zaposlenog,
    ol.prezime_zaposlenog,
    ol.radno_mesto,
    ol.osnovna_zarada,
    ol.ukupno_uvecanje,
    ol.ukupno_umanjenje,
    ol.neto_iznos,
    ol.datum_isplate
FROM obracunski_list ol;

CREATE VIEW pogled_stavke_obracuna AS
SELECT
    so.id_stavka,
    so.id_obracunski_list,
    so.redni_broj,
    sv.sifra,
    sv.naziv        AS naziv_stavke,
    sv.tip,
    so.iznos
FROM stavka_obracuna so
JOIN sifarnik_vrsta_stavke sv ON sv.id_vrsta_stavke = so.id_vrsta_stavke;

DELIMITER //

CREATE PROCEDURE sp_izracunaj_ukupno(IN p_id_obracunski_list INT)
BEGIN
    DECLARE v_uvecanje  DECIMAL(12,2);
    DECLARE v_umanjenje DECIMAL(12,2);
    DECLARE v_osnovna   DECIMAL(12,2);

    SELECT COALESCE(SUM(so.iznos), 0) INTO v_uvecanje
    FROM stavka_obracuna so
    JOIN sifarnik_vrsta_stavke sv ON sv.id_vrsta_stavke = so.id_vrsta_stavke
    WHERE so.id_obracunski_list = p_id_obracunski_list AND sv.tip = 'uvecanje';

    SELECT COALESCE(SUM(so.iznos), 0) INTO v_umanjenje
    FROM stavka_obracuna so
    JOIN sifarnik_vrsta_stavke sv ON sv.id_vrsta_stavke = so.id_vrsta_stavke
    WHERE so.id_obracunski_list = p_id_obracunski_list AND sv.tip = 'umanjenje';

    SELECT osnovna_zarada INTO v_osnovna
    FROM obracunski_list WHERE id_obracunski_list = p_id_obracunski_list;

    UPDATE obracunski_list
    SET ukupno_uvecanje  = v_uvecanje,
        ukupno_umanjenje = v_umanjenje,
        neto_iznos        = v_osnovna + v_uvecanje - v_umanjenje
    WHERE id_obracunski_list = p_id_obracunski_list;
END //

CREATE PROCEDURE sp_pretraga_obracuna(
    IN p_prezime VARCHAR(50),
    IN p_mesec   TINYINT,
    IN p_godina  SMALLINT
)
BEGIN
    SELECT * FROM pogled_obracuni
    WHERE (p_prezime IS NULL OR p_prezime = '' OR prezime_zaposlenog LIKE CONCAT('%', p_prezime, '%'))
      AND (p_mesec IS NULL OR mesec = p_mesec)
      AND (p_godina IS NULL OR godina = p_godina)
    ORDER BY godina DESC, mesec DESC, prezime_zaposlenog;
END //

CREATE PROCEDURE sp_obracun_sa_stavkama(IN p_id_obracunski_list INT)
BEGIN
    SELECT * FROM obracunski_list WHERE id_obracunski_list = p_id_obracunski_list;

    SELECT * FROM pogled_stavke_obracuna
    WHERE id_obracunski_list = p_id_obracunski_list
    ORDER BY redni_broj;
END //

DELIMITER ;

INSERT INTO sifarnik_vrsta_stavke (sifra, naziv, tip) VALUES
    ('D01', 'Prekovremeni rad', 'uvecanje'),
    ('D02', 'Topli obrok', 'uvecanje'),
    ('D03', 'Prevoz', 'uvecanje'),
    ('D04', 'Stimulacija', 'uvecanje'),
    ('O01', 'Porez na zarade', 'umanjenje'),
    ('O02', 'Doprinosi PIO', 'umanjenje'),
    ('O03', 'Doprinosi za zdravstveno osiguranje', 'umanjenje'),
    ('O04', 'Članarina sindikatu', 'umanjenje');

INSERT INTO korisnik (korisnicko_ime, lozinka, ime, prezime) VALUES
    ('pavle', '$2y$12$tGuJjuUBI1GjCnvOev9TGORrOLQmy1/Lmm0WHO7JQ7mvX2MbFS3y6', 'Pavle', 'Stanulov');

START TRANSACTION;

INSERT INTO obracunski_list
    (broj_obracuna, mesec, godina, ime_zaposlenog, prezime_zaposlenog, jmbg, radno_mesto, broj_radnih_sati, osnovna_zarada, datum_isplate)
VALUES
    ('OL-2026-034', 8, 2026, 'Marko', 'Petrović', '0101990123456', 'Programer', 176, 90000.00, '2026-09-05');

SET @id_ol = LAST_INSERT_ID();

INSERT INTO stavka_obracuna (id_obracunski_list, id_vrsta_stavke, redni_broj, iznos) VALUES
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='D01'), 1, 4500.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='D02'), 2, 6000.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='D03'), 3, 3500.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='D04'), 4, 5000.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='O01'), 5, 9945.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='O02'), 6, 10900.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='O03'), 7, 5688.00),
    (@id_ol, (SELECT id_vrsta_stavke FROM sifarnik_vrsta_stavke WHERE sifra='O04'), 8, 800.00);

CALL sp_izracunaj_ukupno(@id_ol);

COMMIT;

