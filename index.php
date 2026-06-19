<?php
/**
 * index.php — Tableau de bord
 * SELECT → v_dashboard_stats, v_derniers_paiements, v_prochaines_lecons
 * Design moderne avec cartes animées et layout professionnel
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

<!-- Welcome Section -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">
            <i class="bi bi-speedometer2 me-2 text-primary"></i>Tableau de bord
        </h1>
        <p class="text-muted mb-0">
            Bienvenue, <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong>
            <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-secondary' ?> ms-2 rounded-pill">
                <i class="bi <?= isAdmin() ? 'bi-shield-check' : 'bi-person' ?> me-1"></i>
                <?= isAdmin() ? 'Admin' : 'Stagiaire' ?>
            </span>
        </p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <i class="bi bi-calendar3 text-muted"></i>
        <span class="text-muted"><?= date('l d F Y') ?></span>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card bg-primary bg-gradient text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Élèves inscrits</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_eleves'] ?></h2>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="bi bi-arrow-up-short me-1"></i>Total des élèves
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card bg-danger bg-gradient text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Moniteurs</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_moniteurs'] ?></h2>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="bi bi-person-check me-1"></i>Moniteurs actifs
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card bg-info bg-gradient text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Véhicules dispo.</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_vehicules_dispos'] ?></h2>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25">
                        <i class="bi bi-car-front-fill fs-4"></i>
                    </div>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="bi bi-check-circle me-1"></i>Prêts à utiliser
                </small>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm stat-card bg-success bg-gradient text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="text-white-50 mb-1">Leçons programmées</h6>
                        <h2 class="mb-0 fw-bold"><?= $stats['nb_lecons_programmees'] ?></h2>
                    </div>
                    <div class="icon-circle bg-white bg-opacity-25">
                        <i class="bi bi-calendar-check-fill fs-4"></i>
                    </div>
                </div>
                <small class="text-white-50 mt-2 d-block">
                    <i class="bi bi-clock me-1"></i>À venir
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm bg-dark text-white">
            <div class="card-body py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-circle bg-success bg-opacity-50">
                                <i class="bi bi-graph-up-arrow fs-4"></i>
                            </div>
                            <div>
                                <h6 class="text-white-50 mb-1">Total des recettes</h6>
                                <h3 class="mb-0 fw-bold"><?= number_format($stats['total_recettes'], 2) ?> $</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-success bg-opacity-25 text-success fs-6">
                            <i class="bi bi-check-circle me-1"></i>Mis à jour en temps réel
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tables Row -->
<div class="row g-3 mb-4">
    <!-- Recent Payments -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">
                    <i class="bi bi-receipt me-2 text-success"></i>Derniers paiements
                </h5>
                <a href="<?= BASE_URL ?>/pages/payments.php" class="btn btn-sm btn-outline-success">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($recentPayments)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-receipt display-4 mb-3 d-block"></i>
                        <p class="mb-0">Aucun paiement récent</p>
                        <small>Les derniers paiements apparaîtront ici</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
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
                                    <td class="ps-3">
                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                        <small><?= date('d/m/Y', strtotime($r['date_paiement'])) ?></small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs bg-success bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:32px;height:32px;">
                                                <span class="text-success small fw-bold">
                                                    <?= strtoupper(substr($r['student_nom'], 0, 1)) ?>
                                                </span>
                                            </div>
                                            <span class="fw-medium"><?= htmlspecialchars($r['student_nom']) ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success"><?= number_format($r['montant'], 2) ?> $</strong>
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

    <!-- Upcoming Lessons -->
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">
                    <i class="bi bi-calendar-check me-2 text-info"></i>Prochaines leçons
                </h5>
                <a href="<?= BASE_URL ?>/pages/lessons.php" class="btn btn-sm btn-outline-info">
                    Voir tout
                </a>
            </div>
            <div class="card-body p-0">
                <?php if (empty($upcomingLessons)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-4 mb-3 d-block"></i>
                        <p class="mb-0">Aucune leçon à venir</p>
                        <small>Les prochaines leçons apparaîtront ici</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date & Heure</th>
                                    <th>Élève</th>
                                    <th>Moniteur</th>
                                    <th class="text-end pe-3">Véhicule</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($upcomingLessons as $r): ?>
                                <tr>
                                    <td class="ps-3">
                                        <i class="bi bi-clock text-muted me-2"></i>
                                        <small><?= date('d/m H:i', strtotime($r['date_lecon'])) ?></small>
                                    </td>
                                    <td>
                                        <span class="fw-medium"><?= htmlspecialchars($r['student_nom']) ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= htmlspecialchars($r['instructor_nom']) ?></small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            <i class="bi bi-car-front me-1"></i>
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

<!-- Quick Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0">
                    <i class="bi bi-lightning-charge me-2 text-warning"></i>Actions rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <?php if (hasPermission('crud_eleves')): ?>
                    <div class="col-xl-3 col-md-6">
                        <a href="<?= BASE_URL ?>/pages/students.php" 
                           class="btn btn-outline-primary w-100 p-3 d-flex align-items-center justify-content-center gap-2 hover-lift">
                            <i class="bi bi-person-plus fs-5"></i>
                            <span>Inscrire un élève</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('crud_paiements')): ?>
                    <div class="col-xl-3 col-md-6">
                        <a href="<?= BASE_URL ?>/pages/payments.php" 
                           class="btn btn-outline-success w-100 p-3 d-flex align-items-center justify-content-center gap-2 hover-lift">
                            <i class="bi bi-cash-stack fs-5"></i>
                            <span>Enregistrer un paiement</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (hasPermission('crud_lecons')): ?>
                    <div class="col-xl-3 col-md-6">
                        <a href="<?= BASE_URL ?>/pages/lessons.php" 
                           class="btn btn-outline-info w-100 p-3 d-flex align-items-center justify-content-center gap-2 hover-lift">
                            <i class="bi bi-calendar-plus fs-5"></i>
                            <span>Planifier une leçon</span>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <div class="col-xl-3 col-md-6">
                        <a href="<?= BASE_URL ?>/pages/enrollments.php" 
                           class="btn btn-outline-secondary w-100 p-3 d-flex align-items-center justify-content-center gap-2 hover-lift">
                            <i class="bi bi-journal-bookmark fs-5"></i>
                            <span>Voir les inscriptions</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom minimal styles -->
<style>
.stat-card {
    transition: transform 0.2s ease;
}
.stat-card:hover {
    transform: translateY(-3px);
}
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.card {
    border-radius: 12px;
}
.badge {
    font-weight: 500;
}
</style>

<?php include BASE_PATH . '/includes/footer.php'; ?>