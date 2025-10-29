<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/auth.php';

liberarAcessoReservaPublica();

header('Location: /hotel/views/reservas.php?via=publico');
exit;
