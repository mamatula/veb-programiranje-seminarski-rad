<?php

require_once __DIR__ . '/kontroler/ObracunKontroler.php';

ObracunKontroler::stampaPojedinacna((int) ($_GET['id'] ?? 0));
