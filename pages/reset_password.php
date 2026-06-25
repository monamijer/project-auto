<?php
/**
 * pages/reset_password.php — Réinitialiser le mot de passe
 */
session_start();
require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';
$token = $_GET['token'] ?? '';
$validToken = false;
$userId = null;

// Vérifier le token
if (!empty($token)) {
    $stmt = $pdo->prepare("SELECT pr.utilisateur_id, pr.expires_at, eu.utilisateur 
        FROM password_resets pr 
        JOIN expirations_utilisateurs eu ON eu.id = pr.utilisateur_id 
        WHERE pr.token = ? AND pr.used = 0");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();
    
    if ($reset) {
        if (strtotime($reset['expires_at']) > time()) {
            $validToken = true;
            $userId = $reset['utilisateur_id'];
        } else {
            $error = 'Ce lien a expiré. Veuillez faire une nouvelle demande.';
        }
    } else {
        $error = 'Lien invalide ou déjà utilisé.';
    }
}

// Traiter le nouveau mot de passe
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $validToken) {
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    
    if (strlen($password) < 6) {
        $error = 'Le mot de passe doit contenir au moins 6 caractères.';
    } elseif ($password !== $confirm) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        
        // Mettre à jour le mot de passe
        require_once BASE_PATH . '/includes/auth.php';
        $msg = callProcedure("CALL sp_changer_mot_de_passe(?,?,@msg)", [$userId, $hash]);
        
        if ($msg === 'OK') {
            // Marquer le token comme utilisé
            $pdo->prepare("UPDATE password_resets SET used = 1 WHERE token = ?")->execute([$token]);
            $message = 'Mot de passe changé avec succès !';
        } else {
            $error = $msg;
        }
    }
}

$pageTitle = 'Réinitialiser le mot de passe — Auto École Pro';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <style>
        body { background: #f5f6fa; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,.08); max-width: 440px; width: 100%; border: 0; }
        .card-body { padding: 2.5rem; }
    </style>
</head>
<body>
<div class="card">
    <div class="card-body">
        <div class="text-center mb-4">
            <i class="bi bi-key text-success display-4"></i>
            <h4 class="mt-2">Nouveau mot de passe</h4>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
        <div class="text-center">
            <a href="<?= BASE_URL ?>/pages/login.php" class="btn btn-primary">Se connecter</a>
        </div>
        <?php elseif ($validToken): ?>
        <form method="POST">
            <p class="text-muted small">Utilisateur : <strong><?= htmlspecialchars($reset['utilisateur']) ?></strong></p>
            <div class="mb-3">
                <label class="form-label">Nouveau mot de passe</label>
                <input type="password" name="password" class="form-control" required minlength="6" autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Confirmer le mot de passe</label>
                <input type="password" name="confirm" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-success w-100">Changer le mot de passe</button>
        </form>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/pages/login.php">← Retour à la connexion</a>
        </div>
    </div>
</div>
</body>
</html>