<?php
/**
 * pages/vehicles.php — Véhicules
 * SELECT → v_vehicules | CRUD → sp_ajouter/modifier/supprimer_vehicule()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    requirePermission('crud_vehicules');
    $msg = callProcedure('CALL sp_ajouter_vehicule(?,?,?,?,@msg)', [trim($_POST['marque']), trim($_POST['modele']), trim($_POST['immatriculation']), (int) $_POST['annee']]);
    $msg === 'OK' ? ($message = 'Véhicule ajouté !') : ($error = $msg);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    requirePermission('crud_vehicules');
    $msg = callProcedure('CALL sp_modifier_vehicule(?,?,?,?,?,?,@msg)', [
        (int) $_POST['id'],
        trim($_POST['marque']),
        trim($_POST['modele']),
        trim($_POST['immatriculation']),
        (int) $_POST['annee'],
        (int) ($_POST['disponibilite'] ?? 0),
    ]);
    $msg === 'OK' ? ($message = 'Véhicule modifié !') : ($error = $msg);
}
if (isset($_GET['delete'])) {
    requirePermission('crud_vehicules');
    $msg = callProcedure('CALL sp_supprimer_vehicule(?,?,@msg)', [(int) $_GET['delete'], $_SESSION['username']]);
    $msg === 'OK' ? ($message = 'Véhicule déplacé vers la corbeille.') : ($error = $msg);
}

$vehicles = $pdo->query('SELECT * FROM v_vehicules ORDER BY marque')->fetchAll();

// Pagination
$perPage = 20;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $vehicles = array_filter($vehicles, function ($v) use ($search) {
        return stripos($v['marque'], $search) !== false || stripos($v['modele'], $search) !== false || stripos($v['immatriculation'], $search) !== false;
    });
    $vehicles = array_values($vehicles);
}
$total = count($vehicles);
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$vehiclesPage = array_slice($vehicles, $offset, $perPage);

$pageTitle = 'Véhicules — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-car-front-fill me-2 text-primary"></i>Véhicules</h1>
        <p class="text-muted mb-0"><?= $total ?> véhicule(s)</p>
    </div>
    <?php if (hasPermission('crud_vehicules')): ?>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-plus-lg me-1"></i>Ajouter
    </button>
    <?php endif; ?>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars(
    $message
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars(
    $error
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info d-flex align-items-center"><i class="bi bi-info-circle-fill me-2"></i>Mode lecture seule.</div><?php endif; ?>

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
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des véhicules</h5>
        <span class="badge bg-primary rounded-pill"><?= $total ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#ID</th>
                        <th>Véhicule</th>
                        <th>Immatriculation</th>
                        <th>Année</th>
                        <th>Disponibilité</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($vehiclesPage)): ?>
                <tr><td colspan="6" class="text-center py-5 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i>Aucun véhicule trouvé</td></tr>
                <?php else: ?>
                <?php foreach ($vehiclesPage as $row): ?>
                <tr>
                    <td class="ps-3"><span class="badge bg-secondary bg-opacity-10 text-secondary">#<?= $row['id'] ?></span></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-secondary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                <i class="bi bi-car-front text-secondary"></i>
                            </div>
                            <span class="fw-medium"><?= htmlspecialchars($row['marque'] . ' ' . $row['modele']) ?></span>
                        </div>
                    </td>
                    <td><code class="bg-light px-2 py-1 rounded"><?= htmlspecialchars($row['immatriculation']) ?></code></td>
                    
                    <td>
                        <?php if ($row['disponibilite']): ?>
                        <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check2 me-1"></i>Disponible</span>
                        <?php else: ?>
                        <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-lg me-1"></i>Indisponible</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <?php if (hasPermission('crud_vehicules')): ?>
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['id'] ?>" title="Modifier"><i class="bi bi-pencil"></i></button>
                            <a href="?delete=<?= $row[
                                'id'
                            ] ?>" class="btn btn-outline-danger" onclick="return confirm('Déplacer vers la corbeille ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <?php if (hasPermission('crud_vehicules')): ?>
                <div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog"><div class="modal-content"><form method="POST">
                        <div class="modal-header"><h5 class="modal-title">Modifier véhicule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                        <div class="modal-body">
                            <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <div class="row g-3">
                                <div class="col-md-6"><label class="form-label">Marque</label><input type="text" name="marque" class="form-control" value="<?= htmlspecialchars(
                                    $row['marque']
                                ) ?>" required></div>
                                <div class="col-md-6"><label class="form-label">Modèle</label><input type="text" name="modele" class="form-control" value="<?= htmlspecialchars(
                                    $row['modele']
                                ) ?>" required></div>
                                <div class="col-md-6"><label class="form-label">Immatriculation</label><input type="text" name="immatriculation" class="form-control" value="<?= htmlspecialchars(
                                    $row['immatriculation']
                                ) ?>" required></div>
                                <div class="col-md-6"><label class="form-label">Année</label><input type="number" name="annee" class="form-control" value="<?= $row['annee'] ?>" required></div>
                                <div class="col-12"><div class="form-check"><input type="checkbox" name="disponibilite" value="1" class="form-check-input" <?= $row['disponibilite']
                                    ? 'checked'
                                    : '' ?>><label class="form-check-label">Disponible</label></div></div>
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
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Précédent</a></li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a></li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Suivant</a></li>
        </ul></nav>
    </div>
    <?php endif; ?>
</div>

<?php if (hasPermission('crud_vehicules')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><form method="POST">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-car-front-fill me-2"></i>Ajouter un véhicule</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="add">
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Marque</label><input type="text" name="marque" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Modèle</label><input type="text" name="modele" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Immatriculation</label><input type="text" name="immatriculation" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Année</label><input type="number" name="annee" class="form-control" value="<?= date('Y') ?>" required></div>
            </div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
    </form></div></div>
</div>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>
