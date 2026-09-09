<?php

require_once __DIR__ . '/kontroler/ObracunKontroler.php';

ObracunKontroler::obrisi((int) ($_GET['id'] ?? 0));
