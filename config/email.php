<?php
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';

/**
 * Retorna configuracoes de SMTP utilizadas pelo PHPMailer.
 *
 * Ajuste os valores conforme o provedor utilizado.
 *
 * @return array{
 *   host: string,
 *   username: string,
 *   password: string,
 *   port: int,
 *   encryption: string|null,
 *   from_email: string,
 *   from_name: string,
 *   reply_to?: string
 * }
 */
function obterConfiguracaoSmtp(): array
{
    return [
        'host' => 'smtp.gmail.com',
        'username' => 'seu email',
        'password' => 'sua senha',
        'port' => 000,
        'encryption' => PHPMailer::ENCRYPTION_STARTTLS,
        'from_email' => 'seu email',
        'from_name' => 'seu nome',
        'reply_to' => 'seu email',
    ];
}

/**
 * Envia um e-mail de confirmacao de reserva para o cliente.
 *
 * @param array $dadosReserva Chaves esperadas: nome_cliente, email, numero_quarto (opcional),
 *                            tipo_quarto (opcional), data_checkin, data_checkout, status.
 * @return bool true em caso de sucesso, false caso contrario.
 */
function enviarEmailConfirmacaoReserva(array $dadosReserva): bool
{
    if (empty($dadosReserva['email']) || !filter_var($dadosReserva['email'], FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $nomeCliente   = $dadosReserva['nome_cliente'] ?? 'Cliente';
    $emailCliente  = $dadosReserva['email'];
    $numeroQuarto  = $dadosReserva['numero_quarto'] ?? null;
    $tipoQuarto    = $dadosReserva['tipo_quarto'] ?? null;
    $dataCheckin   = $dadosReserva['data_checkin'] ?? '';
    $dataCheckout  = $dadosReserva['data_checkout'] ?? '';
    $status        = $dadosReserva['status'] ?? 'confirmada';

    $descricaoQuarto = '';
    if ($numeroQuarto !== null || $tipoQuarto !== null) {
        $descricaoPartes = [];
        if ($numeroQuarto !== null) {
            $descricaoPartes[] = 'numero ' . $numeroQuarto;
        }
        if ($tipoQuarto !== null) {
            $descricaoPartes[] = $tipoQuarto;
        }
        $descricaoQuarto = implode(' - ', $descricaoPartes);
    }

    $assunto = 'Confirmacao de Reserva - Hotel';

    $linhas = [];
    $linhas[] = "Ola, {$nomeCliente}!";
    $linhas[] = '';
    $linhas[] = 'Sua reserva foi registrada com sucesso em nosso sistema.';
    if ($descricaoQuarto !== '') {
        $linhas[] = "Quarto: {$descricaoQuarto}";
    }
    if ($dataCheckin !== '') {
        $linhas[] = 'Check-in: ' . date('d/m/Y', strtotime($dataCheckin));
    }
    if ($dataCheckout !== '') {
        $linhas[] = 'Check-out: ' . date('d/m/Y', strtotime($dataCheckout));
    }
    $linhas[] = 'Status da reserva: ' . ucfirst($status);
    $linhas[] = '';
    $linhas[] = 'Em breve nossa equipe entrara em contato para maiores informacoes.';
    $linhas[] = '';
    $linhas[] = 'Atenciosamente,';
    $linhas[] = 'Equipe de Reservas';

    $mensagem = implode("\r\n", $linhas);

    $config = obterConfiguracaoSmtp();

    $mailer = new PHPMailer(true);

    try {
        $mailer->isSMTP();
        $mailer->Host       = $config['host'];
        $mailer->Port       = (int) $config['port'];
        $mailer->SMTPAuth   = true;
        $mailer->Username   = $config['username'];
        $mailer->Password   = $config['password'];
        $mailer->CharSet    = 'UTF-8';
        $mailer->Encoding   = PHPMailer::ENCODING_BASE64;
        $mailer->Timeout    = 20;
        $mailer->SMTPDebug  = SMTP::DEBUG_OFF;

        if (!empty($config['encryption'])) {
            $mailer->SMTPSecure = $config['encryption'];
        }

        if (!empty($config['from_email'])) {
            $mailer->setFrom($config['from_email'], $config['from_name'] ?? '');
        } else {
            $mailer->setFrom($config['username'], $config['from_name'] ?? '');
        }

        if (!empty($config['reply_to'])) {
            $mailer->addReplyTo($config['reply_to'], $config['from_name'] ?? '');
        }

        $mailer->addAddress($emailCliente, $nomeCliente);

        $mailer->Subject = $assunto;
        $mailer->Body    = $mensagem;
        $mailer->AltBody = strip_tags(str_replace("\r\n", "\n", $mensagem));

        $mailer->send();
        return true;
    } catch (MailerException $e) {
        error_log(sprintf(
            '[reservas] Falha ao enviar e-mail via SMTP (%s:%s) para %s. Erro: %s',
            $config['host'],
            $config['port'],
            $emailCliente,
            $e->getMessage()
        ));
        return false;
    }
}

