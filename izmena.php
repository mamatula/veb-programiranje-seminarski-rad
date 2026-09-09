<?php

require_once __DIR__ . '/kontroler/ObracunKontroler.php';

$id = (int) ($_GET['id'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ObracunKontroler::obradiIzmenu($id);
} else {
    ObracunKontroler::formaIzmena($id);
}
