<?php
/**
 * pages/login.php — Connexion avec 2FA (#8)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';

if (isset($_SESSION['user_id'])) { header('Location: ' . BASE_URL . '/index.php'); exit(); }

$error   = ''; $doLog = false; $logMessage = '';

if (isset($_GET['expired'])) { $error = 'Session expirée. Veuillez vous reconnecter.'; }
if (isset($_GET['reset']))   { $error = 'Mot de passe changé. Connectez-vous.'; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $lockStmt = $pdo->prepare("SELECT tentatives_echouees, verrouille_jusqua FROM expirations_utilisateurs WHERE utilisateur = ?");
    $lockStmt->execute([$username]);
    $lockRow = $lockStmt->fetch();

    if ($lockRow && $lockRow['verrouille_jusqua'] && strtotime($lockRow['verrouille_jusqua']) > time()) {
        $minutes = ceil((strtotime($lockRow['verrouille_jusqua']) - time()) / 60);
        $error = "Compte verrouillé. Réessayez dans {$minutes} minute(s).";
    } else {
        try {
            $pdo->prepare("CALL sp_connexion(?, @p_id, @p_role, @p_hash, @p_statut)")->execute([$username]);
            $row = $pdo->query("SELECT @p_id AS id, @p_role AS role, @p_hash AS hash, @p_statut AS statut")->fetch();
        } catch (PDOException $e) { $error = 'Erreur système.'; $row = null; }

        if ($row && $row['id']) {
            if ($row['statut'] !== 'actif') {
                $error = 'Compte ' . htmlspecialchars($row['statut']) . '.';
                $doLog = true; $logMessage = 'Compte ' . $row['statut'];
            } elseif (password_verify($password, $row['hash'])) {
                callProcedure("CALL sp_reset_tentatives(?,@msg)", [$username]);

                // ── 2FA (#8) ─────────────────────────────────────────────
                try { $r2fa = $pdo->query("SELECT valeur FROM config_systeme WHERE cle='2fa_active' LIMIT 1")->fetchColumn(); } catch (Exception $e) { $r2fa = '0'; }

                if ((int)$r2fa === 1) {
                    $pdo->prepare("CALL sp_generer_otp(?,?,@otp_code,@otp_msg)")->execute([$username, '2fa']);
                    $rotp = $pdo->query("SELECT @otp_code AS code, @otp_msg AS msg")->fetch();
                    if (($rotp['msg'] ?? '') === 'OK') {
                        $_SESSION['2fa_pending_id']   = $row['id'];
                        $_SESSION['2fa_pending_user'] = $username;
                        $_SESSION['2fa_pending_role'] = $row['role'];
                        $_SESSION['2fa_expire']       = time() + 600;
                        $_SESSION['2fa_code_display'] = $rotp['code'];
                        header('Location: ' . BASE_URL . '/pages/verify_2fa.php');
                        exit();
                    }
                }

                $_SESSION['user_id'] = $row['id']; $_SESSION['username'] = $username;
                $_SESSION['role'] = $row['role']; $_SESSION['last_activity'] = time();
                callProcedure('CALL sp_journaliser(?,?,?,@msg)', [$username, 'AUTORISÉE', 'Connexion réussie']);
                header('Location: ' . BASE_URL . '/index.php'); exit();
            } else {
                callProcedure("CALL sp_incrementer_tentative(?,@msg)", [$username]);
                $error = 'Mot de passe incorrect.'; $doLog = true; $logMessage = 'Mot de passe incorrect';
                $lockStmt->execute([$username]); $upd = $lockStmt->fetch();
                $restantes = max(0, 5 - (int)($upd['tentatives_echouees'] ?? 0));
                if ($restantes > 0) $error .= " ($restantes tentative(s) restante(s))";
            }
        } else { $error = 'Identifiant introuvable.'; }
        if ($doLog) callProcedure("CALL sp_journaliser(?,?,?,@msg)", [$username, 'REFUSÉE', $logMessage]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Auto École Pro</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body { background:#f5f7fb; min-height:100vh; display:flex; align-items:center; justify-content:center; padding:1rem; }
        .login-card { border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.09); max-width:420px; width:100%; border:0; }
        .login-card .card-body { padding:2.5rem; }
        .form-control { border-radius:8px; padding:.6rem 1rem; }
        .form-control:focus { box-shadow:0 0 0 3px rgba(79,70,229,.15); border-color:#a5b4fc; }
        .btn-primary { border-radius:8px; padding:.7rem; background:#4f46e5; border-color:#4f46e5; font-weight:500; }
        .btn-primary:hover { background:#4338ca; }
        .forgot-link { color:#6b7280; text-decoration:none; font-size:.85rem; }
        .forgot-link:hover { color:#4f46e5; }
    </style>
</head>
<body>
<div class="card login-card"><div class="card-body">
    <div class="text-center mb-4"><i class="bi bi-car-front-fill text-primary display-4"></i><h4 class="mt-2 mb-0 fw-bold">Auto École Pro</h4><p class="text-muted small mt-1">Connectez-vous</p></div>
    <?php if ($error): ?><div class="alert alert-danger d-flex align-items-start py-2 gap-2"><i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i><small><?= htmlspecialchars($error) ?></small></div><?php endif; ?>
    <form method="POST" autocomplete="off">
        <div class="mb-3"><label class="form-label small fw-medium">Identifiant</label><div class="input-group"><span class="input-group-text border-end-0"><i class="bi bi-person text-muted"></i></span><input type="text" name="username" class="form-control border-start-0" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus></div></div>
        <div class="mb-3"><label class="form-label small fw-medium">Mot de passe</label><div class="input-group"><span class="input-group-text border-end-0"><i class="bi bi-lock text-muted"></i></span><input type="password" name="password" id="pwdInput" class="form-control border-start-0 border-end-0" required><button class="btn btn-outline-secondary border-start-0" type="button" onclick="togglePwd()" tabindex="-1"><i class="bi bi-eye" id="eyeIcon"></i></button></div></div>
        <div class="d-flex justify-content-end mb-3"><a href="<?= BASE_URL ?>/pages/forgot_password.php" class="forgot-link"><i class="bi bi-question-circle me-1"></i>Mot de passe oublié ?</a></div>
        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right me-2"></i>Se connecter</button>
    </form>
    <div class="text-center mt-3"><small class="text-muted"><i class="bi bi-shield-lock me-1"></i>Verrouillage après 5 échecs (15 min)</small></div>
</div></div>
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script>function togglePwd(){const i=document.getElementById('pwdInput'),e=document.getElementById('eyeIcon');i.type=i.type==='password'?'text':'password';e.className=i.type==='password'?'bi bi-eye':'bi bi-eye-slash';}</script>
</body>
</html>