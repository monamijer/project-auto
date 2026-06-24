<?php
/**
 * pages/corbeille.php — Corbeille (admin uniquement)
 * SELECT → v_corbeille_eleves, v_corbeille_moniteurs, v_corbeille_vehicules
 * Actions → sp_restaurer_* / sp_supprimer_*_definitif
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('gestion_comptes');

$message = '';
$error = '';

if (isset($_GET['restaurer']) && isset($_GET['type'])) {
    $id = (int) $_GET['restaurer'];
    $msg = match ($_GET['type']) {
        'eleve' => callProcedure('CALL sp_restaurer_eleve(?,@msg)', [$id]),
        'moniteur' => callProcedure('CALL sp_restaurer_moniteur(?,@msg)', [$id]),
        'vehicule' => callProcedure('CALL sp_restaurer_vehicule(?,@msg)', [$id]),
        default => 'Type invalide',
    };
    $msg === 'OK' ? ($message = 'Élément restauré avec succès !') : ($error = $msg);
}

if (isset($_GET['purger']) && isset($_GET['type'])) {
    $id = (int) $_GET['purger'];
    $msg = match ($_GET['type']) {
        'eleve' => callProcedure('CALL sp_supprimer_eleve_definitif(?,@msg)', [$id]),
        'moniteur' => callProcedure('CALL sp_supprimer_moniteur_definitif(?,@msg)', [$id]),
        'vehicule' => callProcedure('CALL sp_supprimer_vehicule_definitif(?,@msg)', [$id]),
        default => 'Type invalide',
    };
    $msg === 'OK' ? ($message = 'Supprimé définitivement.') : ($error = 'Impossible : données liées.');
}

$eleves = $pdo->query('SELECT * FROM v_corbeille_eleves')->fetchAll();
$moniteurs = $pdo->query('SELECT * FROM v_corbeille_moniteurs')->fetchAll();
$vehicules = $pdo->query('SELECT * FROM v_corbeille_vehicules')->fetchAll();

$totalElements = count($eleves) + count($moniteurs) + count($vehicules);
$activeTab = $_GET['tab'] ?? 'eleves';

$pageTitle = 'Corbeille — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-3">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-trash3 me-2 text-danger"></i>Corbeille</h1>
        <p class="text-muted mb-0 small">Restaurer ou supprimer définitivement les éléments</p>
    </div>
    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
        <i class="bi bi-archive me-1"></i><?= $totalElements ?> élément(s)
    </span>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars(
    $message
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars(
    $error
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="alert alert-warning d-flex align-items-center py-2 mb-3 small">
    <i class="bi bi-exclamation-triangle me-2"></i>
    La suppression définitive est <pre> </pre> <strong> irréversible</strong>. Restaurez si nécessaire.
</div>

<!-- Tabs -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'eleves' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-eleves">
            <i class="bi bi-people me-1"></i>Élèves <span class="badge bg-secondary ms-1"><?= count($eleves) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'moniteurs' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-moniteurs">
            <i class="bi bi-person-badge me-1"></i>Moniteurs <span class="badge bg-secondary ms-1"><?= count($moniteurs) ?></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $activeTab === 'vehicules' ? 'active' : '' ?>" data-bs-toggle="tab" href="#tab-vehicules">
            <i class="bi bi-car-front me-1"></i>Véhicules <span class="badge bg-secondary ms-1"><?= count($vehicules) ?></span>
        </a>
    </li>
</ul>

<div class="tab-content">
    <!-- Élèves -->
    <div class="tab-pane fade <?= $activeTab === 'eleves' ? 'show active' : '' ?>" id="tab-eleves">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <?php if (empty($eleves)): ?>
                <div class="text-center py-5 text-muted"><i class="bi bi-archive fs-1 d-block mb-2"></i>Aucun élève dans la corbeille</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Élève</th><th>Email</th><th>Formation</th><th>Supprimé le</th><th>Par</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($eleves as $e): ?>
                        <tr>
                            <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($e['nom_complet']) ?></span></td>
                            <td><small><?= htmlspecialchars($e['email']) ?></small></td>
                            <td><small><?= htmlspecialchars($e['formation_nom'] ?? '—') ?></small></td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($e['deleted_at'])) ?></small></td>
                            <td><small><?= htmlspecialchars($e['deleted_by'] ?? '—') ?></small></td>
                            <td class="text-end pe-3">
                                <a href="?restaurer=<?= $e['id'] ?>&type=eleve" class="btn btn-sm btn-outline-success" title="Restaurer"><i class="bi bi-arrow-counterclockwise"></i></a>
                                <a href="?purger=<?= $e[
                                    'id'
                                ] ?>&type=eleve" class="btn btn-sm btn-outline-danger" title="Supprimer définitivement" onclick="return confirm('⚠️ Suppression DÉFINITIVE. Continuer ?')"><i class="bi bi-x-octagon"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Moniteurs -->
    <div class="tab-pane fade <?= $activeTab === 'moniteurs' ? 'show active' : '' ?>" id="tab-moniteurs">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <?php if (empty($moniteurs)): ?>
                <div class="text-center py-5 text-muted"><i class="bi bi-archive fs-1 d-block mb-2"></i>Aucun moniteur dans la corbeille</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Moniteur</th><th>Téléphone</th><th>Supprimé le</th><th>Par</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($moniteurs as $m): ?>
                        <tr>
                            <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($m['nom_complet']) ?></span></td>
                            <td><small><?= $m['telephone'] ? htmlspecialchars($m['telephone']) : '—' ?></small></td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($m['deleted_at'])) ?></small></td>
                            <td><small><?= htmlspecialchars($m['deleted_by'] ?? '—') ?></small></td>
                            <td class="text-end pe-3">
                                <a href="?restaurer=<?= $m['id'] ?>&type=moniteur" class="btn btn-sm btn-outline-success" title="Restaurer"><i class="bi bi-arrow-counterclockwise"></i></a>
                                <a href="?purger=<?= $m[
                                    'id'
                                ] ?>&type=moniteur" class="btn btn-sm btn-outline-danger" title="Supprimer définitivement" onclick="return confirm('⚠️ Suppression DÉFINITIVE. Continuer ?')"><i class="bi bi-x-octagon"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Véhicules -->
    <div class="tab-pane fade <?= $activeTab === 'vehicules' ? 'show active' : '' ?>" id="tab-vehicules">
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <?php if (empty($vehicules)): ?>
                <div class="text-center py-5 text-muted"><i class="bi bi-archive fs-1 d-block mb-2"></i>Aucun véhicule dans la corbeille</div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light"><tr><th class="ps-3">Véhicule</th><th>Immatriculation</th><th>Supprimé le</th><th>Par</th><th class="text-end pe-3">Actions</th></tr></thead>
                        <tbody>
                        <?php foreach ($vehicules as $v): ?>
                        <tr>
                            <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($v['designation']) ?></span></td>
                            <td><code class="bg-light px-2 py-1 rounded small"><?= htmlspecialchars($v['immatriculation']) ?></code></td>
                            <td><small class="text-muted"><?= date('d/m/Y H:i', strtotime($v['deleted_at'])) ?></small></td>
                            <td><small><?= htmlspecialchars($v['deleted_by'] ?? '—') ?></small></td>
                            <td class="text-end pe-3">
                                <a href="?restaurer=<?= $v['id'] ?>&type=vehicule" class="btn btn-sm btn-outline-success" title="Restaurer"><i class="bi bi-arrow-counterclockwise"></i></a>
                                <a href="?purger=<?= $v[
                                    'id'
                                ] ?>&type=vehicule" class="btn btn-sm btn-outline-danger" title="Supprimer définitivement" onclick="return confirm('⚠️ Suppression DÉFINITIVE. Continuer ?')"><i class="bi bi-x-octagon"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
