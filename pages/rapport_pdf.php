<?php
/**
 * pages/rapport_pdf.php — Rapport imprimable (PDF via le navigateur)
 * SELECT → v_dashboard_stats, v_stats_formations, v_export_paiements
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('export_donnees');

$periode = $_GET['periode'] ?? 'mois';

$stats = $pdo->query("SELECT * FROM v_dashboard_stats")->fetch();
$formationStats = $pdo->query("SELECT * FROM v_stats_formations")->fetchAll();

$sqlPaiements = "SELECT * FROM v_export_paiements";
if ($periode === 'mois') {
    $sqlPaiements .= " WHERE MONTH(Date_Paiement)=MONTH(CURDATE()) AND YEAR(Date_Paiement)=YEAR(CURDATE())";
}
$paiements = $pdo->query($sqlPaiements)->fetchAll();
$totalPeriode = array_sum(array_column($paiements, 'Montant'));

logActivity('EXPORT', 'rapport_pdf', null, $periode);

include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 no-print">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Rapport</h1>
        <p class="text-muted mb-0">Rapport généré le <?= date('d/m/Y à H:i') ?></p>
    </div>
    <div class="d-flex gap-2">
        <div class="btn-group btn-group-sm">
            <a href="?periode=mois" class="btn btn-outline-primary <?= $periode==='mois'?'active':'' ?>">Ce mois</a>
            <a href="?periode=tout" class="btn btn-outline-primary <?= $periode==='tout'?'active':'' ?>">Tout</a>
        </div>
        <button onclick="window.print()" class="btn btn-primary btn-sm shadow-sm"><i class="bi bi-printer me-1"></i>Imprimer PDF</button>
        <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary btn-sm">Retour</a>
    </div>
</div>

<!-- Report content -->
<div class="report-wrapper">

    <!-- Header -->
    <div class="card shadow-sm border-0 mb-4 report-header-card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-1"><i class="bi bi-car-front-fill me-2"></i>Auto École Pro</h4>
                    <p class="text-muted mb-0 small">Rapport généré le <?= date('d/m/Y à H:i') ?> par <?= htmlspecialchars($_SESSION['username']) ?></p>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <h5 class="mb-1">Rapport d'activité</h5>
                    <span class="badge <?= $periode==='mois' ? 'bg-primary' : 'bg-secondary' ?>"><?= $periode==='mois' ? 'Mois en cours' : 'Toutes périodes' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats cards -->
    <h5 class="mb-3"><i class="bi bi-bar-chart me-2"></i>Statistiques générales</h5>
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body py-3">
                    <i class="bi bi-people-fill text-primary fs-3 mb-2 d-block"></i>
                    <h3 class="mb-0 fw-bold"><?= $stats['nb_eleves'] ?></h3>
                    <small class="text-muted">Élèves actifs</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body py-3">
                    <i class="bi bi-person-badge-fill text-info fs-3 mb-2 d-block"></i>
                    <h3 class="mb-0 fw-bold"><?= $stats['nb_moniteurs'] ?></h3>
                    <small class="text-muted">Moniteurs</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body py-3">
                    <i class="bi bi-car-front-fill text-success fs-3 mb-2 d-block"></i>
                    <h3 class="mb-0 fw-bold"><?= $stats['nb_vehicules_dispos'] ?></h3>
                    <small class="text-muted">Véhicules dispo.</small>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-6">
            <div class="card shadow-sm border-0 text-center">
                <div class="card-body py-3">
                    <i class="bi bi-cash-stack text-warning fs-3 mb-2 d-block"></i>
                    <h3 class="mb-0 fw-bold"><?= number_format($stats['total_recettes'],2) ?> $</h3>
                    <small class="text-muted">Recettes totales</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Formations -->
    <h5 class="mb-3"><i class="bi bi-mortarboard me-2"></i>Détail par formation</h5>
    <div class="card shadow-sm border-0 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Formation</th>
                        <th class="text-center">Élèves</th>
                        <th class="text-end">Perçu</th>
                        <th class="text-center pe-3">Leçons</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($formationStats)): ?>
                    <tr><td colspan="4" class="text-center py-4 text-muted">Aucune donnée</td></tr>
                    <?php else: ?>
                    <?php foreach ($formationStats as $f): ?>
                    <tr>
                        <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($f['formation_nom']) ?></span></td>
                        <td class="text-center"><span class="badge bg-primary rounded-pill"><?= $f['total_eleves'] ?></span></td>
                        <td class="text-end"><?= number_format($f['total_percu'],2) ?> $</td>
                        <td class="text-center pe-3"><span class="badge bg-success rounded-pill"><?= $f['lecons_effectuees'] ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Paiements -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0"><i class="bi bi-receipt me-2"></i>Paiements</h5>
        <span class="badge bg-primary rounded-pill">Total : <?= number_format($totalPeriode,2) ?> $</span>
    </div>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Date</th>
                        <th>Élève</th>
                        <th>Formation</th>
                        <th class="text-end">Montant</th>
                        <th class="pe-3">Mode</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($paiements)): ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Aucun paiement sur cette période</td></tr>
                    <?php else: ?>
                    <?php foreach ($paiements as $p): ?>
                    <tr>
                        <td class="ps-3"><i class="bi bi-calendar3 text-muted me-2"></i><?= htmlspecialchars($p['Date_Paiement']) ?></td>
                        <td><span class="fw-medium"><?= htmlspecialchars($p['Eleve']) ?></span></td>
                        <td><?= htmlspecialchars($p['Formation']) ?></td>
                        <td class="text-end"><strong><?= number_format($p['Montant'],2) ?> $</strong></td>
                        <td class="pe-3"><span class="badge bg-light text-dark"><?= htmlspecialchars($p['Mode_Paiement']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Footer -->
    <p class="text-muted small text-center mt-4">Document généré automatiquement par Auto École Pro — Usage interne</p>

</div>

<!-- Print styles -->
<style>
@media print {
    body { background: #fff !important; }
    .sidebar, .mobile-topbar, .sidebar-backdrop, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    .card { border: 1px solid #dee2e6 !important; box-shadow: none !important; break-inside: avoid; }
    .report-header-card { border-bottom: 3px solid #4f46e5 !important; }
    .table { font-size: 0.85rem; }
}
.report-wrapper { max-width: 900px; }
.report-header-card { border-left: 4px solid #4f46e5; }
</style>

<?php include BASE_PATH . '/includes/footer.php'; ?>