<?php
/**
 * index.php — Tableau de bord
 * SELECT → v_dashboard_stats, v_derniers_paiements, v_prochaines_lecons
 * Design minimaliste, propre et professionnel
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

<!-- Header -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1">Tableau de bord</h1>
        <p class="text-muted small mb-0">
            Bienvenue, <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong>
            <span class="badge <?= isAdmin() ? 'bg-dark' : 'bg-secondary' ?> ms-2">
                <?= isAdmin() ? 'Admin' : 'Stagiaire' ?>
            </span>
        </p>
    </div>
    <div class="text-muted small">
        <i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?>
    </div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted text-uppercase">Élèves</small>
                        <h3 class="mb-0 fw-bold"><?= $stats['nb_eleves'] ?></h3>
                    </div>
                    <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                        <i class="bi bi-people-fill fs-5"></i>
                    </span>
                </div>
                <small class="text-muted mt-2 d-block">Total inscrits</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted text-uppercase">Moniteurs</small>
                        <h3 class="mb-0 fw-bold"><?= $stats['nb_moniteurs'] ?></h3>
                    </div>
                    <span class="badge bg-info bg-opacity-10 text-info p-2">
                        <i class="bi bi-person-badge-fill fs-5"></i>
                    </span>
                </div>
                <small class="text-muted mt-2 d-block">Actifs</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted text-uppercase">Véhicules</small>
                        <h3 class="mb-0 fw-bold"><?= $stats['nb_vehicules_dispos'] ?></h3>
                    </div>
                    <span class="badge bg-success bg-opacity-10 text-success p-2">
                        <i class="bi bi-car-front-fill fs-5"></i>
                    </span>
                </div>
                <small class="text-muted mt-2 d-block">Disponibles</small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <small class="text-muted text-uppercase">Leçons</small>
                        <h3 class="mb-0 fw-bold"><?= $stats['nb_lecons_programmees'] ?></h3>
                    </div>
                    <span class="badge bg-warning bg-opacity-10 text-warning p-2">
                        <i class="bi bi-calendar-check-fill fs-5"></i>
                    </span>
                </div>
                <small class="text-muted mt-2 d-block">Programmées</small>
            </div>
        </div>
    </div>
</div>

<!-- Revenue -->
<div class="card border shadow-sm mb-4">
    <div class="card-body py-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-success bg-opacity-10 text-success p-2">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </span>
                    <div>
                        <small class="text-muted text-uppercase">Total recettes</small>
                        <h4 class="mb-0 fw-bold"><?= number_format($stats['total_recettes'], 2) ?> $</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                <span class="badge bg-light text-muted">
                    <i class="bi bi-check-circle me-1"></i>Temps réel
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row g-3 mb-4">
    <!-- Derniers paiements -->
    <div class="col-lg-6">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Derniers paiements</h6>
                <a href="<?= BASE_URL ?>/pages/payments.php" class="btn btn-sm btn-outline-secondary">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentPayments)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-receipt fs-1 mb-2 d-block opacity-50"></i>
                        <small>Aucun paiement récent</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Élève</th>
                                    <th>Montant</th>
                                    <th class="text-end pe-3">Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentPayments as $r): ?>
                                <tr>
                                    <td class="ps-3 text-muted">
                                        <?= date('d/m/Y', strtotime($r['date_paiement'])) ?>
                                    </td>
                                    <td class="fw-medium"><?= htmlspecialchars($r['student_nom']) ?></td>
                                    <td class="text-success fw-medium">
                                        <?= number_format($r['montant'], 2) ?> $
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-light text-dark">
                                            <?= htmlspecialchars($r['methode']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Prochaines leçons -->
    <div class="col-lg-6">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Prochaines leçons</h6>
                <a href="<?= BASE_URL ?>/pages/lessons.php" class="btn btn-sm btn-outline-secondary">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingLessons)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x fs-1 mb-2 d-block opacity-50"></i>
                        <small>Aucune leçon à venir</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 small">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Élève</th>
                                    <th>Moniteur</th>
                                    <th class="text-end pe-3">Véhicule</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($upcomingLessons as $r): ?>
                                <tr>
                                    <td class="ps-3 text-muted">
                                        <?= date('d/m H:i', strtotime($r['date_lecon'])) ?>
                                    </td>
                                    <td class="fw-medium"><?= htmlspecialchars($r['student_nom']) ?></td>
                                    <td class="text-muted"><?= htmlspecialchars($r['instructor_nom']) ?></td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <?= htmlspecialchars($r['vehicle_nom']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="card border shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0">Actions rapides</h6>
    </div>
    <div class="card-body">
        <div class="row g-2">
            <?php if (hasPermission('crud_eleves')): ?>
            <div class="col-xl-3 col-md-6">
                <a href="<?= BASE_URL ?>/pages/students.php" 
                   class="btn btn-outline-dark w-100 py-2">
                    <i class="bi bi-person-plus me-1"></i>Inscrire un élève
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (hasPermission('crud_paiements')): ?>
            <div class="col-xl-3 col-md-6">
                <a href="<?= BASE_URL ?>/pages/payments.php" 
                   class="btn btn-outline-dark w-100 py-2">
                    <i class="bi bi-cash me-1"></i>Enregistrer paiement
                </a>
            </div>
            <?php endif; ?>
            
            <?php if (hasPermission('crud_lecons')): ?>
            <div class="col-xl-3 col-md-6">
                <a href="<?= BASE_URL ?>/pages/lessons.php" 
                   class="btn btn-outline-dark w-100 py-2">
                    <i class="bi bi-calendar-plus me-1"></i>Planifier leçon
                </a>
            </div>
            <?php endif; ?>
            
            <div class="col-xl-3 col-md-6">
                <a href="<?= BASE_URL ?>/pages/search.php" 
                   class="btn btn-outline-dark w-100 py-2">
                    <i class="bi bi-search me-1"></i>Rechercher
                </a>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>