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
        [(int)$_POST['student_id'], (float)$_POST['montant'],
         $_POST['date_paiement'], $_POST['methode']]);
    $msg === 'OK' ? $message = 'Paiement enregistré !' : $error = $msg;
}
if (isset($_GET['delete'])) {
    requirePermission('crud_paiements');
    $msg = callProcedure("CALL sp_supprimer_paiement(?,@msg)", [(int)$_GET['delete']]);
    $msg === 'OK' ? $message = 'Paiement supprimé.' : $error = $msg;
}

// ── READ via VIEW v_paiements ─────────────────────────────────────────────
$payments    = $pdo->query("SELECT * FROM v_paiements ORDER BY date_paiement DESC")->fetchAll();
$totalPercu  = $pdo->query("SELECT COALESCE(SUM(montant),0) FROM paiement")->fetchColumn();
$totalDu     = $pdo->query("SELECT COALESCE(SUM(f.prix),0) FROM utilisateurs u JOIN formations f ON u.formation_id=f.id")->fetchColumn();
$students    = $pdo->query("SELECT u.id, CONCAT(u.prenom,' ',u.nom,' — ',f.nom,' (',f.prix,' \$)') AS label FROM utilisateurs u JOIN formations f ON u.formation_id=f.id ORDER BY u.prenom")->fetchAll();

$pageTitle = 'Paiements — Auto École Pro'; $dataTableId = 'paymentsTable';
$dataTableOpts = "order: [[1,'desc']],";
include BASE_PATH . '/includes/header.php';
?>
<div class="page-header">
    <h1 class="h2">Paiements</h1>
    <?php if (hasPermission('crud_paiements')): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
        <i class="bi bi-cash-stack"></i> Enregistrer
    </button>
    <?php endif; ?>
</div>
<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if (!isAdmin()): ?><div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>Mode lecture seule.</div><?php endif; ?>

<!-- Cartes résumé -->
<div class="row mb-4">
    <div class="col-md-4"><div class="card text-white bg-success"><div class="card-body">
        <h6>Total perçu</h6><h3><?= number_format($totalPercu,2) ?> $</h3>
    </div></div></div>
    <div class="col-md-4"><div class="card text-white bg-info"><div class="card-body">
        <h6>Total attendu</h6><h3><?= number_format($totalDu,2) ?> $</h3>
    </div></div></div>
    <div class="col-md-4"><div class="card text-white bg-warning"><div class="card-body">
        <h6>Solde impayé</h6><h3><?= number_format(max(0,$totalDu-$totalPercu),2) ?> $</h3>
    </div></div></div>
</div>

<div class="card"><div class="card-body"><div class="table-responsive">
<table id="paymentsTable" class="table table-striped">
<thead><tr><th>ID</th><th>Date</th><th>Élève</th><th>Formation</th><th>Montant</th><th>Mode</th><th>Actions</th></tr></thead>
<tbody>
<?php foreach ($payments as $row): ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= htmlspecialchars($row['date_paiement']) ?></td>
    <td><?= htmlspecialchars($row['student_nom']) ?></td>
    <td><?= htmlspecialchars($row['formation_nom']) ?></td>
    <td><?= number_format($row['montant'],2) ?> $</td>
    <td><span class="badge bg-secondary"><?= htmlspecialchars($row['methode']) ?></span></td>
    <td>
        <?php if (hasPermission('crud_paiements')): ?>
        <a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
        <?php endif; ?>
    </td>
</tr>
<?php endforeach; ?>
</tbody></table>
</div></div></div>

<?php if (hasPermission('crud_paiements')): ?>
<div class="modal fade" id="addModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Enregistrer un paiement</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
      <input type="hidden" name="action" value="add">
      <div class="mb-3"><label class="form-label">Élève</label>
        <select name="student_id" class="form-select" required><option value="">-- Choisir --</option>
          <?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['label']) ?></option><?php endforeach; ?>
        </select></div>
      <div class="mb-3"><label class="form-label">Montant ($)</label>
        <input type="number" name="montant" class="form-control" min="1" step="0.01" required></div>
      <div class="mb-3"><label class="form-label">Date</label>
        <input type="date" name="date_paiement" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
      <div class="mb-3"><label class="form-label">Mode</label>
        <select name="methode" class="form-select">
          <option>Espèces</option><option>Mobile Money</option><option>Carte bancaire</option><option>Virement</option>
        </select></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
  </form></div></div>
</div>
<?php endif; ?>
<?php include BASE_PATH . '/includes/footer.php'; ?>
