<?php
/**
 * pages/payments.php — Paiements
 * SELECT → v_paiements | CRUD → sp_enregistrer/supprimer_paiement()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    requirePermission('crud_paiements');
    $msg = callProcedure("CALL sp_enregistrer_paiement(?,?,?,?,@msg)",
        [(int)$_POST['student_id'], (float)$_POST['montant'], $_POST['date_paiement'], $_POST['methode']]);
    if ($msg==='OK') { $message='Paiement enregistré !'; logActivity('AJOUT','paiements',null,$_POST['montant'].' \$'); notifyAdmins('Nouveau paiement','Un paiement de '.$_POST['montant'].'.00$ a été enregistré.','/pages/payments.php'); } else { $error=$msg; }
}
if (isset($_GET['delete'])) {
    requirePermission('crud_paiements');
    $msg = callProcedure("CALL sp_supprimer_paiement(?,@msg)", [(int)$_GET['delete']]);
    if ($msg==='OK') { $message='Paiement supprimé.'; logActivity('SUPPRESSION','paiements',(int)$_GET['delete']); } else { $error=$msg; }
}

$payments    = $pdo->query("SELECT * FROM v_paiements ORDER BY date_paiement DESC")->fetchAll();
$finances    = $pdo->query("SELECT * FROM v_stats_financieres")->fetch();
$totalPercu  = $finances['total_percu'];
$totalDu     = $finances['total_attendu'];
$students = $pdo->query("SELECT id, nom_complet AS label FROM v_eleves_select")->fetchAll();

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $payments = array_filter($payments, function($p) use ($search) {
        return stripos($p['student_nom'], $search) !== false || 
               stripos($p['formation_nom'], $search) !== false ||
               stripos($p['methode'], $search) !== false;
    });
    $payments = array_values($payments);
}
$total = count($payments);
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$paymentsPage = array_slice($payments, $offset, $perPage);

$pageTitle = 'Paiements — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-cash-stack me-2 text-primary"></i>Paiements</h1>
        <p class="text-muted mb-0"><?= $total ?> transaction(s)</p>
    </div>
    <?php if (hasPermission('crud_paiements')): ?>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i>Enregistrer
    </button>
    <?php endif; ?>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle-fill me-2"></i>Mode lecture seule.</div><?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-success bg-opacity-10">
            <div class="card-body text-center">
                <h6 class="text-success mb-1">Total perçu</h6>
                <h3 class="text-success mb-0"><?= number_format($totalPercu,2) ?> $</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
            <div class="card-body text-center">
                <h6 class="text-primary mb-1">Total attendu</h6>
                <h3 class="text-primary mb-0"><?= number_format($totalDu,2) ?> $</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-warning bg-opacity-10">
            <div class="card-body text-center">
                <h6 class="text-warning mb-1">Solde impayé</h6>
                <h3 class="text-warning mb-0"><?= number_format(max(0,$totalDu-$totalPercu),2) ?> $</h3>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary">Filtrer</button>
                <?php if ($search): ?><a href="?" class="btn btn-sm btn-outline-secondary">Réinitialiser</a><?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Historique des paiements</h5>
        <span class="badge bg-primary rounded-pill"><?= $total ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#ID</th>
                        <th>Date</th>
                        <th>Élève</th>
                        <th>Formation</th>
                        <th>Montant</th>
                        <th>Mode</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($paymentsPage)): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i>Aucun paiement trouvé</td></tr>
                <?php else: ?>
                <?php foreach ($paymentsPage as $row): ?>
                <tr>
                    <td class="ps-3"><span class="badge bg-secondary bg-opacity-10 text-secondary">#<?= $row['id'] ?></span></td>
                    <td><i class="bi bi-calendar3 text-muted me-2"></i><?= date('d/m/Y', strtotime($row['date_paiement'])) ?></td>
                    <td><span class="fw-medium"><?= htmlspecialchars($row['student_nom']) ?></span></td>
                    <td><?= htmlspecialchars($row['formation_nom']) ?></td>
                    <td><strong><?= number_format($row['montant'],2) ?> $</strong></td>
                    <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['methode']) ?></span></td>
                    <td class="text-end pe-3">
                        <?php if (hasPermission('crud_paiements')): ?>
                        <a href="?delete=<?= $row['id'] ?>&page=<?= $page ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white">
        <nav><ul class="pagination pagination-sm justify-content-center mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>">Précédent</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>">Suivant</a></li>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

<?php if (hasPermission('crud_paiements')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><form method="POST">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-cash-stack me-2"></i>Enregistrer un paiement</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="add">
            <div class="mb-3"><label class="form-label">Élève</label><select name="student_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['label']) ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Montant ($)</label><input type="number" name="montant" class="form-control" min="1" step="0.01" required></div>
            <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date_paiement" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
            <div class="mb-3"><label class="form-label">Mode</label><select name="methode" class="form-select"><option>Espèces</option><option>Mobile Money</option><option>Carte bancaire</option><option>Virement</option></select></div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
    </form></div></div>
</div>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>