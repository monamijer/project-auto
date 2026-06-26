<?php
/**
 * pages/verify_2fa.php — Vérification du code 2FA
 */
session_start();
require_once __DIR__ . '/../config/database.php';

// Si déjà connecté → dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: /project_auto/index.php');
    exit();
}

// Si pas de 2FA en attente → login
if (!isset($_SESSION['2fa_pending_id'])) {
    header('Location: /project_auto/pages/login.php');
    exit();
}

$error = '';
$username = $_SESSION['2fa_pending_user'];
$codeDisplay = $_SESSION['2fa_code_display'] ?? null;
$minutesRestantes = max(0, ceil(($_SESSION['2fa_expire'] - time()) / 60));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeEntre = trim(str_replace(' ', '', $_POST['otp_code'] ?? ''));

    if (time() > ($_SESSION['2fa_expire'] ?? 0)) {
        unset($_SESSION['2fa_pending_id'], $_SESSION['2fa_pending_user'], $_SESSION['2fa_pending_role'], $_SESSION['2fa_expire'], $_SESSION['2fa_code_display']);
        header('Location: /project_auto/pages/login.php?expired=1');
        exit();
    }

    $stmt = $pdo->prepare("SELECT id FROM otp_codes WHERE utilisateur = ? AND code = ? AND type = '2fa' AND used = 0 AND expire > NOW() ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$username, $codeEntre]);
    $valid = $stmt->fetch();

    if ($valid) {
        $pdo->prepare('UPDATE otp_codes SET used = 1 WHERE id = ?')->execute([$valid['id']]);

        $_SESSION['user_id'] = $_SESSION['2fa_pending_id'];
        $_SESSION['username'] = $_SESSION['2fa_pending_user'];
        $_SESSION['role'] = $_SESSION['2fa_pending_role'];
        $_SESSION['last_activity'] = time();

        unset($_SESSION['2fa_pending_id'], $_SESSION['2fa_pending_user'], $_SESSION['2fa_pending_role'], $_SESSION['2fa_expire'], $_SESSION['2fa_code_display']);

        header('Location: /project_auto/index.php');
        exit();
    } else {
        $error = 'Code incorrect ou expiré.';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA — Auto École Pro</title>
    <link rel="stylesheet" href="/project_auto/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/project_auto/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body{background:#f5f6fa;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem;font-family:sans-serif;}
        .otp-card{border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.09);max-width:440px;width:100%;border:0;background:#fff;}
        .otp-card .card-body{padding:2rem;}
        .otp-inputs{display:flex;gap:.5rem;justify-content:center;margin:1.5rem 0;}
        .otp-inputs input{width:48px;height:56px;text-align:center;font-size:1.4rem;font-weight:700;border-radius:10px;border:2px solid #e5e7eb;outline:none;}
        .otp-inputs input:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.15);}
        .btn-primary{background:#4f46e5;border-color:#4f46e5;border-radius:8px;padding:.7rem;font-weight:500;}
        .btn-primary:disabled{opacity:.4;}
    </style>
</head>
<body>
<div class="otp-card"><div class="card-body">
    <div class="text-center mb-4">
        <div style="width:64px;height:64px;background:#eef2ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="bi bi-shield-lock-fill text-primary fs-3"></i></div>
        <h4 class="fw-bold mb-1">Vérification 2FA</h4>
        <p class="text-muted small mb-0">Bonjour <strong><?= htmlspecialchars($username) ?></strong></p>
        <p class="text-muted small">Entrez le code à 6 chiffres</p>
    </div>
    <?php if ($codeDisplay): ?><div class="alert alert-info text-center py-2 mb-3"><strong>Code : <?= htmlspecialchars(
    $codeDisplay
) ?></strong><br><small class="text-muted">(Envoyé par email en production)</small></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger py-2 text-center"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST" id="otpForm">
        <div class="otp-inputs">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off">
            <input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off">
        </div>
        <input type="hidden" name="otp_code" id="otpHidden">
        <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>Vérifier</button>
    </form>
    <div class="text-center mt-3"><small class="text-muted">Expire dans <span id="countdown"><?= $minutesRestantes ?></span> min</small><br><a href="/project_auto/pages/login.php" class="small text-danger mt-1 d-inline-block">← Se reconnecter</a></div>
</div></div>
<script>
const d=document.querySelectorAll('.otp-digit'),h=document.getElementById('otpHidden'),s=document.getElementById('submitBtn');
d.forEach((i,x)=>{i.addEventListener('input',e=>{const v=e.target.value.replace(/\D/g,'');e.target.value=v;if(v&&x<5)d[x+1].focus();a();});i.addEventListener('keydown',e=>{if(e.key==='Backspace'&&!e.target.value&&x>0)d[x-1].focus();});});
function a(){const c=Array.from(d).map(x=>x.value).join('');h.value=c;s.disabled=c.length!==6;if(c.length===6)document.getElementById('otpForm').submit();}
d[0].focus();
</script>
</body>
</html>