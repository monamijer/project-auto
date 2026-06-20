<?php
/**
 * pages/login.php — Connexion avec verrouillage automatique
 * sp_connexion() + password_verify() + sp_incrementer_tentative()/sp_reset_tentatives()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$error = '';
if (isset($_GET['expired'])) {
    $error = 'Session expirée par inactivité. Veuillez vous reconnecter.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ── 1. Vérifier le verrouillage (tentatives échouées) ─────────────────
    $lockCheck = $pdo->prepare("SELECT tentatives_echouees, verrouille_jusqua FROM expirations_utilisateurs WHERE utilisateur = ?");
    $lockCheck->execute([$username]);
    $lockRow = $lockCheck->fetch();

    if ($lockRow && $lockRow['verrouille_jusqua'] && strtotime($lockRow['verrouille_jusqua']) > time()) {
        $minutes = ceil((strtotime($lockRow['verrouille_jusqua']) - time()) / 60);
        $error = "Compte verrouillé suite à trop de tentatives échouées. Réessayez dans $minutes minute(s).";
    } else {
        try {
            $pdo->prepare("CALL sp_connexion(?, @p_id, @p_role, @p_hash, @p_statut)")->execute([$username]);
            $row = $pdo->query("SELECT @p_id AS id, @p_role AS role, @p_hash AS hash, @p_statut AS statut")->fetch();
        } catch (PDOException $e) {
            $error = 'Erreur système.';
            $row = null;
        }

        if ($row && $row['id'] && $row['statut'] === 'actif') {
            if (password_verify($password, $row['hash'])) {
                callProcedure("CALL sp_reset_tentatives(?,@msg)", [$username]);

                $_SESSION['user_id']       = $row['id'];
                $_SESSION['username']      = $username;
                $_SESSION['role']          = $row['role'];
                $_SESSION['last_activity'] = time();

                callProcedure("CALL sp_journaliser(?,?,?,@msg)", [$username, 'AUTORISÉE', 'Connexion réussie']);
                header('Location: ' . BASE_URL . '/index.php');
                exit();
            } else {
                callProcedure("CALL sp_incrementer_tentative(?,@msg)", [$username]);
                $error = 'Mot de passe incorrect.';
            }
        } elseif ($row && $row['statut'] !== 'actif') {
            $error = 'Compte ' . htmlspecialchars($row['statut']) . '. Contactez un administrateur.';
        } else {
            $error = 'Identifiant introuvable.';
        }
        callProcedure("CALL sp_journaliser(?,?,?,@msg)", [$username, 'REFUSÉE', $error]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Auto École Pro</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body { background:#f5f7fb; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .login-card { border-radius:16px; box-shadow:0 4px 24px rgba(0,0,0,.08); max-width:420px; width:100%; border:0; }
        .login-card .card-body { padding:2.5rem; }
        .form-control { border-radius:8px; padding:.6rem 1rem; }
        .form-control:focus { box-shadow:0 0 0 3px rgba(13,110,253,.15); border-color:#86b7fe; }
        .btn-primary { border-radius:8px; padding:.65rem; font-weight:500; }
    </style>
</head>
<body>
<div class="card login-card">
    <div class="card-body">
        <div class="text-center mb-4">
            <i class="bi bi-car-front-fill text-primary display-4"></i>
            <h4 class="mt-2 mb-1">Auto École Pro</h4>
            <p class="text-muted small">Connectez-vous pour continuer</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <small><?= htmlspecialchars($error) ?></small>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label small fw-medium">Identifiant</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-person text-muted"></i></span>
                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small fw-medium">Mot de passe</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-lock text-muted"></i></span>
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
        <p class="text-center text-muted mt-3 mb-0" style="font-size:.78rem;">
            <i class="bi bi-shield-lock"></i> Le compte se verrouille 15 min après 5 échecs.
        </p>
    </div>
</div>
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
