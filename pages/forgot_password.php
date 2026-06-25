<?php
/**
 * pages/forgot_password.php — Mot de passe oublié
 */
session_start();
require_once __DIR__ . '/../config/database.php';

$message = '';
$error = '';
$step = 1; // 1 = formulaire email, 2 = email envoyé

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = 'Veuillez entrer votre identifiant.';
    } else {
        // Chercher l'utilisateur
        $stmt = $pdo->prepare("SELECT id, utilisateur FROM expirations_utilisateurs WHERE utilisateur = ? AND statut = 'actif'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Générer un token
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Supprimer les anciens tokens
            $pdo->prepare('DELETE FROM password_resets WHERE utilisateur_id = ?')->execute([$user['id']]);

            // Insérer le nouveau token
            $pdo->prepare('INSERT INTO password_resets (utilisateur_id, token, expires_at) VALUES (?, ?, ?)')->execute([$user['id'], $token, $expires]);

            // Lien de réinitialisation
            $resetLink = BASE_URL . '/pages/reset_password.php?token=' . $token;

            // En production, envoyer par email. Pour le développement, on affiche le lien.
            $message =
                'Un lien de réinitialisation a été généré.<br><br>
                <strong>Lien de test (développement) :</strong><br>
                <a href="' .
                htmlspecialchars($resetLink) .
                '">' .
                htmlspecialchars($resetLink) .
                '</a>';

            $step = 2;
        } else {
            // Ne pas révéler si l'utilisateur existe ou non (sécurité)
            $message = 'Si cet identifiant existe, un email a été envoyé avec les instructions.';
            $step = 2;
        }
    }
}

$pageTitle = 'Mot de passe oublié — Auto École Pro';
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
            <i class="bi bi-shield-lock text-primary display-4"></i>
            <h4 class="mt-2">Mot de passe oublié</h4>
        </div>
        
        <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
        <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        
        <?php if ($step === 1): ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Votre identifiant</label>
                <input type="text" name="email" class="form-control" required autofocus 
                       placeholder="Entrez votre nom d'utilisateur">
            </div>
            <button type="submit" class="btn btn-primary w-100">Envoyer le lien de réinitialisation</button>
        </form>
        <div class="text-center mt-3">
            <a href="<?= BASE_URL ?>/pages/login.php">← Retour à la connexion</a>
        </div>
        <?php endif; ?>
        
        <?php if ($step === 2): ?>
        <div class="text-center">
            <a href="<?= BASE_URL ?>/pages/login.php" class="btn btn-outline-primary">Retour à la connexion</a>
        </div>
        <?php endif; ?>
    </div>
</div>
</body>
</html>