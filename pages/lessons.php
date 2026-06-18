<?php
/**
 * pages/lessons.php — Leçons
 * SELECT → v_lecons | CRUD → sp_planifier/completer/annuler/supprimer_lecon()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add') {
    requirePermission('crud_lecons');
    $msg = callProcedure("CALL sp_planifier_lecon(?,?,?,?,@msg)",
        [(int)$_POST['student_id'], (int)$_POST['instructor_id'],
         (int)$_POST['vehicle_id'], $_POST['date_lecon']]);
    $msg === 'OK' ? $message = 'Leçon planifiée !' : $error = $msg;
}
if (isset($_GET['complete'])) {
    requirePermission('crud_lecons');
    callProcedure("CALL sp_completer_lecon(?,@msg)", [(int)$_GET['complete']]);
    $message = 'Leçon marquée effectuée !';
}
if (isset($_GET['cancel'])) {
    requirePermission('crud_lecons');
    callProcedure("CALL sp_annuler_lecon(?,@msg)", [(int)$_GET['cancel']]);
    $message = 'Leçon annulée.';
}
if (isset($_GET['delete'])) {
    requirePermission('crud_lecons');
    callProcedure("CALL sp_supprimer_lecon(?,@msg)", [(int)$_GET['delete']]);
    $message = 'Leçon supprimée.';
}

// ── READ via VIEW v_lecons ─────────────────────────────────────────────────
$lessons     = $pdo->query("SELECT * FROM v_lecons ORDER BY date_lecon DESC")->fetchAll();
$students    = $pdo->query("SELECT id, CONCAT(prenom,' ',nom) AS nom_complet FROM utilisateurs ORDER BY prenom")->fetchAll();
$instructors = $pdo->query("SELECT id, CONCAT(prenom,' ',nom) AS nom_complet FROM instructeurs ORDER BY prenom")->fetchAll();
$vehicles    = $pdo->query("SELECT id, CONCAT(marque,' ',modele,' (',immatriculation,')') AS label FROM vehicules WHERE disponibilite=1 ORDER BY marque")->fetchAll();

$pageTitle = 'Leçons — Auto École Pro'; $dataTableId = 'lessonsTable';
$dataTableOpts = "order: [[1,'desc']],";
include BASE_PATH . '/includes/header.php';
?>
<div class="page-header">
    <h1 class="h2">Leçons</h1>
    <?php if (hasPermission('crud_lecons')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-calendar-plus"></i> Planifier
    </button>
    <?php endif; ?>
</div>
<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Mode lecture seule.</div><?php endif; ?>

<div class="card"><div class="card-body"><div class="table-responsive">
<table id="lessonsTable" class="table table-striped">
<thead><tr><th>ID</th><th>Date</th><th>Élève</th><th>Moniteur</th><th>Véhicule</th><th>Statut</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($lessons as $row):
    $badge = ($row['statut']==='effectuée') ? 'bg-success' : (($row['statut']==='annulée') ? 'bg-danger' : 'bg-warning text-dark');
?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= date('d/m/Y H:i', strtotime($row['date_lecon'])) ?></td>
    <td><?= htmlspecialchars($row['student_nom']) ?><br><small class="text-muted"><?= htmlspecialchars($row['formation_nom']) ?></small></td>
    <td><?= htmlspecialchars($row['instructor_nom']) ?></td>
    <td><?= htmlspecialchars($row['vehicle_nom']) ?></td>
    <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($row['statut']) ?></span></td>
    <td>
        <?php if (hasPermission('crud_lecons') && $row['statut']==='programmée'): ?>
        <a href="?complete=<?= $row['id'] ?>" class="btn btn-sm btn-success" onclick="return confirm('Marquer effectuée ?')"><i class="bi bi-check-lg"></i></a>
        <a href="?cancel=<?= $row['id'] ?>"   class="btn btn-sm btn-warning" onclick="return confirm('Annuler ?')"><i class="bi bi-x-lg"></i></a>
        <a href="?delete=<?= $row['id'] ?>"   class="btn btn-sm btn-danger"  onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div></div>

<?php if (hasPermission('crud_lecons')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Planifier une leçon</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="add">
      <div class="mb-3"><label class="form-label">Élève</label>
        <select name="student_id" class="form-select" required><option value="">-- Choisir --</option>
          <?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="mb-3"><label class="form-label">Moniteur</label>
        <select name="instructor_id" class="form-select" required><option value="">-- Choisir --</option>
          <?php foreach ($instructors as $i): ?><option value="<?= $i['id'] ?>"><?= htmlspecialchars($i['nom_complet']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="mb-3"><label class="form-label">Véhicule (disponibles)</label>
        <select name="vehicle_id" class="form-select" required><option value="">-- Choisir --</option>
          <?php foreach ($vehicles as $v): ?><option value="<?= $v['id'] ?>"><?= htmlspecialchars($v['label']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="mb-3"><label class="form-label">Date et heure</label>
        <input type="datetime-local" name="date_lecon" class="form-control" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Planifier</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php include BASE_PATH . '/includes/footer.php'; ?>
