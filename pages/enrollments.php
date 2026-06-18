<?php
/**
 * pages/enrollments.php — Inscriptions
 * SELECT → v_inscriptions, v_stats_formations (Views SQL)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

// ── READ via les views ─────────────────────────────────────────────────────
$formationStats = $pdo->query("SELECT * FROM v_stats_formations ORDER BY id")->fetchAll();
$enrollments    = $pdo->query("SELECT * FROM v_inscriptions ORDER BY date_inscription DESC")->fetchAll();

$pageTitle   = 'Inscriptions — Auto École Pro';
$dataTableId = 'enrollmentsTable';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2">Inscriptions aux Formations</h1>
    <?php if (hasPermission('crud_eleves')): ?>
    <a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-primary">
        <i class="bi bi-person-plus"></i> Inscrire un élève
    </a>
    <?php endif; ?>
</div>

<!-- ── Cartes par formation ──────────────────────────────────────────────── -->
<div class="row mb-4">
    <?php foreach ($formationStats as $stat):
        $attendu = $stat['total_eleves'] * $stat['formation_prix'];
        $pct     = $attendu > 0 ? min(100, round(($stat['total_percu'] / $attendu) * 100)) : 0;
    ?>
    <div class="col-md-4 mb-3">
        <div class="card h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?= htmlspecialchars($stat['formation_nom']) ?></h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-3">
                    <li><strong>Élèves inscrits :</strong> <?= $stat['total_eleves'] ?></li>
                    <li><strong>Prix unitaire :</strong>   <?= number_format($stat['formation_prix'], 2) ?> $</li>
                    <li><strong>Total attendu :</strong>   <?= number_format($attendu, 2) ?> $</li>
                    <li><strong>Total perçu :</strong>     <?= number_format($stat['total_percu'], 2) ?> $</li>
                    <li><strong>Leçons effectuées :</strong> <?= $stat['lecons_effectuees'] ?></li>
                </ul>
                <small class="text-muted">Recouvrement : <?= $pct ?>%</small>
                <div class="progress mt-1" style="height:8px;">
                    <div class="progress-bar bg-success" style="width:<?= $pct ?>%;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Tableau détaillé via v_inscriptions ──────────────────────────────── -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Détail des inscriptions</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="enrollmentsTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Élève</th><th>Formation</th><th>Prix</th>
                        <th>Payé</th><th>Solde</th>
                        <th>Leçons OK</th><th>Programmées</th>
                        <th>Inscription</th><th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($enrollments as $row): ?>
                <tr>
                    <td>
                        <?= htmlspecialchars($row['nom_complet']) ?><br>
                        <small class="text-muted"><?= htmlspecialchars($row['email']) ?></small>
                    </td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($row['formation_nom']) ?></span></td>
                    <td><?= number_format($row['formation_prix'], 2) ?> $</td>
                    <td><?= number_format($row['total_paye'], 2) ?> $</td>
                    <td>
                        <span class="badge <?= $row['solde_restant'] <= 0 ? 'bg-success':'bg-danger' ?>">
                            <?= number_format(max(0, $row['solde_restant']), 2) ?> $
                        </span>
                    </td>
                    <td><span class="badge bg-success"><?= $row['lecons_effectuees'] ?></span></td>
                    <td><span class="badge bg-warning text-dark"><?= $row['lecons_programmees'] ?></span></td>
                    <td><?= htmlspecialchars($row['date_inscription'] ?? '') ?></td>
                    <td>
                        <a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $row['id'] ?>"
                           class="btn btn-sm btn-info">
                            <i class="bi bi-eye"></i> Profil
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
