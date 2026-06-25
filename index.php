<?php
/**
 * index.php — Tableau de bord avec graphiques
 * SELECT → v_dashboard_stats, v_derniers_paiements, v_prochaines_lecons,
 *          v_revenus_mensuels, v_stats_examens
 */
session_start();
require_once __DIR__ . '/config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$stats = $pdo->query('SELECT * FROM v_dashboard_stats')->fetch();
$recentPayments = $pdo->query('SELECT * FROM v_derniers_paiements')->fetchAll();
$upcomingLessons = $pdo->query('SELECT * FROM v_prochaines_lecons')->fetchAll();
$revenusMensuels = $pdo->query('SELECT * FROM v_revenus_mensuels')->fetchAll();
$examStats = $pdo->query('SELECT * FROM v_stats_examens')->fetchAll();

$pageTitle = 'Tableau de bord — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h4 mb-1">Tableau de bord</h1>
        <p class="text-muted small mb-0">Bienvenue, <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong>
            <span class="badge <?= getRoleBadgeClass() ?> ms-2"><?= getRoleLabel() ?></span>
        </p>
    </div>
    <div class="text-muted small"><i class="bi bi-calendar3 me-1"></i><?= date('d/m/Y') ?></div>
</div>

<!-- KPI Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="card border shadow-sm h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start"><div><small class="text-muted text-uppercase">Élèves</small><h3 class="mb-0 fw-bold"><?= $stats['nb_eleves'] ?></h3></div><span class="badge bg-primary bg-opacity-10 text-primary p-2"><i class="bi bi-people-fill fs-5"></i></span></div>
        <small class="text-muted mt-2 d-block">Total inscrits</small>
    </div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card border shadow-sm h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start"><div><small class="text-muted text-uppercase">Moniteurs</small><h3 class="mb-0 fw-bold"><?= $stats['nb_moniteurs'] ?></h3></div><span class="badge bg-info bg-opacity-10 text-info p-2"><i class="bi bi-person-badge-fill fs-5"></i></span></div>
        <small class="text-muted mt-2 d-block">Actifs</small>
    </div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card border shadow-sm h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start"><div><small class="text-muted text-uppercase">Véhicules</small><h3 class="mb-0 fw-bold"><?= $stats['nb_vehicules_dispos'] ?></h3></div><span class="badge bg-success bg-opacity-10 text-success p-2"><i class="bi bi-car-front-fill fs-5"></i></span></div>
        <small class="text-muted mt-2 d-block">Disponibles</small>
    </div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card border shadow-sm h-100"><div class="card-body">
        <div class="d-flex justify-content-between align-items-start"><div><small class="text-muted text-uppercase">Recettes</small><h3 class="mb-0 fw-bold"><?= number_format($stats['total_recettes'], 0) ?> $</h3></div><span class="badge bg-warning bg-opacity-10 text-warning p-2"><i class="bi bi-cash-stack fs-5"></i></span></div>
        <small class="text-muted mt-2 d-block">Totales</small>
    </div></div></div>
</div>

<!-- Graphiques -->
<div class="row g-3 mb-4">
    <!-- Revenus mensuels -->
    <div class="col-lg-8">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="mb-0"><i class="bi bi-graph-up me-2"></i>Revenus mensuels</h6></div>
            <div class="card-body"><canvas id="revenueChart" height="250"></canvas></div>
        </div>
    </div>
    <!-- Répartition examens -->
    <div class="col-lg-4">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3"><h6 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Taux réussite examens</h6></div>
            <div class="card-body"><canvas id="examChart" height="250"></canvas></div>
        </div>
    </div>
</div>

