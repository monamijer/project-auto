<?php
/**
 * pages/search.php — Recherche globale
 * SELECT → v_recherche_globale, v_pays_nationalites (Views SQL)
 * Permet de chercher un élève, un moniteur ou un véhicule par nom,
 * et de filtrer par pays/nationalité.
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

// ── Paramètres de recherche (GET) ─────────────────────────────────────────
$q       = trim($_GET['q'] ?? '');
$type    = $_GET['type'] ?? '';   // eleve / moniteur / vehicule / '' (tous)
$pays    = $_GET['pays'] ?? '';

// ── Liste des pays pour le filtre déroulant (via VIEW) ────────────────────
$paysListe = $pdo->query("SELECT pays FROM v_pays_nationalites")->fetchAll(PDO::FETCH_COLUMN);

// ── Construction de la requête sur la VIEW (pas de table brute) ──────────
$results = [];
if ($q !== '' || $type !== '' || $pays !== '') {
    $sql    = "SELECT * FROM v_recherche_globale WHERE 1=1";
    $params = [];

    if ($q !== '') {
        $sql .= " AND (nom_complet LIKE ? COLLATE utf8mb4_unicode_ci OR detail1 LIKE ? COLLATE utf8mb4_unicode_ci OR detail2 LIKE ? COLLATE utf8mb4_unicode_ci)";
        $like = "%$q%";
        $params[] = $like; $params[] = $like; $params[] = $like;
    }
    if ($type !== '') {
        $sql .= " AND type = ? COLLATE utf8mb4_unicode_ci";
        $params[] = $type;
    }
    if ($pays !== '') {
        $sql .= " AND pays = ? COLLATE utf8mb4_unicode_ci";
        $params[] = $pays;
    }
    $sql .= " ORDER BY nom_complet";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
}

$pageTitle = 'Recherche — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2"><i class="bi bi-search me-2"></i>Recherche globale</h1>
</div>

<!-- ── Formulaire de recherche ────────────────────────────────────────── -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label">Nom, téléphone, email, immatriculation...</label>
                <input type="text" name="q" class="form-control"
                       placeholder="Ex : Mukiza, 0790..., ABC-123"
                       value="<?= htmlspecialchars($q) ?>" autofocus>
            </div>
            <div class="col-md-3">
                <label class="form-label">Type</label>
                <select name="type" class="form-select">
                    <option value="">Tous</option>
                    <option value="eleve"    <?= $type==='eleve'?'selected':'' ?>>Élèves</option>
                    <option value="moniteur" <?= $type==='moniteur'?'selected':'' ?>>Moniteurs</option>
                    <option value="vehicule" <?= $type==='vehicule'?'selected':'' ?>>Véhicules</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Pays / Nationalité</label>
                <select name="pays" class="form-select">
                    <option value="">Tous les pays</option>
                    <?php foreach ($paysListe as $p): ?>
                    <option value="<?= htmlspecialchars($p) ?>" <?= $pays===$p?'selected':'' ?>>
                        <?= htmlspecialchars($p) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<!-- ── Résultats ──────────────────────────────────────────────────────── -->
<?php if ($q !== '' || $type !== '' || $pays !== ''): ?>
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Résultats</h5>
        <span class="badge bg-secondary"><?= count($results) ?> trouvé(s)</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Type</th><th>Nom</th><th>Pays</th><th>Détail 1</th><th>Détail 2</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($results as $r):
                    $icon  = match($r['type']) { 'eleve'=>'bi-person', 'moniteur'=>'bi-person-badge', 'vehicule'=>'bi-car-front', default=>'bi-question' };
                    $badge = match($r['type']) { 'eleve'=>'bg-primary', 'moniteur'=>'bg-info', 'vehicule'=>'bg-secondary', default=>'bg-dark' };
                ?>
                <tr>
                    <td><span class="badge <?= $badge ?>"><i class="bi <?= $icon ?>"></i> <?= ucfirst($r['type']) ?></span></td>
                    <td><?= htmlspecialchars($r['nom_complet']) ?></td>
                    <td><?= htmlspecialchars($r['pays'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($r['detail1'] ?? '') ?></td>
                    <td><?= htmlspecialchars($r['detail2'] ?? '') ?></td>
                    <td>
                        <?php if ($r['type'] === 'eleve'): ?>
                        <a href="<?= BASE_URL ?>/pages/student_profile.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Profil</a>
                        <?php elseif ($r['type'] === 'moniteur'): ?>
                        <a href="<?= BASE_URL ?>/pages/instructors.php" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Voir</a>
                        <?php else: ?>
                        <a href="<?= BASE_URL ?>/pages/vehicles.php" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Voir</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($results)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Aucun résultat pour cette recherche.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i>
    Saisissez un nom, un téléphone, un email ou choisissez un pays pour lancer la recherche.
</div>
<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>