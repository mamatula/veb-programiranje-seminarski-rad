<?php

require_once __DIR__ . '/kontroler/ObracunKontroler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ObracunKontroler::obradiUnos();
} else {
    ObracunKontroler::formaUnos();
}
