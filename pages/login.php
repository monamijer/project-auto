<?php
/**
 * pages/login.php — Connexion
 * Utilise la stored procedure sp_connexion() + password_verify() PHP.
 */
session_start();
require_once __DIR__ . '/../config/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/index.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // ── 1. Appel de la procédure : récupère id, rôle, hash, statut_réel ──
    try {
        $pdo->prepare("CALL sp_connexion(?, @p_id, @p_role, @p_hash, @p_statut)")
            ->execute([$username]);

        $row = $pdo->query("SELECT @p_id AS id, @p_role AS role,
                                   @p_hash AS hash, @p_statut AS statut")->fetch();
    } catch (PDOException $e) {
        $error = 'Erreur système : ' . $e->getMessage();
        $row   = null;
    }

    // ── 2. Vérification du mot de passe côté PHP ──────────────────────────
    if ($row && $row['id'] && $row['statut'] === 'actif') {
        if (password_verify($password, $row['hash'])) {
            // ── Connexion réussie ─────────────────────────────────────────
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role']     = $row['role']; // 'admin' ou 'stagiaire'

            // Journal via procédure stockée (plus de UPDATE brut)
            require_once BASE_PATH . '/includes/auth.php';
            callProcedure("CALL sp_journaliser(?,?,?,@msg)", [$username, 'AUTORISÉE', 'Connexion réussie']);

            header('Location: ' . BASE_URL . '/index.php');
            exit();
        } else {
            $error = 'Mot de passe incorrect.';
        }
    } elseif ($row && $row['statut'] !== 'actif') {
        $error = 'Compte ' . htmlspecialchars($row['statut']) . '. Contactez un administrateur.';
    } else {
        $error = 'Identifiant introuvable.';
    }

    // Journal via procédure stockée (plus de UPDATE brut)
    require_once BASE_PATH . '/includes/auth.php';
    callProcedure("CALL sp_journaliser(?,?,?,@msg)", [$username, 'REFUSÉE', $error]);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — Auto École Pro</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card { border-radius: 15px; box-shadow: 0 10px 40px rgba(0,0,0,.2); max-width: 420px; width: 100%; }
    </style>
</head>
<body>
<div class="card login-card">
    <div class="card-body p-4">
        <h3 class="text-center mb-1">🚗 Auto École Pro</h3>
        <p class="text-center text-muted mb-4">Connectez-vous pour continuer</p>

        <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Identifiant</label>
                <input type="text" name="username" class="form-control"
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required autofocus>
            </div>
            <div class="mb-4">
                <label class="form-label">Mot de passe</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Se connecter</button>
        </form>
    </div>
</div>
<script src="<?= BASE_URL ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
