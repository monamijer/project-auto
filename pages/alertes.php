<?php
/**
 * pages/alertes.php — Alertes automatiques (#37)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$impayes = $pdo->query("SELECT * FROM v_alertes_impayes")->fetchAll();
$comptesExpires = $pdo->query("SELECT * FROM v_alertes_comptes")->fetchAll();
$prochainesLecons = $pdo->query("SELECT * FROM v_alertes_lecons_24h")->fetchAll();
$vehiculesIndispos = $pdo->query("SELECT * FROM v_alertes_vehicules")->fetchAll();
$total = count($impayes) + count($comptesExpires) + count($prochainesLecons) + count($vehiculesIndispos);

$pageTitle = "Alertes — Auto École Pro";
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div><h1 class="h4 mb-1"><i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>Alertes</h1><p class="text-muted mb-0"><?= $total ?> alerte(s)</p></div>
    <a href="<?= BASE_URL ?>/index.php" class="btn btn-sm btn-outline-secondary">← Tableau de bord</a>
</div>

<?php if(empty($impayes) && empty($comptesExpires) && empty($prochainesLecons) && empty($vehiculesIndispos)): ?>
<div class="text-center py-5"><i class="bi bi-check-circle display-1 text-success d-block mb-3"></i><h5>Aucune alerte</h5><p class="text-muted">Tout est en ordre.</p></div>
<?php else: ?>

<?php if(!empty($impayes)): ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-danger bg-opacity-10 py-3 d-flex justify-content-between"><h5 class="mb-0 text-danger"><i class="bi bi-cash-stack me-2"></i>Soldes impayés</h5><span class="badge bg-danger"><?= count($impayes) ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Élève</th><th>Solde</th><th></th></tr></thead><tbody>
    <?php foreach($impayes as $r): ?><tr><td class="ps-3 fw-medium"><?= htmlspecialchars($r['nom']) ?></td><td><span class="badge bg-danger"><?= number_format($r['solde'],2) ?> $</span></td><td><a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-danger">Profil</a></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>
<?php endif; ?>

<?php if(!empty($comptesExpires)): ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-warning bg-opacity-10 py-3 d-flex justify-content-between"><h5 class="mb-0 text-warning"><i class="bi bi-person-x me-2"></i>Comptes expirés</h5><span class="badge bg-warning text-dark"><?= count($comptesExpires) ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Compte</th><th>Expiration</th><th></th></tr></thead><tbody>
    <?php foreach($comptesExpires as $r): ?><tr><td class="ps-3 fw-medium"><?= htmlspecialchars($r['utilisateur']) ?></td><td><?= htmlspecialchars($r['date_expiration']) ?></td><td><a href="<?= BASE_URL ?>/pages/settings.php" class="btn btn-sm btn-outline-warning">Renouveler</a></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>
<?php endif; ?>

<?php if(!empty($prochainesLecons)): ?>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-info bg-opacity-10 py-3 d-flex justify-content-between"><h5 class="mb-0 text-info"><i class="bi bi-calendar-event me-2"></i>Leçons dans 24h</h5><span class="badge bg-info"><?= count($prochainesLecons) ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Heure</th><th>Élève</th><th>Moniteur</th></tr></thead><tbody>
    <?php foreach($prochainesLecons as $r): ?><tr><td class="ps-3 fw-medium"><?= date('d/m H:i', strtotime($r['date_lecon'])) ?></td><td><?= htmlspecialchars($r['student_nom']) ?></td><td><?= htmlspecialchars($r['instructor_nom']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>
<?php endif; ?>

<?php if(!empty($vehiculesIndispos)): ?>
<div class="card shadow-sm border-0">
    <div class="card-header bg-secondary bg-opacity-10 py-3 d-flex justify-content-between"><h5 class="mb-0"><i class="bi bi-car-front me-2"></i>Véhicules indisponibles</h5><span class="badge bg-secondary"><?= count($vehiculesIndispos) ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Véhicule</th><th>Immatriculation</th></tr></thead><tbody>
    <?php foreach($vehiculesIndispos as $r): ?><tr><td class="ps-3 fw-medium"><?= htmlspecialchars($r['designation']) ?></td><td><?= htmlspecialchars($r['immatriculation']) ?></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>
<?php endif; ?>

<?php endif; ?>
<?php include BASE_PATH . '/includes/footer.php'; ?>