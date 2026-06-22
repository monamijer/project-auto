<?php
/**
 * pages/instructors.php — Moniteurs
 * SELECT → v_moniteurs | CRUD → sp_ajouter/modifier/supprimer_moniteur()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    requirePermission('crud_moniteurs');
    $msg = callProcedure("CALL sp_ajouter_moniteur(?,?,?,?,?,@msg)",
        [trim($_POST['nom']), trim($_POST['prenom']), trim($_POST['nationalite']),
         trim($_POST['telephone']), (int)$_POST['experience']]);
    $msg === 'OK' ? $message = 'Moniteur ajouté !' : $error = $msg;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    requirePermission('crud_moniteurs');
    $msg = callProcedure("CALL sp_modifier_moniteur(?,?,?,?,?,?,@msg)",
        [(int)$_POST['id'], trim($_POST['nom']), trim($_POST['prenom']),
         trim($_POST['nationalite']), trim($_POST['telephone']), (int)$_POST['experience']]);
    $msg === 'OK' ? $message = 'Moniteur modifié !' : $error = $msg;
}
if (isset($_GET['delete'])) {
    requirePermission('crud_moniteurs');
    $msg = callProcedure("CALL sp_supprimer_moniteur(?,@msg)", [(int)$_GET['delete']]);
    $msg === 'OK' ? $message = 'Moniteur supprimé !' : $error = 'Impossible : leçons associées.';
}

$instructors = $pdo->query("SELECT * FROM v_moniteurs ORDER BY nom")->fetchAll();

// Pagination
$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $instructors = array_filter($instructors, function($i) use ($search) {
        return stripos($i['nom_complet'], $search) !== false || 
               stripos($i['nationalite'] ?? '', $search) !== false ||
               stripos($i['telephone'] ?? '', $search) !== false;
    });
    $instructors = array_values($instructors);
}
$total = count($instructors);
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$instructorsPage = array_slice($instructors, $offset, $perPage);

$pageTitle = 'Moniteurs — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-person-badge-fill me-2 text-primary"></i>Moniteurs</h1>
        <p class="text-muted mb-0"><?= $total ?> moniteur(s)</p>
    </div>
    <?php if (hasPermission('crud_moniteurs')): ?>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-person-plus me-1"></i>Ajouter
    </button>
    <?php endif; ?>
</div>

<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
<?php endif; ?>
<?php if (!isAdmin()): ?>
<div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle-fill me-2"></i>Mode lecture seule.</div>
<?php endif; ?>

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
                <?php if ($search): ?>
                <a href="?" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des moniteurs</h5>
        <span class="badge bg-primary rounded-pill"><?= $total ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#Matricule</th>
                        <th>Nom complet</th>
                        <th>Nationalité</th>
                        <th>Téléphone</th>
                        <th>Expérience</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($instructorsPage)): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i>Aucun moniteur trouvé</td></tr>
                <?php else: ?>
                <?php foreach ($instructorsPage as $row): ?>
                <tr>
                    <td class="ps-3"><span class="badge bg-secondary bg-opacity-10 text-secondary">#<?= $row['id'] ?></span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                <span class="text-primary fw-bold small"><?= strtoupper(substr($row['prenom'],0,1).substr($row['nom'],0,1)) ?></span>
                            </div>
                            <span class="fw-medium"><?= htmlspecialchars($row['nom_complet']) ?></span>
                        </div>
                    </td>
                    <td><?= $row['nationalite'] ? htmlspecialchars($row['nationalite']) : '<span class="text-muted">—</span>' ?></td>
                    <td><?= $row['telephone'] ? htmlspecialchars($row['telephone']) : '<span class="text-muted">—</span>' ?></td>
                    <td><span class="badge bg-light text-dark"><?= (int)$row['experience'] ?> an(s)</span></td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <?php if (hasPermission('crud_moniteurs')): ?>
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['id'] ?>" title="Modifier"><i class="bi bi-pencil"></i></button>
                            <a href="?delete=<?= $row['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <?php if (hasPermission('crud_moniteurs')): ?>
                <div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content"><form method="POST">
                        <div class="modal-header"><h5 class="modal-title">Modifier moniteur</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($row['prenom']) ?>" required></div>
                                <div class="col-md-6"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($row['nom']) ?>" required></div>
                                <div class="col-md-6"><label class="form-label">Nationalité</label><input type="text" name="nationalite" class="form-control" value="<?= htmlspecialchars($row['nationalite'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($row['telephone'] ?? '') ?>"></div>
                                <div class="col-md-6"><label class="form-label">Expérience (ans)</label><input type="number" name="experience" class="form-control" value="<?= (int)$row['experience'] ?>" min="0" required></div>
                            </div>
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
                    </form></div></div>
                </div>
                <?php endif; ?>
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

<?php if (hasPermission('crud_moniteurs')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><form method="POST">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Ajouter un moniteur</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="add">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Nationalité</label><input type="text" name="nationalite" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Expérience (ans)</label><input type="number" name="experience" class="form-control" min="0" required></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
    </form></div></div>
</div>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>