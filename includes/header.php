<?php
/**
 * includes/header.php — En-tête HTML commun avec sécurité
 */
require_once BASE_PATH . '/includes/auth.php';

if (empty($pageTitle)) { $pageTitle = 'Auto École Pro'; }

// Security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 1; mode=block');
if (ENVIRONMENT === 'production') {
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
}

$notifCount = 0;
if (isAdmin()) {
    $notifCount = (int) $pdo->query("SELECT COUNT(*) FROM notifications WHERE destinataire='all' AND lu=0")->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
    <link rel="shortcut icon" href="<?= BASE_URL ?>/assets/images/favicon.ico" type="image/x-icon">
</head>
<body>

<div class="mobile-topbar">
    <button class="btn-burger" onclick="document.getElementById('appSidebar').classList.add('show');document.getElementById('sidebarBackdrop').classList.add('show');">
        <i class="bi bi-list"></i>
    </button>
    <strong><i class="bi bi-car-front-fill me-1"></i>Auto École Pro</strong>
    <?php if (isAdmin()): ?>
    <a href="<?= BASE_URL ?>/pages/notifications.php" class="text-white position-relative">
        <i class="bi bi-bell fs-5"></i>
        <?php if ($notifCount > 0): ?><span class="badge bg-danger position-absolute top-0 start-100 translate-middle rounded-pill" style="font-size:.6rem;"><?= $notifCount ?></span><?php endif; ?>
    </a>
    <?php else: ?><span style="width:24px;"></span><?php endif; ?>
</div>

<div class="sidebar-backdrop" id="sidebarBackdrop" onclick="document.getElementById('appSidebar').classList.remove('show');this.classList.remove('show');"></div>

<?php include BASE_PATH . '/includes/sidebar.php'; ?>

<div class="main-content">