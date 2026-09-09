<?php

require_once __DIR__ . '/kontroler/ObracunKontroler.php';

ObracunKontroler::prikaz((int) ($_GET['id'] ?? 0));
