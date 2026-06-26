<?php
/**
 * pages/presentation.php — Présentation (#48)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$stats = $pdo->query('SELECT * FROM v_dashboard_stats')->fetch();
$pageTitle = 'À propos — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header mb-4"><h1 class="h4 mb-0"><i class="bi bi-info-circle me-2 text-primary"></i>Présentation</h1></div>

<div class="card border-0 shadow-sm mb-4" style="background:linear-gradient(135deg,#4f46e5,#7c3aed);color:#fff;">
    <div class="card-body p-5 text-center">
        <i class="bi bi-car-front-fill" style="font-size:4rem;opacity:.9;"></i>
        <h1 class="fw-bold mt-3 mb-2">Auto École Pro</h1>
        <p class="mb-3 opacity-75">Système de gestion complet pour auto écoles</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <span class="badge bg-white bg-opacity-25 px-3 py-2">v1.0</span>
            <span class="badge bg-white bg-opacity-25 px-3 py-2">PHP 8.2</span>
            <span class="badge bg-white bg-opacity-25 px-3 py-2">MySQL 8</span>
            <span class="badge bg-white bg-opacity-25 px-3 py-2">Bootstrap 5</span>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <?php foreach (
        [
            ['Élèves', 'nb_eleves', 'people-fill', 'primary'],
            ['Moniteurs', 'nb_moniteurs', 'person-badge-fill', 'success'],
            ['Véhicules', 'nb_vehicules_dispos', 'car-front-fill', 'info'],
            ['Recettes', 'total_recettes', 'cash-stack', 'warning'],
        ]
        as [$l, $k, $i, $c]
    ):
        $v = $k === 'total_recettes' ? number_format($stats[$k], 2) . ' $' : $stats[$k] ?? 0; ?>
    <div class="col-md-3"><div class="card border-0 shadow-sm text-center p-3"><i class="bi bi-<?= $i ?> text-<?= $c ?> fs-1 mb-2"></i><h2 class="fw-bold mb-0"><?= $v ?></h2><small class="text-muted"><?= $l ?></small></div></div>
    <?php
    endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <?php foreach (
        [
            ['bi-shield-lock', 'Sécurité', '2FA, verrouillage, chiffrement bcrypt, CSRF', 'danger'],
            ['bi-people', 'Utilisateurs', '6 rôles avec permissions granulaires', 'primary'],
            ['bi-database', 'Base de données', 'Procédures stockées + Views SQL, corbeille', 'success'],
            ['bi-camera-video', 'Communication', 'Chat temps réel + appels WebRTC', 'info'],
            ['bi-file-earmark-pdf', 'Rapports', 'PDF, Excel, graphiques Chart.js', 'warning'],
            ['bi-phone', 'Mobile', 'Responsive + PWA installable', 'secondary'],
        ]
        as [$icon, $titre, $desc, $color]
    ): ?>
    <div class="col-md-4"><div class="card border-0 shadow-sm h-100 p-3"><div class="d-flex align-items-center gap-3 mb-2"><div class="rounded-circle bg-<?= $color ?> bg-opacity-10 d-flex align-items-center justify-content-center" style="width:44px;height:44px;"><i class="bi <?= $icon ?> text-<?= $color ?> fs-5"></i></div><h6 class="fw-bold mb-0"><?= $titre ?></h6></div><p class="text-muted small mb-0"><?= $desc ?></p></div></div>
    <?php endforeach; ?>
</div>

<div class="card border-0 shadow-sm"><div class="card-body text-center py-4">
    <p class="text-muted mb-1">Développé avec ❤️ pour <strong>Auto École Pro</strong></p>
    <p class="text-muted small mb-0">PHP · MySQL · Bootstrap · WebRTC · Node.js · <?= date('Y') ?></p>
</div></div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
