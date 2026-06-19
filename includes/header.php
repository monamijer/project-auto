<?php
/**
 * includes/header.php — En-tête HTML commun (DRY)
 * Inclut auth.php pour que les fonctions isAdmin()/hasPermission()
 * soient disponibles dans sidebar.php et toutes les pages.
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
    <link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/datatables.net-bs5/css/dataTables.bootstrap5.min.css">
    <link rel="shortcut icon" href="<?=BASE_URL ?>/assets/images/favicon.ico" type="image/x-icon">
    <style>
        body { background-color: #f8f9fa; }

        /* ── Sidebar (desktop) ──────────────────────────────────────── */
        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            position: sticky;
            top: 0;
        }
        .sidebar .brand { padding: 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.15); }
        .sidebar .brand h5  { color: #fff; margin: 0; font-size: 1rem; }
        .sidebar .brand small { color: rgba(255,255,255,.7); font-size: .78rem; }
        .sidebar .nav-link { color: #ecf0f1; padding: .5rem 1rem; border-radius: 6px; font-size: .9rem; }
        .sidebar .nav-link:hover  { background-color: #34495e; color: #fff; }
        .sidebar .nav-link.active { background-color: #1abc9c; color: #fff; }
        .sidebar .badge { font-size: .65rem; }

        .stat-card { border-radius: 10px; padding: 20px; margin-bottom: 20px; color: #fff; }
        .stat-card i { font-size: 2.5rem; opacity: .7; }
        .page-header { display:flex; flex-wrap:wrap; gap:.5rem; justify-content:space-between; align-items:center;
                       padding-top:1rem; padding-bottom:.75rem; margin-bottom:1.5rem;
                       border-bottom:1px solid #dee2e6; }

        /* ── Barre mobile (topbar avec bouton burger) ──────────────────── */
        .mobile-topbar {
            display: none;
            background: #2c3e50;
            color: #fff;
            padding: .75rem 1rem;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1030;
        }
        .mobile-topbar .btn-burger {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 1.4rem;
        }

        /* ── RESPONSIVE : sidebar devient off-canvas sous 768px ───────── */
        @media (max-width: 767.98px) {
            .mobile-topbar { display: flex; }
            .sidebar {
                position: fixed;
                top: 0; left: 0;
                width: 260px;
                height: 100vh;
                z-index: 1040;
                transform: translateX(-100%);
                transition: transform .25s ease-in-out;
                overflow-y: auto;
            }
            .sidebar.show { transform: translateX(0); }
            .sidebar-backdrop {
                display: none;
                position: fixed; inset: 0;
                background: rgba(0,0,0,.45);
                z-index: 1035;
            }
            .sidebar-backdrop.show { display: block; }
            main { padding-top: 1rem; }
            .page-header h1 { font-size: 1.3rem; }
            .table-responsive { font-size: .85rem; }
        }
    </style>
</head>
<body>

<!-- ── Barre mobile (visible uniquement < 768px) ──────────────────────── -->
<div class="mobile-topbar">
    <button class="btn-burger" onclick="document.getElementById('appSidebar').classList.add('show'); document.getElementById('sidebarBackdrop').classList.add('show');">
        <i class="bi bi-list"></i>
    </button>
    <strong><i class="bi bi-car-front-fill"></i> Auto École Pro</strong>
    <span style="width:24px;"></span>
</div>

<!-- ── Fond sombre cliquable pour fermer la sidebar mobile ────────────── -->
<div class="sidebar-backdrop" id="sidebarBackdrop"
     onclick="document.getElementById('appSidebar').classList.remove('show'); this.classList.remove('show');"></div>

<div class="container-fluid">
    <div class="row">
        <?php include BASE_PATH . '/includes/sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
