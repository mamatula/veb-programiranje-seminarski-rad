<?php

require_once __DIR__ . '/kontroler/PrijavaKontroler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    PrijavaKontroler::obradiPrijavu();
} else {
    PrijavaKontroler::prikaziFormu();
}
