<?php
/**
 * includes/mailer.php — Utilitaire d'envoi d'emails via SMTP Gmail
 * Compatible alwaysdata et XAMPP local
 */

function sendMail(string $to, string $subject, string $body): bool
{
    $config = getConfig('email_ecole') ?: 'noreply@autoecole.pro';
    $nomEcole = getConfig('nom_ecole') ?: 'Auto École Pro';
    $smtpUser = getConfig('smtp_user') ?: '';
    $smtpPass = getConfig('smtp_pass') ?: '';

    $headers = ['MIME-Version: 1.0', 'Content-Type: text/html; charset=UTF-8', 'From: ' . $nomEcole . ' <' . $config . '>', 'Reply-To: ' . $config, 'X-Mailer: PHP/' . phpversion()];

    $htmlBody =
        '<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"></head>' .
        '<body style="font-family:sans-serif;background:#f5f6fa;padding:20px;">' .
        '<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:12px;padding:30px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">' .
        '<div style="text-align:center;margin-bottom:20px;"><h2 style="color:#4f46e5;margin:0;">' .
        htmlspecialchars($nomEcole) .
        '</h2></div>' .
        $body .
        '<hr style="border:0;border-top:1px solid #e5e7eb;margin:20px 0;">' .
        '<p style="color:#9ca3af;font-size:12px;text-align:center;">Cet email a été envoyé automatiquement par ' .
        htmlspecialchars($nomEcole) .
        '.</p>' .
        '</div></body></html>';

    // Si SMTP configuré → utiliser PHPMailer
    if (!empty($smtpUser) && !empty($smtpPass)) {
        return sendViaSMTP($to, $subject, $htmlBody, $config, $nomEcole, $smtpUser, $smtpPass);
    }

    // Fallback : mail() natif (alwaysdata le supporte bien)
    return mail($to, $subject, $htmlBody, implode("\r\n", $headers));
}

/**
 * Envoi via SMTP Gmail avec socket PHP (sans librairie externe)
 */
function sendViaSMTP(string $to, string $subject, string $body, string $from, string $fromName, string $user, string $pass): bool
{
    $host = 'smtp.gmail.com';
    $port = 587;

    $socket = @fsockopen($host, $port, $errno, $errstr, 10);
    if (!$socket) {
        error_log("SMTP connection failed: $errstr ($errno)");
        return false;
    }

    $read = function () use ($socket) {
        $resp = '';
        while ($line = fgets($socket, 512)) {
            $resp .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $resp;
    };

    $send = function ($cmd) use ($socket) {
        fwrite($socket, $cmd . "\r\n");
    };

    // EHLO
    $read();
    $send('EHLO autoecole.pro');
    $read();

    // STARTTLS
    $send('STARTTLS');
    $read();
    stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    $send('EHLO autoecole.pro');
    $read();

    // AUTH LOGIN
    $send('AUTH LOGIN');
    $read();
    $send(base64_encode($user));
    $read();
    $send(base64_encode($pass));
    $authResp = $read();
    if (strpos($authResp, '235') !== 0) {
        fclose($socket);
        return false;
    }

    // MAIL FROM
    $send('MAIL FROM:<' . $from . '>');
    $read();

    // RCPT TO
    $send('RCPT TO:<' . $to . '>');
    $read();

    // DATA
    $send('DATA');
    $read();

    $message =
        'From: ' .
        $fromName .
        ' <' .
        $from .
        ">\r\n" .
        'To: <' .
        $to .
        ">\r\n" .
        'Subject: =?UTF-8?B?' .
        base64_encode($subject) .
        "?=\r\n" .
        "MIME-Version: 1.0\r\n" .
        "Content-Type: text/html; charset=UTF-8\r\n\r\n" .
        $body .
        "\r\n.";

    $send($message);
    $read();

    $send('QUIT');
    fclose($socket);
    return true;
}

function sendPasswordResetEmail(string $toEmail, string $username, string $resetLink): bool
{
    $subject = 'Réinitialisation de votre mot de passe';
    $body =
        '<h3>Réinitialisation de mot de passe</h3>' .
        '<p>Bonjour <strong>' .
        htmlspecialchars($username) .
        '</strong>,</p>' .
        '<p>Vous avez demandé la réinitialisation de votre mot de passe.</p>' .
        '<div style="text-align:center;margin:25px 0;">' .
        '<a href="' .
        $resetLink .
        '" style="background:#4f46e5;color:#fff;padding:12px 30px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-block;">Réinitialiser mon mot de passe</a>' .
        '</div>' .
        '<p style="color:#9ca3af;font-size:13px;">Ce lien expire dans 1 heure.</p>' .
        '<p style="color:#9ca3af;font-size:13px;">Si vous n\'avez pas demandé cette réinitialisation, ignorez cet email.</p>';
    return sendMail($toEmail, $subject, $body);
}

function sendPaymentReceiptEmail(string $toEmail, string $nomComplet, float $montant, string $date, string $mode): bool
{
    $subject = 'Reçu de paiement - ' . number_format($montant, 2) . ' $';
    $body =
        '<h3>Reçu de paiement</h3>' .
        '<p>Bonjour <strong>' .
        htmlspecialchars($nomComplet) .
        '</strong>,</p>' .
        '<p>Nous confirmons la réception de votre paiement :</p>' .
        '<table style="width:100%;border-collapse:collapse;margin:15px 0;">' .
        '<tr><td style="padding:8px;border-bottom:1px solid #e5e7eb;color:#6b7280;">Montant</td><td style="padding:8px;border-bottom:1px solid #e5e7eb;font-weight:600;">' .
        number_format($montant, 2) .
        ' $</td></tr>' .
        '<tr><td style="padding:8px;border-bottom:1px solid #e5e7eb;color:#6b7280;">Date</td><td style="padding:8px;border-bottom:1px solid #e5e7eb;">' .
        $date .
        '</td></tr>' .
        '<tr><td style="padding:8px;border-bottom:1px solid #e5e7eb;color:#6b7280;">Mode</td><td style="padding:8px;border-bottom:1px solid #e5e7eb;">' .
        htmlspecialchars($mode) .
        '</td></tr>' .
        '</table>' .
        '<p style="color:#9ca3af;font-size:13px;">Merci de votre confiance.</p>';
    return sendMail($toEmail, $subject, $body);
}
