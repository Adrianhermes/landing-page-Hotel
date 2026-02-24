<?php
require_once __DIR__ . '/../config/auth.php';

encerrarSessao();

header('Location: /landing-page-Hotel-principal/views/login.php?m=Voce saiu do sistema');
exit;
