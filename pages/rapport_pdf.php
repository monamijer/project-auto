<?php
/**
 * pages/rapport_pdf.php — Rapport imprimable (PDF via le navigateur)
 * Aucune librairie PDF externe n'est installée sur ce serveur (pas d'accès
 * réseau pour composer/TCPDF). Cette page est donc mise en page spécialement
 * pour l'impression : utilisez Ctrl+P puis "Enregistrer en PDF".
 * SELECT → v_dashboard_stats, v_stats_formations, v_export_paiements
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('export_donnees');

$periode = $_GET['periode'] ?? 'mois'; // 'mois' ou 'tout'

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
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Rapport — Auto École Pro</title>
<link rel="stylesheet" href="<?= BASE_URL ?>/node_modules/bootstrap/dist/css/bootstrap.min.css">
<style>
    body { padding: 2rem; }
    .no-print { }
    @media print {
        .no-print { display: none !important; }
        body { padding: 0; }
        .card { border: 1px solid #ccc !important; break-inside: avoid; }
    }
    .report-header { border-bottom: 3px solid #4f46e5; padding-bottom: 1rem; margin-bottom: 1.5rem; }
</style>
</head>
<body>

<div class="no-print mb-3 d-flex gap-2">
    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Imprimer / Enregistrer en PDF</button>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-outline-secondary">← Retour</a>
    <a href="?periode=mois" class="btn btn-outline-secondary <?= $periode==='mois'?'active':'' ?>">Ce mois</a>
    <a href="?periode=tout" class="btn btn-outline-secondary <?= $periode==='tout'?'active':'' ?>">Tout</a>
</div>

<div class="report-header d-flex justify-content-between align-items-center">
    <div>
        <h2>🚗 Auto École Pro</h2>
        <p class="text-muted mb-0">Rapport généré le <?= date('d/m/Y à H:i') ?> par <?= htmlspecialchars($_SESSION['username']) ?></p>
    </div>
    <div class="text-end">
        <h4>Rapport d'activité</h4>
        <span class="badge bg-secondary"><?= $periode==='mois' ? 'Mois en cours' : 'Toutes périodes' ?></span>
    </div>
</div>

<!-- Statistiques générales -->
<h5 class="mt-4 mb-3">Statistiques générales</h5>
<div class="row g-3 mb-4">
    <div class="col-3"><div class="card text-center p-2"><strong><?= $stats['nb_eleves'] ?></strong><br><small>Élèves actifs</small></div></div>
    <div class="col-3"><div class="card text-center p-2"><strong><?= $stats['nb_moniteurs'] ?></strong><br><small>Moniteurs</small></div></div>
    <div class="col-3"><div class="card text-center p-2"><strong><?= $stats['nb_vehicules_dispos'] ?></strong><br><small>Véhicules dispo.</small></div></div>
    <div class="col-3"><div class="card text-center p-2"><strong><?= number_format($stats['total_recettes'],2) ?> $</strong><br><small>Recettes totales</small></div></div>
</div>

<!-- Par formation -->
<h5 class="mb-3">Détail par formation</h5>
<table class="table table-bordered table-sm mb-4">
<thead class="table-light"><tr><th>Formation</th><th>Élèves</th><th>Perçu</th><th>Leçons effectuées</th></tr></thead>
<tbody>
<?php foreach ($formationStats as $f): ?>
<tr>
    <td><?= htmlspecialchars($f['formation_nom']) ?></td>
    <td><?= $f['total_eleves'] ?></td>
    <td><?= number_format($f['total_percu'],2) ?> $</td>
    <td><?= $f['lecons_effectuees'] ?></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>

<!-- Paiements de la période -->
<h5 class="mb-3">Paiements (<?= $periode==='mois'?'ce mois':'toutes périodes' ?>) — Total : <?= number_format($totalPeriode,2) ?> $</h5>
<table class="table table-bordered table-sm">
<thead class="table-light"><tr><th>Date</th><th>Élève</th><th>Formation</th><th>Montant</th><th>Mode</th></tr></thead>
<tbody>
<?php foreach ($paiements as $p): ?>
<tr>
    <td><?= htmlspecialchars($p['Date_Paiement']) ?></td>
    <td><?= htmlspecialchars($p['Eleve']) ?></td>
    <td><?= htmlspecialchars($p['Formation']) ?></td>
    <td><?= number_format($p['Montant'],2) ?> $</td>
    <td><?= htmlspecialchars($p['Mode_Paiement']) ?></td>
</tr>
<?php endforeach; ?>
<?php if (empty($paiements)): ?>
<tr><td colspan="5" class="text-center text-muted">Aucun paiement sur cette période.</td></tr>
<?php endif; ?>
</tbody>
</table>

<p class="text-muted small mt-4">Document généré automatiquement par Auto École Pro — Usage interne.</p>

</body>
</html>
