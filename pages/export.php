<?php
/**
 * pages/export.php — Export Excel / CSV
 * SELECT → v_export_eleves, v_export_paiements, v_export_lecons
 * Génère un fichier .csv lisible directement par Excel (compatible Office/LibreOffice).
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('export_donnees');

// ── Si un type est demandé, on génère et on télécharge le CSV ─────────────
if (isset($_GET['type'])) {
    $view = match($_GET['type']) {
        'eleves'    => 'v_export_eleves',
        'paiements' => 'v_export_paiements',
        'lecons'    => 'v_export_lecons',
        default     => null,
    };

    if ($view) {
        $rows = $pdo->query("SELECT * FROM $view")->fetchAll();
        logActivity('EXPORT', 'export', null, $_GET['type']);

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="export_' . $_GET['type'] . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputs($output, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel

        if (!empty($rows)) {
            fputcsv($output, array_keys($rows[0]), ';');   // en-têtes
            foreach ($rows as $row) {
                fputcsv($output, $row, ';');
            }
        }
        fclose($output);
        exit();
    }
}

$pageTitle = 'Export — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-file-earmark-spreadsheet me-2"></i>Export de données (Excel/CSV)</h1>
</div>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-people text-primary" style="font-size:2.5rem;"></i>
                <h5 class="mt-3">Élèves</h5>
                <p class="text-muted small">Liste complète avec formation, paiements et soldes.</p>
                <a href="?type=eleves" class="btn btn-primary"><i class="bi bi-download"></i> Exporter</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-cash text-success" style="font-size:2.5rem;"></i>
                <h5 class="mt-3">Paiements</h5>
                <p class="text-muted small">Historique complet de tous les paiements enregistrés.</p>
                <a href="?type=paiements" class="btn btn-success"><i class="bi bi-download"></i> Exporter</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-info" style="font-size:2.5rem;"></i>
                <h5 class="mt-3">Leçons</h5>
                <p class="text-muted small">Toutes les leçons : élève, moniteur, véhicule, statut.</p>
                <a href="?type=lecons" class="btn btn-info text-white"><i class="bi bi-download"></i> Exporter</a>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info mt-4">
    <i class="bi bi-info-circle me-2"></i>
    Les fichiers <code>.csv</code> générés s'ouvrent directement dans <strong>Excel</strong>, <strong>LibreOffice Calc</strong> ou <strong>Google Sheets</strong>.
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
