<?php
require_once __DIR__ . '/../config/auth.php';

encerrarSessao();

header('Location: /hotel/views/login.php?m=Voce saiu do sistema');
exit;
