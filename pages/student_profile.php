<?php
/**
 * pages/student_profile.php — Profil complet d'un élève
 * SELECT via requêtes préparées sécurisées (pas de view spécifique car données par ID)
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
$pctPaye          = $student['formation_prix'] > 0
    ? min(100, round(($totalPaye / $student['formation_prix']) * 100)) : 0;

$pageTitle = 'Profil : ' . $student['prenom'] . ' ' . $student['nom'];
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2"><i class="bi bi-person-circle me-2"></i>Profil Élève</h1>
    <a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

<div class="row">
    <!-- Infos personnelles -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Informations personnelles</h5></div>
            <div class="card-body">
                <h4><?= htmlspecialchars($student['nom_complet']) ?></h4>
                <hr>
                <p><strong>Nationalité :</strong> <?= htmlspecialchars($student['nationalite'] ?? 'N/A') ?></p>
                <p><strong>Email :</strong> <?= htmlspecialchars($student['email']) ?></p>
                <p><strong>Téléphone :</strong> <?= htmlspecialchars($student['telephone'] ?? 'N/A') ?></p>
                <p class="mb-0"><strong>Inscription :</strong> <?= htmlspecialchars($student['date_inscription'] ?? '') ?></p>
            </div>
        </div>
    </div>

    <!-- Formation + paiement -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Formation</h5></div>
            <div class="card-body">
                <h4><?= htmlspecialchars($student['formation_nom']) ?></h4>
                <hr>
                <p><strong>Prix :</strong> <?= number_format($student['formation_prix'], 2) ?> $</p>
                <p><strong>Durée :</strong> <?= $student['formation_duree_mois'] ?> mois</p>
                <p><strong>Payé :</strong> <?= number_format($totalPaye, 2) ?> $</p>
                <p><strong>Solde :</strong>
                    <span class="badge <?= $solde <= 0 ? 'bg-success':'bg-danger' ?>">
                        <?= number_format(max(0, $solde), 2) ?> $
                    </span>
                </p>
                <div class="progress mt-2" style="height:10px;" title="<?= $pctPaye ?>% payé">
                    <div class="progress-bar bg-success" style="width:<?= $pctPaye ?>%;"></div>
                </div>
                <small class="text-muted"><?= $pctPaye ?>% payé</small>
            </div>
        </div>
    </div>

    <!-- Avancement -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Avancement</h5></div>
            <div class="card-body">
                <h4><?= $leconEffectuees ?> leçon(s) effectuée(s)</h4>
                <hr>
                <p><strong>Paiement :</strong>
                    <?php if ($solde <= 0): ?>
                        <span class="badge bg-success">Soldé ✓</span>
                    <?php else: ?>
                        <span class="badge bg-warning text-dark">Solde en attente</span>
                    <?php endif; ?>
                </p>
                <p><strong>Éligibilité examen :</strong>
                    <?php if ($leconEffectuees >= 3): ?>
                        <span class="badge bg-success">Éligible ✓</span>
                    <?php else: ?>
                        <span class="badge bg-danger">
                            <?= 3 - $leconEffectuees ?> leçon(s) manquante(s)
                        </span>
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Historique paiements -->
<div class="card mb-4">
    <div class="card-header bg-white"><h5 class="mb-0">Historique des paiements</h5></div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Date</th><th>Montant</th><th>Mode</th></tr></thead>
            <tbody>
            <?php if (empty($payments)): ?>
            <tr><td colspan="3" class="text-center text-muted py-3">Aucun paiement.</td></tr>
            <?php else: ?>
            <?php foreach ($payments as $p): ?>
            <tr>
                <td><?= htmlspecialchars($p['date_paiement']) ?></td>
                <td><?= number_format($p['montant'], 2) ?> $</td>
                <td><?= htmlspecialchars($p['methode']) ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Historique leçons -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Historique des leçons</h5></div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Date & Heure</th><th>Moniteur</th><th>Véhicule</th><th>Statut</th></tr></thead>
            <tbody>
            <?php if (empty($lessons)): ?>
            <tr><td colspan="4" class="text-center text-muted py-3">Aucune leçon.</td></tr>
            <?php else: ?>
            <?php foreach ($lessons as $l):
                $badge = ($l['statut']==='effectuée') ? 'bg-success' : (($l['statut']==='annulée') ? 'bg-danger' : 'bg-warning text-dark');
            ?>
            <tr>
                <td><?= date('d/m/Y H:i', strtotime($l['date_lecon'])) ?></td>
                <td><?= htmlspecialchars($l['instructor_nom']) ?></td>
                <td><?= htmlspecialchars($l['vehicle_nom']) ?> (<?= htmlspecialchars($l['immatriculation']) ?>)</td>
                <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($l['statut']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