<!-- Tables -->
<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center"><h6 class="mb-0">Derniers paiements</h6><a href="<?= BASE_URL ?>/pages/payments.php" class="btn btn-sm btn-outline-secondary">Voir tout</a></div>
            <div class="card-body p-0">
                <?php if (empty($recentPayments)): ?><div class="text-center py-5 text-muted"><i class="bi bi-receipt fs-1 mb-2 d-block opacity-50"></i><small>Aucun paiement récent</small></div>
                <?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0 small"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Élève</th><th class="text-end pe-3">Montant</th></tr></thead><tbody>
                <?php foreach ($recentPayments as $r): ?><tr><td class="ps-3 text-muted"><?= date('d/m/Y', strtotime($r['date_paiement'])) ?></td><td class="fw-medium"><?= htmlspecialchars($r['student_nom']) ?></td><td class="text-end pe-3 text-success fw-medium"><?= number_format($r['montant'], 2) ?> $</td></tr><?php endforeach; ?>
                </tbody></table></div><?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center"><h6 class="mb-0">Prochaines leçons</h6><a href="<?= BASE_URL ?>/pages/lessons.php" class="btn btn-sm btn-outline-secondary">Voir tout</a></div>
            <div class="card-body p-0">
                <?php if (empty($upcomingLessons)): ?><div class="text-center py-5 text-muted"><i class="bi bi-calendar-x fs-1 mb-2 d-block opacity-50"></i><small>Aucune leçon à venir</small></div>
                <?php else: ?><div class="table-responsive"><table class="table table-hover align-middle mb-0 small"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Élève</th><th class="text-end pe-3">Moniteur</th></tr></thead><tbody>
                <?php foreach ($upcomingLessons as $r): ?><tr><td class="ps-3 text-muted"><?= date('d/m H:i', strtotime($r['date_lecon'])) ?></td><td class="fw-medium"><?= htmlspecialchars($r['student_nom']) ?></td><td class="text-end pe-3 text-muted"><?= htmlspecialchars($r['instructor_nom']) ?></td></tr><?php endforeach; ?>
                </tbody></table></div><?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Actions rapides -->
<div class="card border shadow-sm">
    <div class="card-header bg-white py-3"><h6 class="mb-0">Actions rapides</h6></div>
    <div class="card-body"><div class="row g-2">
        <?php if (hasPermission('crud_eleves')): ?><div class="col-xl-3 col-md-6"><a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-outline-dark w-100 py-2"><i class="bi bi-person-plus me-1"></i>Inscrire un élève</a></div><?php endif; ?>
        <?php if (hasPermission('crud_paiements')): ?><div class="col-xl-3 col-md-6"><a href="<?= BASE_URL ?>/pages/payments.php" class="btn btn-outline-dark w-100 py-2"><i class="bi bi-cash me-1"></i>Enregistrer paiement</a></div><?php endif; ?>
        <?php if (hasPermission('crud_lecons')): ?><div class="col-xl-3 col-md-6"><a href="<?= BASE_URL ?>/pages/lessons.php" class="btn btn-outline-dark w-100 py-2"><i class="bi bi-calendar-plus me-1"></i>Planifier leçon</a></div><?php endif; ?>
        <div class="col-xl-3 col-md-6"><a href="<?= BASE_URL ?>/pages/search.php" class="btn btn-outline-dark w-100 py-2"><i class="bi bi-search me-1"></i>Rechercher</a></div>
    </div></div>
</div>

<script src="<?= BASE_URL ?>/node_modules/chart.js/dist/chart.umd.js"></script>
<script>
// Revenus mensuels
const revCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($revenusMensuels, 'mois')) ?>,
        datasets: [{
            label: 'Revenus ($)',
            data: <?= json_encode(array_column($revenusMensuels, 'total')) ?>,
            backgroundColor: '#4f46e5',
            borderRadius: 6
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Taux réussite examens
const examCtx = document.getElementById('examChart').getContext('2d');
const examLabels = <?= json_encode(array_column($examStats, 'formation_nom')) ?>;
const examData = <?= json_encode(array_column($examStats, 'eligibles')) ?>;
new Chart(examCtx, {
    type: 'doughnut',
    data: {
        labels: examLabels,
        datasets: [{ data: examData, backgroundColor: ['#4f46e5','#22c55e','#f59e0b','#ef4444','#06b6d4','#8b5cf6'] }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>