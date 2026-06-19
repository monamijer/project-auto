<?php
/**
 * pages/student_profile.php — Profil complet d'un élève
 * SELECT via requêtes préparées sécurisées
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM v_eleves WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: ' . BASE_URL . '/pages/students.php');
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM v_paiements WHERE utilisateur_id = ? ORDER BY date_paiement DESC");
$stmt->execute([$studentId]);
$payments = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT * FROM v_lecons WHERE utilisateur_id = ? ORDER BY date_lecon DESC");
$stmt->execute([$studentId]);
$lessons = $stmt->fetchAll();

$totalPaye = array_sum(array_column($payments, 'montant'));
$solde = $student['formation_prix'] - $totalPaye;
$leconEffectuees = count(array_filter($lessons, fn($l) => $l['statut'] === 'effectuée'));
$leconPlanifiees = count(array_filter($lessons, fn($l) => $l['statut'] === 'planifiée'));
$leconAnnulees = count(array_filter($lessons, fn($l) => $l['statut'] === 'annulée'));
$totalLecons = count($lessons);
$pctPaye = $student['formation_prix'] > 0 ? min(100, round(($totalPaye / $student['formation_prix']) * 100)) : 0;
$pctProgression = $student['formation_duree_mois'] > 0 ? min(100, round(($leconEffectuees / max(1, $student['formation_duree_mois'] * 4)) * 100)) : 0;

$statut = 'Actif';
$statutBadge = 'success';
if ($solde <= 0 && $leconEffectuees >= 3) {
    $statut = 'Éligible examen';
    $statutBadge = 'primary';
} elseif ($solde > $student['formation_prix'] * 0.5) {
    $statut = 'Paiement en retard';
    $statutBadge = 'danger';
}

$pageTitle = 'Profil : ' . $student['prenom'] . ' ' . $student['nom'];
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/students.php">Élèves</a></li><li class="breadcrumb-item active"><?= htmlspecialchars($student['nom_complet']) ?></li></ol></nav>
        <h1 class="h3 mb-0"><i class="bi bi-person-badge me-2"></i><?= htmlspecialchars($student['nom_complet']) ?> <span class="badge bg-<?= $statutBadge ?> bg-opacity-10 text-<?= $statutBadge ?> ms-2"><?= $statut ?></span></h1>
    </div>
    <div class="btn-group">
        <a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Retour</a>
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0"><div class="card-body">
            <h6 class="text-muted small mb-2">Progression</h6>
            <div class="d-flex justify-content-between align-items-end mb-2"><h3 class="mb-0 fw-bold"><?= $pctProgression ?>%</h3><i class="bi bi-graph-up text-primary fs-4"></i></div>
            <div class="progress" style="height:6px;"><div class="progress-bar bg-primary" style="width:<?= $pctProgression ?>%;"></div></div>
            <small class="text-muted"><?= $leconEffectuees ?> leçons</small>
        </div></div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0"><div class="card-body">
            <h6 class="text-muted small mb-2">Paiement</h6>
            <div class="d-flex justify-content-between align-items-end mb-2"><h3 class="mb-0 fw-bold"><?= $pctPaye ?>%</h3><i class="bi bi-cash-stack text-success fs-4"></i></div>
            <div class="progress" style="height:6px;"><div class="progress-bar bg-success" style="width:<?= $pctPaye ?>%;"></div></div>
            <small class="text-muted"><?= number_format($totalPaye, 2) ?> $ / <?= number_format($student['formation_prix'], 2) ?> $</small>
        </div></div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0"><div class="card-body">
            <h6 class="text-muted small mb-2">Leçons</h6>
            <h3 class="mb-0 fw-bold"><?= $totalLecons ?></h3>
            <small><span class="text-success"><?= $leconEffectuees ?> OK</span> · <span class="text-warning"><?= $leconPlanifiees ?> prévues</span> · <span class="text-danger"><?= $leconAnnulees ?> annulées</span></small>
        </div></div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-sm border-0"><div class="card-body">
            <h6 class="text-muted small mb-2">Solde</h6>
            <h3 class="mb-0 fw-bold <?= $solde <= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format(max(0, $solde), 2) ?> $</h3>
            <small class="<?= $solde <= 0 ? 'text-success' : 'text-danger' ?>"><?= $solde <= 0 ? 'Payé' : 'En attente' ?></small>
        </div></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body">
                <h5 class="card-title small fw-bold text-muted mb-3">INFORMATIONS</h5>
                <div class="text-center mb-3">
                    <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:64px;height:64px;">
                        <span class="text-primary fw-bold fs-5"><?= strtoupper(substr($student['prenom'], 0, 1) . substr($student['nom'], 0, 1)) ?></span>
                    </div>
                </div>
                <div class="small"><i class="bi bi-calendar-date text-muted me-2"></i>Inscrit le <?= date('d/m/Y', strtotime($student['date_inscription'] ?? 'now')) ?></div>
                <div class="small mt-2"><i class="bi bi-flag text-muted me-2"></i><?= htmlspecialchars($student['nationalite'] ?? 'N/A') ?></div>
                <div class="small mt-2"><i class="bi bi-envelope text-muted me-2"></i><?= htmlspecialchars($student['email']) ?></div>
                <div class="small mt-2"><i class="bi bi-telephone text-muted me-2"></i><?= htmlspecialchars($student['telephone'] ?? 'Non renseigné') ?></div>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title small fw-bold text-muted mb-3">FORMATION</h5>
                <strong><?= htmlspecialchars($student['formation_nom']) ?></strong>
                <div class="row g-2 mt-2">
                    <div class="col-6"><div class="p-2 bg-light rounded-3"><small class="text-muted d-block">Durée</small><strong><?= $student['formation_duree_mois'] ?> mois</strong></div></div>
                    <div class="col-6"><div class="p-2 bg-light rounded-3"><small class="text-muted d-block">Prix</small><strong><?= number_format($student['formation_prix'], 2) ?> $</strong></div></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted">PAIEMENTS</h5></div>
            <div class="card-body p-0">
                <?php if (empty($payments)): ?>
                <div class="text-center py-4 text-muted"><small>Aucun paiement</small></div>
                <?php else: ?>
                <table class="table table-sm mb-0"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Montant</th><th>Mode</th></tr></thead><tbody>
                <?php foreach ($payments as $p): ?>
                <tr><td class="ps-3"><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></td><td><?= number_format($p['montant'], 2) ?> $</td><td><?= htmlspecialchars($p['methode']) ?></td></tr>
                <?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>
            </div>
        </div>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted">LEÇONS</h5></div>
            <div class="card-body p-0">
                <?php if (empty($lessons)): ?>
                <div class="text-center py-4 text-muted"><small>Aucune leçon</small></div>
                <?php else: ?>
                <table class="table table-sm mb-0"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Moniteur</th><th>Véhicule</th><th>Statut</th></tr></thead><tbody>
                <?php foreach ($lessons as $l):
                    $badge = match($l['statut']) {
                        'effectuée' => 'bg-success bg-opacity-10 text-success',
                        'annulée' => 'bg-danger bg-opacity-10 text-danger',
                        default => 'bg-warning bg-opacity-10 text-warning'
                    };
                ?>
                <tr><td class="ps-3"><small><?= date('d/m/Y H:i', strtotime($l['date_lecon'])) ?></small></td><td><?= htmlspecialchars($l['instructor_nom'] ?? 'N/A') ?></td><td><small><?= htmlspecialchars($l['vehicle_nom']) ?></small></td><td><span class="badge <?= $badge ?>"><?= htmlspecialchars($l['statut']) ?></span></td></tr>
                <?php endforeach; ?>
                </tbody></table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>