<?php
/**
 * Funcoes auxiliares de autenticacao do painel administrativo.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Registra o usuario autenticado na sessao.
 *
 * @param array $usuario Campos minimos: id, email, nome (opcional).
 */
function registrarLogin(array $usuario): void
{
    $_SESSION['usuario_admin'] = [
        'id' => $usuario['id'] ?? null,
        'email' => $usuario['email'] ?? null,
        'nome' => $usuario['nome'] ?? null,
    ];
}

/**
 * Retorna os dados do usuario autenticado ou null.
 */
function usuarioLogado(): ?array
{
    return $_SESSION['usuario_admin'] ?? null;
}

/**
 * Garante que a pagina so seja acessada quando o usuario estiver logado.
 */
function exigirLogin(): void
{
    if (!usuarioLogado()) {
        header('Location: /hotel/views/login.php?e=Faca login para continuar');
        exit;
    }
}

/**
 * Encerra a sessao ativa do usuario.
 */
function encerrarSessao(): void
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }
    session_destroy();
}

/**
 * Permite liberar a tela publica de cadastro de reservas.
 */
function liberarAcessoReservaPublica(): void
{
    $_SESSION['reserva_publica_autorizada'] = time();
}

/**
 * Verifica se o acesso publico esta liberado (com pequena validade).
 */
function acessoReservaPublicaLiberado(): bool
{
    $timestamp = $_SESSION['reserva_publica_autorizada'] ?? null;
    if (!$timestamp) {
        return false;
    }

    $timestamp = (int) $timestamp;
    $janela = 1800; // 30 minutos

    if ($timestamp + $janela < time()) {
        unset($_SESSION['reserva_publica_autorizada']);
        return false;
    }

    // renova a janela de acesso enquanto a pessoa utiliza a pagina
    $_SESSION['reserva_publica_autorizada'] = time();
    return true;
}

/**
 * Exige autenticao ou um acesso publico liberado para a pagina de reservas.
 */
function exigirLoginOuReservaPublica(): void
{
    if (usuarioLogado() || acessoReservaPublicaLiberado()) {
        return;
    }

    header('Location: /hotel/views/login.php?e=Faca login para continuar');
    exit;
}
