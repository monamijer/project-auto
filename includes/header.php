<?php
/**
 * includes/header.php — En-tête HTML commun
 */
require_once BASE_PATH . '/includes/auth.php';

if (empty($pageTitle)) { $pageTitle = 'Auto École Pro'; }

header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

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
    <link rel="manifest" href="<?= BASE_URL ?>/manifest.json">
    <meta name="theme-color" content="#4f46e5">
</head>
<body>

<div class="mobile-topbar">
    <button class="btn-burger" onclick="document.getElementById('appSidebar').classList.add('show');document.getElementById('sidebarBackdrop').classList.add('show');">
        <i class="bi bi-list"></i>
    </button>
    <div class="d-flex align-items-center gap-2">
        <button class="theme-toggle-mobile" onclick="toggleTheme()" title="Mode sombre/clair">
            <span></span>
        </button>
        <strong><i class="bi bi-car-front-fill me-1"></i>Auto École Pro</strong>
    </div>
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