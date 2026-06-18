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

// ── READ via la VIEW v_moniteurs ──────────────────────────────────────────
$instructors = $pdo->query("SELECT * FROM v_moniteurs ORDER BY nom")->fetchAll();

$pageTitle = 'Moniteurs — Auto École Pro'; $dataTableId = 'instructorsTable';
include BASE_PATH . '/includes/header.php';
?>
<div class="page-header">
    <h1 class="h2">Moniteurs</h1>
    <?php if (hasPermission('crud_moniteurs')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-person-badge-fill"></i> Ajouter
    </button>
    <?php endif; ?>
</div>
<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Mode lecture seule.</div><?php endif; ?>

<div class="card"><div class="card-body">
<table id="instructorsTable" class="table table-striped">
<thead><tr><th>ID</th><th>Nom complet</th><th>Nationalité</th><th>Téléphone</th><th>Expérience</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($instructors as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['nom_complet']) ?></td>
    <td><?= htmlspecialchars($row['nationalite'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['telephone'] ?? '') ?></td>
    <td><?= (int)$row['experience'] ?> ans</td>
    <td>
        <?php if (hasPermission('crud_moniteurs')): ?>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>
        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php if (hasPermission('crud_moniteurs')): ?>
<div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Modifier moniteur</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($row['prenom']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($row['nom']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Nationalité</label><input type="text" name="nationalite" class="form-control" value="<?= htmlspecialchars($row['nationalite'] ?? '') ?>"></div>
      <div class="mb-3"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($row['telephone'] ?? '') ?>"></div>
      <div class="mb-3"><label class="form-label">Expérience (ans)</label><input type="number" name="experience" class="form-control" value="<?= (int)$row['experience'] ?>" min="0" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table>
</div></div>

<?php if (hasPermission('crud_moniteurs')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Ajouter un moniteur</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="add">
      <div class="mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Nationalité</label><input type="text" name="nationalite" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Expérience (ans)</label><input type="number" name="experience" class="form-control" min="0" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php include BASE_PATH . '/includes/footer.php'; ?>
