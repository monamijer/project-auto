<?php
/**
 * includes/header.php — En-tête HTML commun
 */
require_once BASE_PATH . '/includes/auth.php';

if (empty($pageTitle)) { $pageTitle = 'Auto École Pro'; }
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

<!-- Mobile topbar -->
<div class="mobile-topbar">
    <button class="btn-burger" onclick="document.getElementById('appSidebar').classList.add('show'); document.getElementById('sidebarBackdrop').classList.add('show');">
        <i class="bi bi-list"></i>
    </button>
    <strong><i class="bi bi-car-front-fill me-1"></i>Auto École Pro</strong>
    <span style="width:24px;"></span>
</div>

<!-- Backdrop -->
<div class="sidebar-backdrop" id="sidebarBackdrop"
     onclick="document.getElementById('appSidebar').classList.remove('show'); this.classList.remove('show');"></div>

<?php include BASE_PATH . '/includes/sidebar.php'; ?>

<!-- Main content -->
<div class="main-content">