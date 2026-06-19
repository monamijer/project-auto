<?php
/**
 * pages/students.php — Élèves
 * SELECT → v_eleves, v_formations | CRUD → procédures stockées (soft delete)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add') {
    requirePermission('crud_eleves');
    $msg = callProcedure("CALL sp_ajouter_eleve(?,?,?,?,?,?,@msg)",
        [trim($_POST['nom']), trim($_POST['prenom']), trim($_POST['nationalite']),
         trim($_POST['email']), trim($_POST['telephone']), (int)$_POST['formation_id']]);
    $msg==='OK' ? $message='Élève ajouté !' : $error=$msg;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='edit') {
    requirePermission('crud_eleves');
    $msg = callProcedure("CALL sp_modifier_eleve(?,?,?,?,?,?,?,@msg)",
        [(int)$_POST['id'], trim($_POST['nom']), trim($_POST['prenom']),
         trim($_POST['nationalite']), trim($_POST['email']),
         trim($_POST['telephone']), (int)$_POST['formation_id']]);
    $msg==='OK' ? $message='Élève modifié !' : $error=$msg;
}
if (isset($_GET['delete'])) {
    requirePermission('crud_eleves');
    $msg = callProcedure("CALL sp_supprimer_eleve(?,?,@msg)",
        [(int)$_GET['delete'], $_SESSION['username']]);
    $msg==='OK' ? $message='Élève déplacé vers la corbeille.' : $error=$msg;
}

// ── READ via les VIEWS ───────────────────────────────────────────────────
$students   = $pdo->query("SELECT * FROM v_eleves ORDER BY date_inscription DESC")->fetchAll();
$formations = $pdo->query("SELECT * FROM v_formations")->fetchAll();

$pageTitle = 'Élèves — Auto École Pro'; $dataTableId = 'studentsTable';
$dataTableOpts = "pageLength: 25,";
include BASE_PATH . '/includes/header.php';
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-people-fill me-2 text-primary"></i>Gestion des Élèves</h1>
        <p class="text-muted mb-0"><?= count($students) ?> élève(s) inscrit(s)</p>
    </div>
    <?php if (hasPermission('crud_eleves')): ?>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-person-plus me-1"></i> Ajouter un élève
    </button>
    <?php endif; ?>
</div>

<!-- Alertes -->
<?php if ($message): ?>
<div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i>
    <?= htmlspecialchars($message) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    <?= htmlspecialchars($error) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if (!isAdmin()): ?>
<div class="alert alert-info d-flex align-items-center">
    <i class="bi bi-info-circle-fill me-2"></i>
    Mode lecture seule — vous ne pouvez pas modifier les données.
</div>
<?php endif; ?>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Liste des élèves</h5>
        <span class="badge bg-primary rounded-pill"><?= count($students) ?> total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="studentsTable" class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">#ID</th>
                        <th>Nom complet</th>
                        <th>Nationalité</th>
                        <th>Contact</th>
                        <th>Formation</th>
                        <th>Inscription</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($students as $row): ?>
                <tr>
                    <td class="ps-3">
                        <span class="badge bg-secondary bg-opacity-10 text-secondary fw-normal">
                            #<?= $row['id'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle me-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                <span class="text-primary fw-bold small">
                                    <?= strtoupper(substr($row['prenom'],0,1).substr($row['nom'],0,1)) ?>
                                </span>
                            </div>
                            <span class="fw-medium"><?= htmlspecialchars($row['nom_complet']) ?></span>
                        </div>
                    </td>
                    <td>
                        <?php if ($row['nationalite']): ?>
                            <span class="badge bg-light text-dark"><?= htmlspecialchars($row['nationalite']) ?></span>
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <small>
                            <div><i class="bi bi-envelope text-muted me-1"></i><?= htmlspecialchars($row['email']) ?></div>
                            <?php if ($row['telephone']): ?>
                            <div><i class="bi bi-telephone text-muted me-1"></i><?= htmlspecialchars($row['telephone']) ?></div>
                            <?php endif; ?>
                        </small>
                    </td>
                    <td>
                        <div class="fw-medium"><?= htmlspecialchars($row['formation_nom'] ?? '—') ?></div>
                        <small class="text-muted"><?= number_format($row['formation_prix']??0,2) ?> $</small>
                    </td>
                    <td>
                        <i class="bi bi-calendar3 text-muted me-1"></i>
                        <small><?= date('d/m/Y', strtotime($row['date_inscription'] ?? 'now')) ?></small>
                    </td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>" 
                               class="btn btn-outline-info" title="Voir le profil">
                                <i class="bi bi-eye"></i>
                            </a>
                            <?php if (hasPermission('crud_eleves')): ?>
                            <button class="btn btn-outline-warning" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editModal-<?= $row['id'] ?>"
                                    title="Modifier">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <a href="?delete=<?= $row['id'] ?>" 
                               class="btn btn-outline-danger"
                               onclick="return confirm('Déplacer cet élève vers la corbeille ?')"
                               title="Supprimer">
                                <i class="bi bi-trash"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <?php if (hasPermission('crud_eleves')): ?>
                <!-- Modal édition -->
                <div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <form method="POST">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title">
                                        <i class="bi bi-pencil-square me-2"></i>Modifier l'élève
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="action" value="edit">
                                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                    
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Prénom <span class="text-danger">*</span></label>
                                            <input type="text" name="prenom" class="form-control" 
                                                   value="<?= htmlspecialchars($row['prenom']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Nom <span class="text-danger">*</span></label>
                                            <input type="text" name="nom" class="form-control" 
                                                   value="<?= htmlspecialchars($row['nom']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Nationalité</label>
                                            <input type="text" name="nationalite" class="form-control" 
                                                   value="<?= htmlspecialchars($row['nationalite']??'') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                                            <input type="email" name="email" class="form-control" 
                                                   value="<?= htmlspecialchars($row['email']) ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Téléphone</label>
                                            <input type="text" name="telephone" class="form-control" 
                                                   value="<?= htmlspecialchars($row['telephone']??'') ?>">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-medium">Formation <span class="text-danger">*</span></label>
                                            <select name="formation_id" class="form-select" required>
                                                <?php foreach ($formations as $f): ?>
                                                <option value="<?= $f['id'] ?>" <?= $f['id']==$row['formation_id']?'selected':'' ?>>
                                                    <?= htmlspecialchars($f['label']) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check-lg me-1"></i>Enregistrer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (hasPermission('crud_eleves')): ?>
<!-- Modal ajout -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-person-plus me-2"></i>Ajouter un élève
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="prenom" class="form-control" 
                                   placeholder="Ex: Jean" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="nom" class="form-control" 
                                   placeholder="Ex: Dupont" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Nationalité</label>
                            <input type="text" name="nationalite" class="form-control" 
                                   placeholder="Ex: Française">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" 
                                   placeholder="jean.dupont@email.com" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Téléphone</label>
                            <input type="text" name="telephone" class="form-control" 
                                   placeholder="Ex: 06 12 34 56 78">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">Formation <span class="text-danger">*</span></label>
                            <select name="formation_id" class="form-select" required>
                                <option value="">-- Choisir une formation --</option>
                                <?php foreach ($formations as $f): ?>
                                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['label']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-lg me-1"></i>Ajouter l'élève
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>