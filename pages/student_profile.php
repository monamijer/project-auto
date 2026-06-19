<?php
/**
 * pages/student_profile.php — Profil complet d'un élève
 * SELECT via requêtes préparées sécurisées (pas de view spécifique car données par ID)
 * Design moderne avec dashboard académique professionnel
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

// ── ID sécurisé ───────────────────────────────────────────────────────────
$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

// ── Élève + formation via v_eleves ────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM v_eleves WHERE id = ?");
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: ' . BASE_URL . '/pages/students.php');
    exit();
}

// ── Paiements de cet élève ────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM v_paiements WHERE utilisateur_id = ? ORDER BY date_paiement DESC");
$stmt->execute([$studentId]);
$payments = $stmt->fetchAll();

// ── Leçons de cet élève via v_lecons ─────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM v_lecons WHERE utilisateur_id = ? ORDER BY date_lecon DESC");
$stmt->execute([$studentId]);
$lessons = $stmt->fetchAll();

// ── Calculs ───────────────────────────────────────────────────────────────
$totalPaye        = array_sum(array_column($payments, 'montant'));
$solde            = $student['formation_prix'] - $totalPaye;
$leconEffectuees  = count(array_filter($lessons, fn($l) => $l['statut'] === 'effectuée'));
$leconPlanifiees  = count(array_filter($lessons, fn($l) => $l['statut'] === 'planifiée'));
$leconAnnulees    = count(array_filter($lessons, fn($l) => $l['statut'] === 'annulée'));
$totalLecons      = count($lessons);
$pctPaye          = $student['formation_prix'] > 0
    ? min(100, round(($totalPaye / $student['formation_prix']) * 100)) : 0;
$pctProgression   = $student['formation_duree_mois'] > 0 
    ? min(100, round(($leconEffectuees / max(1, $student['formation_duree_mois'] * 4)) * 100)) : 0;

// Déterminer le statut de l'étudiant
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

<!-- Header Section -->
<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/students.php">Élèves</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($student['nom_complet']) ?></li>
            </ol>
        </nav>
        <h1 class="h2 mb-0">
            <i class="bi bi-person-badge me-2"></i>
            <?= htmlspecialchars($student['nom_complet']) ?>
            <span class="badge bg-<?= $statutBadge ?> ms-2 align-middle"><?= $statut ?></span>
        </h1>
    </div>
    <div class="btn-group">
        <a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> Retour
        </a>
        <button class="btn btn-outline-primary" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Imprimer
        </button>
    </div>
</div>

<!-- Dashboard Cards Row -->
<div class="row g-3 mb-4">
    <!-- Progression Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Progression</h6>
                        <h3 class="mb-0 fw-bold"><?= $pctProgression ?>%</h3>
                    </div>
                    <div class="icon-circle bg-primary bg-opacity-10">
                        <i class="bi bi-graph-up text-primary"></i>
                    </div>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-primary progress-bar-animated" 
                         role="progressbar" 
                         style="width: <?= $pctProgression ?>%" 
                         aria-valuenow="<?= $pctProgression ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <?= $leconEffectuees ?> leçons sur ~<?= $student['formation_duree_mois'] * 4 ?> estimées
                </small>
            </div>
        </div>
    </div>

    <!-- Paiement Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Paiement</h6>
                        <h3 class="mb-0 fw-bold"><?= $pctPaye ?>%</h3>
                    </div>
                    <div class="icon-circle bg-success bg-opacity-10">
                        <i class="bi bi-cash-stack text-success"></i>
                    </div>
                </div>
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar bg-success" 
                         role="progressbar" 
                         style="width: <?= $pctPaye ?>%" 
                         aria-valuenow="<?= $pctPaye ?>" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
                <small class="text-muted mt-2 d-block">
                    <?= number_format($totalPaye, 2) ?> $ / <?= number_format($student['formation_prix'], 2) ?> $
                </small>
            </div>
        </div>
    </div>

    <!-- Leçons Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Total Leçons</h6>
                        <h3 class="mb-0 fw-bold"><?= $totalLecons ?></h3>
                    </div>
                    <div class="icon-circle bg-info bg-opacity-10">
                        <i class="bi bi-calendar-check text-info"></i>
                    </div>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <small class="text-success d-block">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                            <?= $leconEffectuees ?> effectuées
                        </small>
                        <small class="text-warning d-block">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                            <?= $leconPlanifiees ?> planifiées
                        </small>
                        <small class="text-danger d-block">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>
                            <?= $leconAnnulees ?> annulées
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Solde Card -->
    <div class="col-xl-3 col-md-6">
        <div class="card border-0 shadow-sm h-100 hover-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h6 class="text-muted mb-1">Solde restant</h6>
                        <h3 class="mb-0 fw-bold <?= $solde <= 0 ? 'text-success' : 'text-danger' ?>">
                            <?= number_format(max(0, $solde), 2) ?> $
                        </h3>
                    </div>
                    <div class="icon-circle <?= $solde <= 0 ? 'bg-success' : 'bg-danger' ?> bg-opacity-10">
                        <i class="bi <?= $solde <= 0 ? 'bi-check-circle text-success' : 'bi-exclamation-circle text-danger' ?>"></i>
                    </div>
                </div>
                <?php if ($solde <= 0): ?>
                    <span class="badge bg-success bg-opacity-10 text-success">
                        <i class="bi bi-check2 me-1"></i>Paiement complet
                    </span>
                <?php else: ?>
                    <span class="badge bg-danger bg-opacity-10 text-danger">
                        <i class="bi bi-exclamation-triangle me-1"></i>En attente
                    </span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-3">
    <!-- Left Column -->
    <div class="col-lg-4">
        <!-- Personal Info Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="card-title d-flex align-items-center">
                    <i class="bi bi-person-vcard me-2 text-primary"></i>
                    Informations personnelles
                </h5>
            </div>
            <div class="card-body pt-3">
                <div class="text-center mb-4">
                    <div class="avatar-circle bg-primary bg-opacity-10 mx-auto mb-3">
                        <span class="display-6 text-primary fw-bold">
                            <?= strtoupper(substr($student['prenom'], 0, 1) . substr($student['nom'], 0, 1)) ?>
                        </span>
                    </div>
                    <h4 class="mb-1"><?= htmlspecialchars($student['nom_complet']) ?></h4>
                    <span class="badge bg-<?= $statutBadge ?> bg-opacity-10 text-<?= $statutBadge ?>">
                        <?= $statut ?>
                    </span>
                </div>
                
                <div class="info-item">
                    <div class="d-flex align-items-center py-2">
                        <i class="bi bi-calendar-date text-muted me-3"></i>
                        <div>
                            <small class="text-muted d-block">Date d'inscription</small>
                            <span class="fw-medium">
                                <?= date('d/m/Y', strtotime($student['date_inscription'] ?? 'now')) ?>
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="info-item border-top">
                    <div class="d-flex align-items-center py-2">
                        <i class="bi bi-flag text-muted me-3"></i>
                        <div>
                            <small class="text-muted d-block">Nationalité</small>
                            <span class="fw-medium"><?= htmlspecialchars($student['nationalite'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="info-item border-top">
                    <div class="d-flex align-items-center py-2">
                        <i class="bi bi-envelope text-muted me-3"></i>
                        <div>
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-medium"><?= htmlspecialchars($student['email']) ?></span>
                        </div>
                    </div>
                </div>
                
                <div class="info-item border-top">
                    <div class="d-flex align-items-center py-2">
                        <i class="bi bi-telephone text-muted me-3"></i>
                        <div>
                            <small class="text-muted d-block">Téléphone</small>
                            <span class="fw-medium"><?= htmlspecialchars($student['telephone'] ?? 'Non renseigné') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formation Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                <h5 class="card-title d-flex align-items-center">
                    <i class="bi bi-mortarboard me-2 text-info"></i>
                    Formation
                </h5>
            </div>
            <div class="card-body pt-3">
                <h5 class="mb-3 text-info"><?= htmlspecialchars($student['formation_nom']) ?></h5>
                
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <small class="text-muted d-block">Durée</small>
                            <strong><?= $student['formation_duree_mois'] ?> mois</strong>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-2 bg-light rounded-3">
                            <small class="text-muted d-block">Prix total</small>
                            <strong><?= number_format($student['formation_prix'], 2) ?> $</strong>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">Progression formation</small>
                    <small class="fw-bold"><?= $pctProgression ?>%</small>
                </div>
                <div class="progress mt-2" style="height: 6px;">
                    <div class="progress-bar bg-info" style="width: <?= $pctProgression ?>%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column -->
    <div class="col-lg-8">
        <!-- Paiements Card -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 pt-3">
                <h5 class="card-title d-flex align-items-center mb-0">
                    <i class="bi bi-receipt me-2 text-success"></i>
                    Historique des paiements
                </h5>
                <span class="badge bg-success bg-opacity-10 text-success">
                    <?= count($payments) ?> transaction(s)
                </span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($payments)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-receipt display-4 mb-3 d-block"></i>
                        <p class="mb-0">Aucun paiement enregistré</p>
                        <small>Les paiements apparaîtront ici une fois effectués</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date</th>
                                    <th>Montant</th>
                                    <th>Mode</th>
                                    <th class="text-end pe-3">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($payments as $p): ?>
                                <tr>
                                    <td class="ps-3">
                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                        <?= date('d/m/Y', strtotime($p['date_paiement'])) ?>
                                    </td>
                                    <td>
                                        <strong><?= number_format($p['montant'], 2) ?> $</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            <?= htmlspecialchars($p['methode']) ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge bg-success bg-opacity-10 text-success">
                                            <i class="bi bi-check2 me-1"></i>Complété
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Leçons Card -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center border-bottom-0 pt-3">
                <h5 class="card-title d-flex align-items-center mb-0">
                    <i class="bi bi-calendar-check me-2 text-warning"></i>
                    Historique des leçons
                </h5>
                <span class="badge bg-warning bg-opacity-10 text-warning">
                    <?= $totalLecons ?> leçon(s)
                </span>
            </div>
            <div class="card-body p-0">
                <?php if (empty($lessons)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-calendar-x display-4 mb-3 d-block"></i>
                        <p class="mb-0">Aucune leçon planifiée</p>
                        <small>Les leçons apparaîtront ici une fois programmées</small>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Date & Heure</th>
                                    <th>Moniteur</th>
                                    <th>Véhicule</th>
                                    <th class="text-end pe-3">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lessons as $l):
                                    $badge = match($l['statut']) {
                                        'effectuée' => 'bg-success bg-opacity-10 text-success',
                                        'annulée' => 'bg-danger bg-opacity-10 text-danger',
                                        default => 'bg-warning bg-opacity-10 text-warning'
                                    };
                                    $icon = match($l['statut']) {
                                        'effectuée' => 'bi-check-circle',
                                        'annulée' => 'bi-x-circle',
                                        default => 'bi-clock'
                                    };
                                ?>
                                <tr>
                                    <td class="ps-3">
                                        <i class="bi bi-calendar3 text-muted me-2"></i>
                                        <?= date('d/m/Y H:i', strtotime($l['date_lecon'])) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-xs bg-primary bg-opacity-10 rounded-circle me-2">
                                                <span class="text-primary small fw-bold">
                                                    <?= strtoupper(substr($l['instructor_nom'] ?? 'M', 0, 1)) ?>
                                                </span>
                                            </div>
                                            <?= htmlspecialchars($l['instructor_nom'] ?? 'N/A') ?>
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            <?= htmlspecialchars($l['vehicle_nom']) ?>
                                            <br>
                                            <span class="text-muted"><?= htmlspecialchars($l['immatriculation']) ?></span>
                                        </small>
                                    </td>
                                    <td class="text-end pe-3">
                                        <span class="badge <?= $badge ?>">
                                            <i class="bi <?= $icon ?> me-1"></i>
                                            <?= htmlspecialchars($l['statut']) ?>
                                        </span>
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

<!-- Custom CSS -->
<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1)!important;
}
.icon-circle {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.icon-circle i {
    font-size: 1.5rem;
}
.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-xs {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.info-item:not(:last-child) {
    border-bottom: 1px solid #f0f0f0;
}
.card {
    border-radius: 12px;
    transition: box-shadow 0.3s ease;
}
.card:hover {
    box-shadow: 0 5px 20px rgba(0,0,0,0.08)!important;
}
.progress {
    border-radius: 10px;
}
.progress-bar {
    border-radius: 10px;
}
.progress-bar-animated {
    animation: progress-bar-stripes 1s linear infinite;
}
.table > :not(caption) > * > * {
    padding-top: 0.75rem;
    padding-bottom: 0.75rem;
}
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
.btn-group .btn {
    border-radius: 8px;
    margin: 0 2px;
}
.breadcrumb {
    background: transparent;
    padding: 0;
}
@media (max-width: 768px) {
    .display-6 {
        font-size: 2rem;
    }
    .card-title {
        font-size: 1rem;
    }
}
</style>

<?php include BASE_PATH . '/includes/footer.php'; ?>