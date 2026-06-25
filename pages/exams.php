<?php
/**
 * pages/exams.php — Suivi des examens et résultats
 * SELECT → v_examens_eligibles, v_stats_examens, v_resultats_examens (Views SQL)
 * CRUD → sp_enregistrer_resultat()
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_resultat') {
    requirePermission('crud_eleves');
    $msg = callProcedure("CALL sp_enregistrer_resultat(?,?,?,?,?,?,?,?,@msg)", [
        (int)$_POST['utilisateur_id'],
        $_POST['type_examen'],
        $_POST['date_examen'],
        $_POST['resultat'],
        (int)$_POST['note'],
        trim($_POST['centre_examen'] ?? ''),
        trim($_POST['commentaire'] ?? ''),
        $_SESSION['username']
    ]);
    if ($msg === 'OK') {
        $message = 'Résultat enregistré !';
        logActivity('AJOUT', 'examens', (int)$_POST['utilisateur_id']);
    } else {
        $error = $msg;
    }
}

$eligible = $pdo->query('SELECT * FROM v_examens_eligibles ORDER BY lecons_effectuees DESC')->fetchAll();
$examStats = $pdo->query('SELECT * FROM v_stats_examens')->fetchAll();
$resultats = $pdo->query('SELECT * FROM v_resultats_examens ORDER BY date_examen DESC LIMIT 50')->fetchAll();
$tousEleves = $pdo->query("SELECT id, CONCAT(prenom, ' ', nom) AS nom_complet FROM utilisateurs WHERE deleted_at IS NULL ORDER BY nom")->fetchAll();

$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$totalEligible = count($eligible);
$totalPages = ceil($totalEligible / $perPage);
$offset = ($page - 1) * $perPage;
$eligiblePage = array_slice($eligible, $offset, $perPage);

$pageTitle = 'Examens — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-clipboard-check me-2 text-primary"></i>Suivi des Examens</h1>
        <p class="text-muted mb-0"><?= $totalEligible ?> élève(s) éligible(s)</p>
    </div>
    <?php if (hasPermission('crud_eleves')): ?>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addResultModal">
        <i class="bi bi-plus-lg me-1"></i>Enregistrer un résultat
    </button>
    <?php endif; ?>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($examStats as $stat):
        $pct = $stat['total_eleves'] > 0 ? round(($stat['eligibles'] / $stat['total_eleves']) * 100) : 0; ?>
    <div class="col-lg-4 col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="card-title"><?= htmlspecialchars($stat['formation_nom']) ?></h6>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted small">Éligibles</span>
                    <strong><?= $stat['eligibles'] ?> / <?= $stat['total_eleves'] ?></strong>
                </div>
                <div class="progress" style="height:6px;">
                    <div class="progress-bar bg-success" style="width:<?= $pct ?>%;"></div>
                </div>
                <small class="text-muted"><?= $pct ?>% d'éligibilité</small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Élèves éligibles -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Élèves éligibles (≥ 3 leçons)</h5>
        <span class="badge bg-success rounded-pill"><?= $totalEligible ?></span>
    </div>
    <div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Élève</th><th>Contact</th><th>Formation</th><th>Leçons</th><th class="text-end pe-3">Profil</th></tr></thead>
            <tbody>
            <?php if (empty($eligiblePage)): ?>
            <tr><td colspan="5" class="text-center py-5 text-muted"><i class="bi bi-emoji-neutral display-4 d-block mb-2"></i>Aucun élève éligible</td></tr>
            <?php else: foreach ($eligiblePage as $row): ?>
            <tr>
                <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($row['nom_complet']) ?></span></td>
                <td><small><div><?= htmlspecialchars($row['email']) ?></div><?php if ($row['telephone']): ?><div class="text-muted"><?= htmlspecialchars($row['telephone']) ?></div><?php endif; ?></small></td>
                <td><?= htmlspecialchars($row['formation_nom']) ?></td>
                <td><span class="badge bg-success bg-opacity-10 text-success"><?= $row['lecons_effectuees'] ?></span></td>
                <td class="text-end pe-3"><a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div></div>
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white"><nav><ul class="pagination pagination-sm justify-content-center mb-0">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>">Précédent</a></li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?><li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a></li><?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>">Suivant</a></li>
    </ul></nav></div>
    <?php endif; ?>
</div>

<!-- Résultats enregistrés -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Résultats d'examens enregistrés</h5></div>
    <div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th class="ps-3">Date</th><th>Élève</th><th>Type</th><th>Résultat</th><th>Note</th><th>Centre</th><th>Commentaire</th></tr></thead>
            <tbody>
            <?php if (empty($resultats)): ?>
            <tr><td colspan="7" class="text-center py-4 text-muted">Aucun résultat enregistré</td></tr>
            <?php else: foreach ($resultats as $r): ?>
            <tr>
                <td class="ps-3"><small><?= date('d/m/Y', strtotime($r['date_examen'])) ?></small></td>
                <td><span class="fw-medium"><?= htmlspecialchars($r['nom_complet']) ?></span></td>
                <td><span class="badge bg-light text-dark"><?= $r['type_examen'] === 'theorique' ? 'Théorique' : 'Pratique' ?></span></td>
                <td><span class="badge <?= $r['resultat'] === 'reussi' ? 'bg-success' : 'bg-danger' ?>"><?= $r['resultat'] === 'reussi' ? 'Réussi' : 'Échoué' ?></span></td>
                <td><?= $r['note'] ? $r['note'].'/100' : '—' ?></td>
                <td><small><?= htmlspecialchars($r['centre_examen'] ?? '—') ?></small></td>
                <td><small class="text-muted"><?= htmlspecialchars($r['commentaire'] ?? '—') ?></small></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div></div>
</div>

<!-- Info -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex"><i class="bi bi-info-circle text-primary me-3 fs-4"></i>
                    <div><strong>Conditions d'éligibilité</strong><ul class="mb-0 small text-muted"><li>Minimum 3 leçons effectuées</li><li>Permis d'apprenti valide</li></ul></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex"><i class="bi bi-calendar text-warning me-3 fs-4"></i>
                    <div><strong>Calendrier des examens</strong><ul class="mb-0 small text-muted"><li>Théorique : chaque lundi à 9h00</li><li>Pratique : chaque vendredi à 10h00</li></ul></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal ajout résultat -->
<?php if (hasPermission('crud_eleves')): ?>
<div class="modal fade" id="addResultModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-plus-lg me-2"></i>Enregistrer un résultat</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="add_resultat">
        <div class="mb-3">
            <label class="form-label">Élève</label>
            <select name="utilisateur_id" class="form-select" required>
                <option value="">-- Choisir un élève --</option>
                <?php foreach ($tousEleves as $e): ?>
                <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nom_complet']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3"><label class="form-label">Type d'examen</label><select name="type_examen" class="form-select" required><option value="theorique">Théorique</option><option value="pratique">Pratique</option></select></div>
        <div class="mb-3"><label class="form-label">Date</label><input type="date" name="date_examen" class="form-control" value="<?= date('Y-m-d') ?>" required></div>
        <div class="mb-3"><label class="form-label">Résultat</label><select name="resultat" class="form-select" required><option value="reussi">Réussi</option><option value="echoue">Échoué</option></select></div>
        <div class="mb-3"><label class="form-label">Note (/100)</label><input type="number" name="note" class="form-control" min="0" max="100"></div>
        <div class="mb-3"><label class="form-label">Centre d'examen</label><input type="text" name="centre_examen" class="form-control"></div>
        <div class="mb-3"><label class="form-label">Commentaire</label><textarea name="commentaire" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
</form></div></div></div>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>