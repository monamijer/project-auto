<?php
/**
 * pages/404.php — Page introuvable
 */
http_response_code(404);
$pageTitle = 'Page introuvable — Auto École Pro';

// Charger la config
if (file_exists(__DIR__ . '/../config/database.php')) {
    require_once __DIR__ . '/../config/database.php';
}

// Vérifier si l'utilisateur est connecté
$isLoggedIn = isset($_SESSION['user_id']);

if ($isLoggedIn && file_exists(BASE_PATH . '/includes/header.php')) {
    include BASE_PATH . '/includes/header.php';
} else {
     ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Page introuvable</title>
        <link rel="stylesheet" href="/project_auto/node_modules/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="/project_auto/node_modules/bootstrap-icons/font/bootstrap-icons.css">
        <style>
            body { background: #f5f6fa; }
        </style>
    </head>
    <body>
    <div class="container">
    <?php
}
?>

<div class="text-center py-5">
    <i class="bi bi-emoji-frown display-1 text-muted mb-3 d-block"></i>
    <h1 class="display-3 fw-bold text-muted">404</h1>
    <p class="lead text-muted mb-4">La page que vous recherchez n'existe pas ou a été déplacée.</p>
    <?php if ($isLoggedIn): ?>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-primary btn-lg">
        <i class="bi bi-house me-2"></i>Retour au tableau de bord
    </a>
    <?php else: ?>
    <a href="/project_auto/pages/login.php" class="btn btn-primary btn-lg">
        <i class="bi bi-box-arrow-in-right me-2"></i>Se connecter
    </a>
    <?php endif; ?>
</div>

<?php if ($isLoggedIn && file_exists(BASE_PATH . '/includes/footer.php')) {
    include BASE_PATH . '/includes/footer.php';
} else {
    echo '</div></body></html>';
}
