<?php
/**
 * index.php — Tableau de bord
 * SELECT → v_dashboard_stats, v_derniers_paiements, v_prochaines_lecons
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

// ── Statistiques via la VIEW v_dashboard_stats ────────────────────────────
$stats = $pdo->query("SELECT * FROM v_dashboard_stats")->fetch();

// ── Derniers paiements via v_derniers_paiements ───────────────────────────
$recentPayments = $pdo->query("SELECT * FROM v_derniers_paiements")->fetchAll();

// ── Prochaines leçons via v_prochaines_lecons ─────────────────────────────
$upcomingLessons = $pdo->query("SELECT * FROM v_prochaines_lecons")->fetchAll();

$pageTitle = 'Tableau de bord — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2">Tableau de bord</h1>
    <span class="text-muted">
        Bienvenue, <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong>
        <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-secondary' ?> ms-1">
            <?= isAdmin() ? 'Admin' : 'Stagiaire' ?>
        </span>
    </span>
</div>

<!-- ── Cartes statistiques ─────────────────────────────────────────────── -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#667eea,#764ba2);">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="mb-0">Élèves</h6><h2 class="mb-0"><?= $stats['nb_eleves'] ?></h2></div>
                <i class="bi bi-people-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#f093fb,#f5576c);">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="mb-0">Moniteurs</h6><h2 class="mb-0"><?= $stats['nb_moniteurs'] ?></h2></div>
                <i class="bi bi-person-badge-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#4facfe,#00f2fe);">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="mb-0">Véhicules dispo</h6><h2 class="mb-0"><?= $stats['nb_vehicules_dispos'] ?></h2></div>
                <i class="bi bi-car-front-fill"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#43e97b,#38f9d7);">
            <div class="d-flex justify-content-between align-items-center">
                <div><h6 class="mb-0">Leçons programmées</h6><h2 class="mb-0"><?= $stats['nb_lecons_programmees'] ?></h2></div>
                <i class="bi bi-calendar-check-fill"></i>
            </div>
        </div>
    </div>
</div>

<!-- ── Recettes + tableaux ─────────────────────────────────────────────── -->
<div class="row mt-3">
    <div class="col-12 mb-3">
        <div class="card bg-dark text-white">
            <div class="card-body py-2 d-flex align-items-center gap-3">
                <i class="bi bi-graph-up-arrow fs-4"></i>
                <div>Total recettes : <strong><?= number_format($stats['total_recettes'], 2) ?> $</strong></div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-2">
    <!-- Derniers paiements -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Derniers paiements</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Élève</th><th>Montant</th><th>Mode</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentPayments as $r): ?>
                    <tr>
                        <td><?= htmlspecialchars($r['date_paiement']) ?></td>
                        <td><?= htmlspecialchars($r['student_nom']) ?></td>
                        <td><?= number_format($r['montant'], 2) ?> $</td>
                        <td><?= htmlspecialchars($r['methode']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentPayments)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Aucun paiement</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Prochaines leçons -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Prochaines leçons</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>Date</th><th>Élève</th><th>Moniteur</th><th>Véhicule</th></tr></thead>
                    <tbody>
                    <?php foreach ($upcomingLessons as $r): ?>
                    <tr>
                        <td><?= date('d/m H:i', strtotime($r['date_lecon'])) ?></td>
                        <td><?= htmlspecialchars($r['student_nom']) ?></td>
                        <td><?= htmlspecialchars($r['instructor_nom']) ?></td>
                        <td><?= htmlspecialchars($r['vehicle_nom']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($upcomingLessons)): ?>
                    <tr><td colspan="4" class="text-center text-muted py-3">Aucune leçon à venir</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ── Actions rapides ─────────────────────────────────────────────────── -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Actions rapides</h5></div>
            <div class="card-body">
                <div class="row g-2">
                    <?php if (hasPermission('crud_eleves')): ?>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person-plus"></i> Inscrire un élève
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (hasPermission('crud_paiements')): ?>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>/pages/payments.php" class="btn btn-outline-success w-100">
                            <i class="bi bi-cash-stack"></i> Enregistrer un paiement
                        </a>
                    </div>
                    <?php endif; ?>
                    <?php if (hasPermission('crud_lecons')): ?>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>/pages/lessons.php" class="btn btn-outline-info w-100">
                            <i class="bi bi-calendar-plus"></i> Planifier une leçon
                        </a>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-3">
                        <a href="<?= BASE_URL ?>/pages/enrollments.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-journal-bookmark"></i> Voir les inscriptions
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
