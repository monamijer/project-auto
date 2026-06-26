<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['2fa_pending_id'])) {
    header('Location: ' . BASE_URL . '/pages/login.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codeEntre = trim(str_replace(' ', '', $_POST['otp_code'] ?? ''));
    $username = $_SESSION['2fa_pending_user'];
    if (time() > ($_SESSION['2fa_expire'] ?? 0)) {
        session_unset();
        header('Location: ' . BASE_URL . '/pages/login.php?expired=1');
        exit();
    }
    $pdo->prepare('CALL sp_verifier_otp(?,?,?,@valide,@msg)')->execute([$username, $codeEntre, '2fa']);
    $r = $pdo->query('SELECT @valide AS valide, @msg AS msg')->fetch();
    if ((int) $r['valide'] === 1) {
        $_SESSION['user_id'] = $_SESSION['2fa_pending_id'];
        $_SESSION['username'] = $_SESSION['2fa_pending_user'];
        $_SESSION['role'] = $_SESSION['2fa_pending_role'];
        $_SESSION['last_activity'] = time();
        unset($_SESSION['2fa_pending_id'], $_SESSION['2fa_pending_user'], $_SESSION['2fa_pending_role'], $_SESSION['2fa_expire'], $_SESSION['2fa_code_display']);
        header('Location: ' . BASE_URL . '/index.php');
        exit();
    } else {
        $error = 'Code incorrect ou expiré.';
    }
}

$codeDisplay = $_SESSION['2fa_code_display'] ?? null;
$minutesRestantes = max(0, ceil(($_SESSION['2fa_expire'] - time()) / 60));
?>
<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification 2FA</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>body{background:#f5f6fa;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1rem;}.otp-card{border-radius:16px;box-shadow:0 4px 24px rgba(0,0,0,.09);max-width:440px;width:100%;border:0;}.otp-inputs{display:flex;gap:.5rem;justify-content:center;margin:1.5rem 0;}.otp-inputs input{width:50px;height:60px;text-align:center;font-size:1.5rem;font-weight:700;border-radius:10px;border:2px solid #e5e7eb;outline:none;}.otp-inputs input:focus{border-color:#4f46e5;box-shadow:0 0 0 3px rgba(79,70,229,.15);}</style>
</head>
<body>
<div class="card otp-card"><div class="card-body p-4">
    <div class="text-center mb-4"><div style="width:64px;height:64px;background:#eef2ff;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;"><i class="bi bi-shield-lock-fill text-primary fs-3"></i></div><h4 class="fw-bold mb-1">Vérification 2FA</h4><p class="text-muted small">Entrez le code reçu par email.</p></div>
    <?php if ($codeDisplay): ?><div class="alert alert-info text-center mb-3">Code : <strong><?= $codeDisplay ?></strong></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
    <form method="POST" id="otpForm"><div class="otp-inputs"><?php for (
        $i = 0;
        $i < 6;
        $i++
    ): ?><input type="text" maxlength="1" inputmode="numeric" pattern="[0-9]" class="otp-digit" autocomplete="off"><?php endfor; ?></div><input type="hidden" name="otp_code" id="otpHidden"><button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>Vérifier</button></form>
    <div class="text-center mt-3"><small class="text-muted">Expire dans <span id="countdown"><?= $minutesRestantes ?></span> min</small><br><a href="<?= BASE_URL ?>/pages/login.php" class="small text-danger mt-1">Se reconnecter</a></div>
</div></div>
<script>
const d=document.querySelectorAll('.otp-digit'),h=document.getElementById('otpHidden'),s=document.getElementById('submitBtn');
d.forEach((i,x)=>{i.addEventListener('input',e=>{const v=e.target.value.replace(/\D/g,'');e.target.value=v;if(v&&x<5)d[x+1].focus();a();});i.addEventListener('keydown',e=>{if(e.key==='Backspace'&&!e.target.value&&x>0)d[x-1].focus();});});
function a(){const c=Array.from(d).map(x=>x.value).join('');h.value=c;s.disabled=c.length!==6;if(c.length===6)document.getElementById('otpForm').submit();}
d[0].focus();
</script>
</body>
</html>