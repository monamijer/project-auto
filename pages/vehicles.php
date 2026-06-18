<?php
/**
 * pages/vehicles.php — Véhicules
 * SELECT → v_vehicules | CRUD → sp_ajouter/modifier/supprimer_vehicule(), sp_basculer_disponibilite()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    requirePermission('crud_vehicules');
    $msg = callProcedure("CALL sp_ajouter_vehicule(?,?,?,?,@msg)",
        [trim($_POST['marque']), trim($_POST['modele']),
         trim($_POST['immatriculation']), isset($_POST['disponibilite']) ? 1 : 0]);
    $msg === 'OK' ? $message = 'Véhicule ajouté !' : $error = $msg;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit') {
    requirePermission('crud_vehicules');
    $msg = callProcedure("CALL sp_modifier_vehicule(?,?,?,?,?,@msg)",
        [(int)$_POST['id'], trim($_POST['marque']), trim($_POST['modele']),
         trim($_POST['immatriculation']), isset($_POST['disponibilite']) ? 1 : 0]);
    $msg === 'OK' ? $message = 'Véhicule modifié !' : $error = $msg;
}
if (isset($_GET['toggle'])) {
    requirePermission('crud_vehicules');
    callProcedure("CALL sp_basculer_disponibilite(?,@msg)", [(int)$_GET['toggle']]);
    $message = 'Disponibilité mise à jour !';
}
if (isset($_GET['delete'])) {
    requirePermission('crud_vehicules');
    $msg = callProcedure("CALL sp_supprimer_vehicule(?,@msg)", [(int)$_GET['delete']]);
    $msg === 'OK' ? $message = 'Véhicule supprimé !' : $error = 'Impossible : lié à des leçons.';
}

// ── READ via VIEW v_vehicules ─────────────────────────────────────────────
$vehicles = $pdo->query("SELECT * FROM v_vehicules ORDER BY marque")->fetchAll();

$pageTitle = 'Véhicules — Auto École Pro'; $dataTableId = 'vehiclesTable';
include BASE_PATH . '/includes/header.php';
?>
<div class="page-header">
    <h1 class="h2">Véhicules</h1>
    <?php if (hasPermission('crud_vehicules')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-car-front"></i> Ajouter
    </button>
    <?php endif; ?>
</div>
<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Mode lecture seule.</div><?php endif; ?>

<div class="card"><div class="card-body">
<table id="vehiclesTable" class="table table-striped">
<thead><tr><th>ID</th><th>Désignation</th><th>Immatriculation</th><th>Disponibilité</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($vehicles as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['designation']) ?></td>
    <td><?= htmlspecialchars($row['immatriculation']) ?></td>
    <td><span class="badge <?= $row['disponibilite'] ? 'bg-success':'bg-danger' ?>"><?= $row['disponibilite_label'] ?></span></td>
    <td>
        <?php if (hasPermission('crud_vehicules')): ?>
        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editModal-<?= $row['id'] ?>"><i class="bi bi-pencil"></i></button>
        <a href="?toggle=<?= $row['id'] ?>" class="btn btn-sm btn-info" title="Basculer dispo"><i class="bi bi-arrow-repeat"></i></a>
        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php if (hasPermission('crud_vehicules')): ?>
<div class="modal fade" id="editModal-<?= $row['id'] ?>" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Modifier</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="edit"><input type="hidden" name="id" value="<?= $row['id'] ?>">
      <div class="mb-3"><label class="form-label">Marque</label><input type="text" name="marque" class="form-control" value="<?= htmlspecialchars($row['marque']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Modèle</label><input type="text" name="modele" class="form-control" value="<?= htmlspecialchars($row['modele']) ?>" required></div>
      <div class="mb-3"><label class="form-label">Immatriculation</label><input type="text" name="immatriculation" class="form-control" value="<?= htmlspecialchars($row['immatriculation']) ?>" required></div>
      <div class="mb-3 form-check"><input type="checkbox" name="disponibilite" class="form-check-input" <?= $row['disponibilite'] ? 'checked':'' ?>><label class="form-check-label">Disponible</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php endforeach; ?>
</tbody></table>
</div></div>

<?php if (hasPermission('crud_vehicules')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Ajouter un véhicule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="add">
      <div class="mb-3"><label class="form-label">Marque</label><input type="text" name="marque" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Modèle</label><input type="text" name="modele" class="form-control" required></div>
      <div class="mb-3"><label class="form-label">Immatriculation</label><input type="text" name="immatriculation" class="form-control" required></div>
      <div class="mb-3 form-check"><input type="checkbox" name="disponibilite" class="form-check-input" checked><label class="form-check-label">Disponible</label></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Ajouter</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php include BASE_PATH . '/includes/footer.php'; ?>
