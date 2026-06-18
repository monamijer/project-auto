<?php
/**
 * pages/exams.php — Examens
 * SELECT → v_examens_eligibles, v_stats_formations (Views SQL)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

// ── READ via les views ─────────────────────────────────────────────────────
$eligible   = $pdo->query("SELECT * FROM v_examens_eligibles ORDER BY lecons_effectuees DESC")->fetchAll();
$examStats  = $pdo->query("
    SELECT
        f.nom AS formation_nom,
        COUNT(DISTINCT u.id) AS total_eleves,
        COUNT(DISTINCT e.id) AS eligibles
    FROM formations f
    LEFT JOIN utilisateurs u ON u.formation_id = f.id
    LEFT JOIN v_examens_eligibles e ON e.id = u.id
    GROUP BY f.id, f.nom
")->fetchAll();

$pageTitle   = 'Examens — Auto École Pro';
$dataTableId = 'eligibleTable';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2">Suivi des Examens</h1>
</div>

<!-- ── Cartes d'éligibilité ─────────────────────────────────────────────── -->
<div class="row mb-4">
    <?php foreach ($examStats as $stat):
        $pct = $stat['total_eleves'] > 0
            ? round(($stat['eligibles'] / $stat['total_eleves']) * 100) : 0;
    ?>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($stat['formation_nom']) ?></h5>
                <p class="mb-1"><strong>Total élèves :</strong> <?= $stat['total_eleves'] ?></p>
                <p class="mb-1"><strong>Éligibles :</strong> <?= $stat['eligibles'] ?></p>
                <p class="mb-2"><strong>Taux :</strong> <?= $pct ?>%</p>
                <div class="progress bg-light" style="height:8px;">
                    <div class="progress-bar bg-success" style="width:<?= $pct ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Tableau élèves éligibles ─────────────────────────────────────────── -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">Élèves éligibles (≥ 3 leçons effectuées)</h5>
    </div>
    <div class="card-body">
        <table id="eligibleTable" class="table table-striped">
            <thead>
                <tr><th>ID</th><th>Élève</th><th>Email</th><th>Téléphone</th>
                    <th>Formation</th><th>Leçons</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($eligible as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['nom_complet']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= htmlspecialchars($row['telephone'] ?? '') ?></td>
                <td><?= htmlspecialchars($row['formation_nom']) ?></td>
                <td><span class="badge bg-success"><?= $row['lecons_effectuees'] ?></span></td>
                <td><span class="badge bg-success">Éligible ✓</span></td>
                <td>
                    <a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>"
                       class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> Profil
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($eligible)): ?>
            <tr><td colspan="8" class="text-center text-muted py-3">
                Aucun élève éligible pour le moment (minimum 3 leçons effectuées requis).
            </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── Informations pratiques ────────────────────────────────────────────── -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Informations sur les examens</h5></div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="alert alert-info mb-0">
                    <strong><i class="bi bi-info-circle me-2"></i>Conditions d'éligibilité</strong>
                    <ul class="mt-2 mb-0">
                        <li>Minimum 3 leçons de conduite effectuées</li>
                        <li>Permis d'apprenti conducteur valide</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="alert alert-warning mb-0">
                    <strong><i class="bi bi-calendar me-2"></i>Calendrier des examens</strong>
                    <ul class="mt-2 mb-0">
                        <li>Théorique : chaque lundi à 9h00</li>
                        <li>Pratique : chaque vendredi à 10h00</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
