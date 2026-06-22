<?php
/**
 * pages/exams.php — Examens
 * SELECT → v_examens_eligibles, v_stats_examens (Views SQL)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$eligible  = $pdo->query("SELECT * FROM v_examens_eligibles ORDER BY lecons_effectuees DESC")->fetchAll();
$examStats = $pdo->query("SELECT * FROM v_stats_examens")->fetchAll();

// Pagination
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
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <?php foreach ($examStats as $stat):
        $pct = $stat['total_eleves'] > 0 ? round(($stat['eligibles'] / $stat['total_eleves']) * 100) : 0;
    ?>
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
                <small class="text-muted"><?= $pct ?>% de réussite</small>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table -->
<div class="card shadow-sm border-0 mb-3">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Élèves éligibles (≥ 3 leçons)</h5>
        <span class="badge bg-success rounded-pill"><?= $totalEligible ?></span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Élève</th>
                        <th>Contact</th>
                        <th>Formation</th>
                        <th>Leçons</th>
                        <th>Statut</th>
                        <th class="text-end pe-3"></th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($eligiblePage)): ?>
                <tr><td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-emoji-neutral display-4 d-block mb-2"></i>Aucun élève éligible pour le moment
                </td></tr>
                <?php else: ?>
                <?php foreach ($eligiblePage as $row): ?>
                <tr>
                    <td><span class="fw-medium"><?= htmlspecialchars($row['nom_complet']) ?></span></td>
                    <td>
                        <small>
                            <div><?= htmlspecialchars($row['email']) ?></div>
                            <?php if ($row['telephone']): ?>
                            <div class="text-muted"><?= htmlspecialchars($row['telephone']) ?></div>
                            <?php endif; ?>
                        </small>
                    </td>
                    <td><?= htmlspecialchars($row['formation_nom']) ?></td>
                    <td><span class="badge bg-success bg-opacity-10 text-success"><?= $row['lecons_effectuees'] ?></span></td>
                    <td><span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check2 me-1"></i>Éligible</span></td>
                    <td class="text-end pe-3">
                        <a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($totalPages > 1): ?>
    <div class="card-footer bg-white">
        <nav>
            <ul class="pagination pagination-sm justify-content-center mb-0">
                <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page-1 ?>">Précédent</a>
                </li>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>
                <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page+1 ?>">Suivant</a>
                </li>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<!-- Info -->
<div class="card shadow-sm border-0">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="d-flex">
                    <i class="bi bi-info-circle text-primary me-3 fs-4"></i>
                    <div>
                        <strong>Conditions d'éligibilité</strong>
                        <ul class="mb-0 small text-muted">
                            <li>Minimum 3 leçons de conduite effectuées</li>
                            <li>Permis d'apprenti conducteur valide</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="d-flex">
                    <i class="bi bi-calendar text-warning me-3 fs-4"></i>
                    <div>
                        <strong>Calendrier des examens</strong>
                        <ul class="mb-0 small text-muted">
                            <li>Théorique : chaque lundi à 9h00</li>
                            <li>Pratique : chaque vendredi à 10h00</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>