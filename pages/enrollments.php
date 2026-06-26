<?php
/**
 * pages/enrollments.php — Inscriptions avec tri
 * SELECT → v_inscriptions, v_stats_formations (Views SQL)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$formationStats = $pdo->query('SELECT * FROM v_stats_formations ORDER BY id')->fetchAll();
$enrollments = $pdo->query('SELECT * FROM v_inscriptions ORDER BY date_inscription DESC')->fetchAll();

$perPage = 10;
$page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
$search = trim($_GET['search'] ?? '');
if ($search !== '') {
    $enrollments = array_filter($enrollments, function ($e) use ($search) {
        return stripos($e['nom_complet'], $search) !== false || stripos($e['formation_nom'], $search) !== false || stripos($e['email'], $search) !== false;
    });
    $enrollments = array_values($enrollments);
}
$total = count($enrollments);
$totalPages = ceil($total / $perPage);
$offset = ($page - 1) * $perPage;
$enrollmentsPage = array_slice($enrollments, $offset, $perPage);

$pageTitle = 'Inscriptions — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h3 mb-1"><i class="bi bi-journal-check me-2 text-primary"></i>Inscriptions</h1><p class="text-muted mb-0"><?= $total ?> inscription(s)</p></div>
    <?php if (hasPermission('crud_eleves')): ?><a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-primary shadow-sm"><i class="bi bi-person-plus me-1"></i>Inscrire</a><?php endif; ?>
</div>

<div class="row g-3 mb-4">
    <?php foreach ($formationStats as $stat):

        $attendu = $stat['total_eleves'] * $stat['formation_prix'];
        $pct = $attendu > 0 ? min(100, round(($stat['total_percu'] / $attendu) * 100)) : 0;
        ?>
    <div class="col-lg-4 col-md-6"><div class="card shadow-sm border-0 h-100"><div class="card-body">
        <h6 class="text-primary"><?= htmlspecialchars($stat['formation_nom']) ?></h6>
        <div class="row g-2 small"><div class="col-6"><span class="text-muted">Inscrits:</span> <strong><?= $stat[
            'total_eleves'
        ] ?></strong></div><div class="col-6"><span class="text-muted">Prix:</span> <strong><?= number_format($stat['formation_prix'], 2) ?> $</strong></div></div>
        <div class="d-flex justify-content-between mt-2"><small class="text-muted">Recouvrement</small><small class="fw-bold"><?= $pct ?>%</small></div>
        <div class="progress" style="height:6px;"><div class="progress-bar bg-success" style="width:<?= $pct ?>%;"></div></div>
    </div></div></div>
    <?php
    endforeach; ?>
</div>

<div class="card shadow-sm border-0 mb-3"><div class="card-body py-2"><form method="GET" class="row g-2 align-items-center">
    <div class="col-md-4"><div class="input-group input-group-sm"><span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span><input type="text" name="search" class="form-control" placeholder="Rechercher..." value="<?= htmlspecialchars(
        $search
    ) ?>"></div></div>
    <div class="col-auto"><button type="submit" class="btn btn-sm btn-outline-primary">Filtrer</button><?php if (
        $search
    ): ?><a href="?" class="btn btn-sm btn-outline-secondary">Réinitialiser</a><?php endif; ?></div>
</form></div></div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Inscriptions</h5><span class="badge bg-primary rounded-pill"><?= $total ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr>
            <th class="ps-3 sortable">Élève</th>
            <th class="sortable">Formation</th>
            <th class="sortable">Prix</th>
            <th class="sortable">Payé</th>
            <th>Solde</th>
            <th>Leçons</th>
            <th class="sortable">Inscription</th>
            <th class="text-end pe-3"></th>
        </tr></thead>
        <tbody>
        <?php if (empty($enrollmentsPage)): ?><tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-inbox display-4 d-block mb-2"></i>Aucune inscription</td></tr>
        <?php else:foreach ($enrollmentsPage as $row): ?>
        <tr>
            <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($row['nom_complet']) ?></span><small class="text-muted d-block"><?= htmlspecialchars($row['email']) ?></small></td>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($row['formation_nom']) ?></span></td>
            <td><?= number_format($row['formation_prix'], 2) ?> $</td>
            <td><?= number_format($row['total_paye'], 2) ?> $</td>
            <td><span class="badge <?= $row['solde_restant'] <= 0 ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?>"><?= number_format(
    max(0, $row['solde_restant']),
    2
) ?> $</span></td>
            <td><small><span class="text-success"><?= $row['lecons_effectuees'] ?> OK</span> / <span class="text-warning"><?= $row['lecons_programmees'] ?> prévues</span></small></td>
            <td><small><?= date('d/m/Y', strtotime($row['date_inscription'] ?? 'now')) ?></small></td>
            <td class="text-end pe-3"><a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a></td>
        </tr>
        <?php endforeach;endif; ?>
        </tbody>
    </table></div></div>
    <?php if ($totalPages > 1): ?><div class="card-footer bg-white"><nav><ul class="pagination pagination-sm justify-content-center mb-0">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">Précédent</a></li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?><li class="page-item <?= $i === $page ? 'active' : '' ?>"><a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode(
    $search
) ?>"><?= $i ?></a></li><?php endfor; ?>
        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>"><a class="page-link" href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">Suivant</a></li>
    </ul></nav></div><?php endif; ?>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
