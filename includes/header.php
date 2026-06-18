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
        .sidebar { min-height: 100vh; background-color: #2c3e50; }
        .sidebar .brand { padding: 1.2rem 1rem; border-bottom: 1px solid rgba(255,255,255,.15); }
        .sidebar .brand h5  { color: #fff; margin: 0; font-size: 1rem; }
        .sidebar .brand small { color: rgba(255,255,255,.7); font-size: .78rem; }
        .sidebar .nav-link { color: #ecf0f1; padding: .45rem 1rem; border-radius: 6px; font-size: .9rem; }
        .sidebar .nav-link:hover  { background-color: #34495e; color: #fff; }
        .sidebar .nav-link.active { background-color: #1abc9c; color: #fff; }
        .stat-card { border-radius: 10px; padding: 20px; margin-bottom: 20px; color: #fff; }
        .stat-card i { font-size: 2.5rem; opacity: .7; }
        .page-header { display:flex; justify-content:space-between; align-items:center;
                       padding-top:1rem; padding-bottom:.75rem; margin-bottom:1.5rem;
                       border-bottom:1px solid #dee2e6; }
        /* Badge rôle dans la sidebar */
        .sidebar .badge { font-size: .65rem; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <?php include BASE_PATH . '/includes/sidebar.php'; ?>
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
