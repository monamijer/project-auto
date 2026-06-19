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
// Soft delete : passe vers la Corbeille
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
<div class="page-header">
    <h1 class="h2">Gestion des Élèves</h1>
    <?php if (hasPermission('crud_eleves')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-person-plus"></i> Ajouter
    </button>
    <?php endif; ?>
</div>
<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Mode lecture seule.</div><?php endif; ?>

<div class="card"><div class="card-body"><div class="table-responsive">
<table id="studentsTable" class="table table-striped">
<thead><tr><th>ID</th><th>Nom complet</th><th>Nationalité</th><th>Email</th><th>Téléphone</th><th>Formation</th><th>Inscription</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($students as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['nom_complet']) ?></td>
    <td><?= htmlspecialchars($row['nationalite'] ?? '') ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td><?= htmlspecialchars($row['telephone'] ?? '') ?></td>
    <td><span class="badge bg-info"><?= htmlspecialchars($row['formation_nom'] ?? '—') ?></span>
        <small class="text-muted">(<?= number_format($row['formation_prix']??0,2) ?> $)</small></td>
    <td><?= htmlspecialchars($row['date_inscription'] ?? '') ?></td>
    <td>
        <?php if (hasPermission('crud_eleves')): ?>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
        <?php if (hasPermission('crud_eleves')): ?>
        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Déplacer vers la corbeille ?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php if (hasPermission('crud_eleves')): ?>
<div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Modifier l'élève</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" value="<?= htmlspecialchars($row['prenom']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" value="<?= htmlspecialchars($row['nom']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Nationalité</label><input type="text" name="nationalite" class="form-control" value="<?= htmlspecialchars($row['nationalite']??'') ?>"></div>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?= htmlspecialchars($row['email']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control" value="<?= htmlspecialchars($row['telephone']??'') ?>"></div>
      <div class="mb-3"><label class="form-label">Formation</label>
        <select name="formation_id" class="form-select" required>
          <?php foreach ($formations as $f): ?>
          <option value="<?= $f['id'] ?>" <?= $f['id']==$row['formation_id']?'selected':'' ?>><?= htmlspecialchars($f['label']) ?></option>
          <?php endforeach; ?>
        </select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table>
</div></div></div>

<?php if (hasPermission('crud_eleves')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Ajouter un élève</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="add">
      <div class="mb-3"><label class="form-label">Prénom</label><input type="text" name="prenom" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Nom</label><input type="text" name="nom" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Nationalité</label><input type="text" name="nationalite" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Téléphone</label><input type="text" name="telephone" class="form-control"></div>
      <div class="mb-3"><label class="form-label">Formation</label>
        <select name="formation_id" class="form-select" required><option value="">-- Choisir --</option>
          <?php foreach ($formations as $f): ?><option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['label']) ?></option><?php endforeach; ?>
        </select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php include BASE_PATH . '/includes/footer.php'; ?>
