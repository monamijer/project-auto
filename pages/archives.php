<?php
/**
 * pages/archives.php — Archivage et restauration des données
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('gestion_comptes');

$message = ''; $error = '';

// Lancer un archivage
if (isset($_POST['action']) && $_POST['action'] === 'archiver') {
    $type = $_POST['type_archive'] ?? '';
    if (in_array($type, ['eleves', 'paiements', 'lecons'])) {
        $msg = callProcedure("CALL sp_archiver_{$type}(?, @msg)", [$_SESSION['username']]);
        $result = $pdo->query("SELECT @msg AS msg")->fetch();
        $message = $result['msg'] ?? 'Archive créée.';
        logActivity('ARCHIVAGE', $type, null, $result['msg']);
    }
}

// Restaurer une archive
if (isset($_GET['restaurer'])) {
    $msg = callProcedure("CALL sp_restaurer_archive(?, @msg)", [(int)$_GET['restaurer']]);
    $result = $pdo->query("SELECT @msg AS msg")->fetch();
    $message = $result['msg'] ?? 'Archive restaurée.';
    logActivity('RESTAURATION', 'archives', (int)$_GET['restaurer']);
}

$archives = $pdo->query("SELECT * FROM v_archives")->fetchAll();

// Stats
$statsArchive = $pdo->query("
    SELECT 
        (SELECT COUNT(*) FROM utilisateurs WHERE deleted_at IS NOT NULL) AS eleves_a_archiver,
        (SELECT COUNT(*) FROM lecons WHERE statut IN ('effectuée','annulée') AND date_lecon < DATE_SUB(NOW(), INTERVAL 6 MONTH)) AS lecons_a_archiver,
        (SELECT COUNT(*) FROM paiement WHERE date_paiement < DATE_SUB(NOW(), INTERVAL 12 MONTH)) AS paiements_a_archiver
")->fetch();

$pageTitle = 'Archives — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h3 mb-1"><i class="bi bi-archive me-2 text-primary"></i>Archivage</h1><p class="text-muted mb-0">Gérez l'archivage des anciennes données</p></div>
</div>

<?php if($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Cartes d'actions -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <i class="bi bi-people-fill text-primary display-4 mb-2 d-block"></i>
                <h6>Élèves supprimés (+6 mois)</h6>
                <h3 class="fw-bold"><?= $statsArchive['eleves_a_archiver'] ?></h3>
                <form method="POST" class="mt-2">
                    <input type="hidden" name="action" value="archiver">
                    <input type="hidden" name="type_archive" value="eleves">
                    <button type="submit" class="btn btn-primary btn-sm" <?= $statsArchive['eleves_a_archiver']==0?'disabled':'' ?>>
                        <i class="bi bi-archive me-1"></i>Archiver
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <i class="bi bi-calendar-check text-success display-4 mb-2 d-block"></i>
                <h6>Leçons terminées (+6 mois)</h6>
                <h3 class="fw-bold"><?= $statsArchive['lecons_a_archiver'] ?></h3>
                <form method="POST" class="mt-2">
                    <input type="hidden" name="action" value="archiver">
                    <input type="hidden" name="type_archive" value="lecons">
                    <button type="submit" class="btn btn-success btn-sm" <?= $statsArchive['lecons_a_archiver']==0?'disabled':'' ?>>
                        <i class="bi bi-archive me-1"></i>Archiver
                    </button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <i class="bi bi-cash-stack text-warning display-4 mb-2 d-block"></i>
                <h6>Paiements anciens (+12 mois)</h6>
                <h3 class="fw-bold"><?= $statsArchive['paiements_a_archiver'] ?></h3>
                <form method="POST" class="mt-2">
                    <input type="hidden" name="action" value="archiver">
                    <input type="hidden" name="type_archive" value="paiements">
                    <button type="submit" class="btn btn-warning btn-sm" <?= $statsArchive['paiements_a_archiver']==0?'disabled':'' ?>>
                        <i class="bi bi-archive me-1"></i>Archiver
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Historique des archives -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historique des archives</h5></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="ps-3">Date</th><th>Type</th><th>Fichier</th><th>Enregistrements</th><th>Créé par</th><th class="text-end pe-3">Action</th></tr></thead>
        <tbody>
        <?php if(empty($archives)): ?><tr><td colspan="6" class="text-center py-4 text-muted">Aucune archive</td></tr>
        <?php else: foreach($archives as $a): ?>
        <tr>
            <td class="ps-3"><small><?= date('d/m/Y H:i', strtotime($a['created_at'])) ?></small></td>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($a['type_archive']) ?></span></td>
            <td><small><?= htmlspecialchars($a['nom_fichier']) ?></small></td>
            <td><?= $a['nb_enregistrements'] ?></td>
            <td><small><?= htmlspecialchars($a['cree_par'] ?? '—') ?></small></td>
            <td class="text-end pe-3">
                <a href="?restaurer=<?= $a['id'] ?>" class="btn btn-sm btn-outline-success" onclick="return confirm('Restaurer cette archive ?')"><i class="bi bi-arrow-counterclockwise"></i></a>
            </td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div></div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>