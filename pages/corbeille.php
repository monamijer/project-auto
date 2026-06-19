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
requirePermission('gestion_comptes'); // Corbeille réservée aux admins

$message = ''; $error = '';

// ── Restaurer ────────────────────────────────────────────────────────────
if (isset($_GET['restaurer']) && isset($_GET['type'])) {
    $id = (int) $_GET['restaurer'];
    $msg = match($_GET['type']) {
        'eleve'    => callProcedure("CALL sp_restaurer_eleve(?,@msg)", [$id]),
        'moniteur' => callProcedure("CALL sp_restaurer_moniteur(?,@msg)", [$id]),
        'vehicule' => callProcedure("CALL sp_restaurer_vehicule(?,@msg)", [$id]),
        default    => 'Type invalide',
    };
    $msg === 'OK' ? $message = 'Élément restauré avec succès !' : $error = $msg;
}

// ── Supprimer définitivement ──────────────────────────────────────────────
if (isset($_GET['purger']) && isset($_GET['type'])) {
    $id = (int) $_GET['purger'];
    $msg = match($_GET['type']) {
        'eleve'    => callProcedure("CALL sp_supprimer_eleve_definitif(?,@msg)", [$id]),
        'moniteur' => callProcedure("CALL sp_supprimer_moniteur_definitif(?,@msg)", [$id]),
        'vehicule' => callProcedure("CALL sp_supprimer_vehicule_definitif(?,@msg)", [$id]),
        default    => 'Type invalide',
    };
    $msg === 'OK' ? $message = 'Supprimé définitivement.' : $error = 'Impossible : données liées (leçons/paiements).';
}

// ── READ via les VIEWS de corbeille ───────────────────────────────────────
$eleves    = $pdo->query("SELECT * FROM v_corbeille_eleves")->fetchAll();
$moniteurs = $pdo->query("SELECT * FROM v_corbeille_moniteurs")->fetchAll();
$vehicules = $pdo->query("SELECT * FROM v_corbeille_vehicules")->fetchAll();

$pageTitle = 'Corbeille — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2"><i class="bi bi-trash3 me-2"></i>Corbeille</h1>
    <span class="badge bg-secondary fs-6"><?= count($eleves)+count($moniteurs)+count($vehicules) ?> élément(s)</span>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Les éléments supprimés sont conservés ici. Vous pouvez les <strong>restaurer</strong>
    ou les <strong>supprimer définitivement</strong> (irréversible).
</div>

<!-- ── Onglets : Élèves / Moniteurs / Véhicules ──────────────────────────── -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-eleves">
        <i class="bi bi-people"></i> Élèves <span class="badge bg-secondary"><?= count($eleves) ?></span>
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-moniteurs">
        <i class="bi bi-person-badge"></i> Moniteurs <span class="badge bg-secondary"><?= count($moniteurs) ?></span>
    </button></li>
    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-vehicules">
        <i class="bi bi-car-front"></i> Véhicules <span class="badge bg-secondary"><?= count($vehicules) ?></span>
    </button></li>
</ul>

<div class="tab-content">

    <!-- Élèves supprimés -->
    <div class="tab-pane fade show active" id="tab-eleves">
        <div class="card"><div class="card-body"><div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Nom</th><th>Email</th><th>Formation</th><th>Supprimé le</th><th>Par</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($eleves as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['nom_complet']) ?></td>
                <td><?= htmlspecialchars($e['email']) ?></td>
                <td><?= htmlspecialchars($e['formation_nom'] ?? '—') ?></td>
                <td><?= htmlspecialchars($e['deleted_at']) ?></td>
                <td><?= htmlspecialchars($e['deleted_by'] ?? '—') ?></td>
                <td>
                    <a href="?restaurer=<?= $e['id'] ?>&type=eleve" class="btn btn-sm btn-success" title="Restaurer"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <a href="?purger=<?= $e['id'] ?>&type=eleve" class="btn btn-sm btn-danger" title="Supprimer définitivement"
                       onclick="return confirm('Suppression DÉFINITIVE et IRRÉVERSIBLE. Continuer ?')"><i class="bi bi-x-octagon"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($eleves)): ?><tr><td colspan="6" class="text-center text-muted py-3">Corbeille vide.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div></div></div>
    </div>

    <!-- Moniteurs supprimés -->
    <div class="tab-pane fade" id="tab-moniteurs">
        <div class="card"><div class="card-body"><div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Nom</th><th>Téléphone</th><th>Supprimé le</th><th>Par</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($moniteurs as $m): ?>
            <tr>
                <td><?= htmlspecialchars($m['nom_complet']) ?></td>
                <td><?= htmlspecialchars($m['telephone'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['deleted_at']) ?></td>
                <td><?= htmlspecialchars($m['deleted_by'] ?? '—') ?></td>
                <td>
                    <a href="?restaurer=<?= $m['id'] ?>&type=moniteur" class="btn btn-sm btn-success" title="Restaurer"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <a href="?purger=<?= $m['id'] ?>&type=moniteur" class="btn btn-sm btn-danger" title="Supprimer définitivement"
                       onclick="return confirm('Suppression DÉFINITIVE et IRRÉVERSIBLE. Continuer ?')"><i class="bi bi-x-octagon"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($moniteurs)): ?><tr><td colspan="5" class="text-center text-muted py-3">Corbeille vide.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div></div></div>
    </div>

    <!-- Véhicules supprimés -->
    <div class="tab-pane fade" id="tab-vehicules">
        <div class="card"><div class="card-body"><div class="table-responsive">
        <table class="table table-striped">
            <thead><tr><th>Véhicule</th><th>Immatriculation</th><th>Supprimé le</th><th>Par</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($vehicules as $v): ?>
            <tr>
                <td><?= htmlspecialchars($v['designation']) ?></td>
                <td><?= htmlspecialchars($v['immatriculation']) ?></td>
                <td><?= htmlspecialchars($v['deleted_at']) ?></td>
                <td><?= htmlspecialchars($v['deleted_by'] ?? '—') ?></td>
                <td>
                    <a href="?restaurer=<?= $v['id'] ?>&type=vehicule" class="btn btn-sm btn-success" title="Restaurer"><i class="bi bi-arrow-counterclockwise"></i></a>
                    <a href="?purger=<?= $v['id'] ?>&type=vehicule" class="btn btn-sm btn-danger" title="Supprimer définitivement"
                       onclick="return confirm('Suppression DÉFINITIVE et IRRÉVERSIBLE. Continuer ?')"><i class="bi bi-x-octagon"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($vehicules)): ?><tr><td colspan="5" class="text-center text-muted py-3">Corbeille vide.</td></tr><?php endif; ?>
            </tbody>
        </table>
        </div></div></div>
    </div>

</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
