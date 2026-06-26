<?php
/**
 * pages/student_profile.php — Profil complet d'un élève avec commentaires AJAX
 * SELECT → v_eleves, v_paiements, v_lecons (Views SQL)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$studentId = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$stmt = $pdo->prepare('SELECT * FROM v_eleves WHERE id = ?');
$stmt->execute([$studentId]);
$student = $stmt->fetch();

if (!$student) {
    header('Location: ' . BASE_URL . '/pages/students.php');
    exit();
}

// ── Paiements et leçons via vues ──────────────────────────────────────────
$stmt = $pdo->prepare('SELECT * FROM v_paiements WHERE utilisateur_id = ? ORDER BY date_paiement DESC');
$stmt->execute([$studentId]);
$payments = $stmt->fetchAll();

$stmt = $pdo->prepare('SELECT * FROM v_lecons WHERE utilisateur_id = ? ORDER BY date_lecon DESC');
$stmt->execute([$studentId]);
$lessons = $stmt->fetchAll();

// ── Commentaires via AJAX ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_comment'])) {
    header('Content-Type: application/json');

    if ($_POST['ajax_comment'] === 'add') {
        $contenu = trim($_POST['contenu'] ?? '');
        $prive = isset($_POST['prive']) ? 1 : 0;
        if (!empty($contenu)) {
            $stmt = $pdo->prepare("INSERT INTO commentaires (type_cible, cible_id, auteur, contenu, prive) VALUES ('eleve', ?, ?, ?, ?)");
            $stmt->execute([$studentId, $_SESSION['username'], $contenu, $prive]);
            echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Commentaire vide.']);
        }
        exit();
    }

    if ($_POST['ajax_comment'] === 'delete' && isAdmin()) {
        $cid = (int) $_POST['cid'];
        $pdo->prepare("DELETE FROM commentaires WHERE id = ? AND type_cible = 'eleve'")->execute([$cid]);
        echo json_encode(['success' => true]);
        exit();
    }
}

// ── Lire les commentaires ─────────────────────────────────────────────────
try {
    $stmt = $pdo->prepare("SELECT * FROM commentaires WHERE type_cible='eleve' AND cible_id=? ORDER BY created_at DESC");
    $stmt->execute([$studentId]);
    $comments = $stmt->fetchAll();
} catch (Exception $e) {
    $comments = [];
}

// ── Calculs ───────────────────────────────────────────────────────────────
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
        <nav aria-label="breadcrumb"><ol class="breadcrumb mb-2"><li class="breadcrumb-item"><a href="<?= BASE_URL ?>/pages/students.php">Élèves</a></li><li class="breadcrumb-item active"><?= htmlspecialchars(
    $student['nom_complet']
) ?></li></ol></nav>
        <h1 class="h3 mb-0"><i class="bi bi-person-badge me-2"></i><?= htmlspecialchars(
            $student['nom_complet']
        ) ?> <span class="badge bg-<?= $statutBadge ?> bg-opacity-10 text-<?= $statutBadge ?> ms-2"><?= $statut ?></span></h1>
    </div>
    <div class="btn-group">
        <a href="<?= BASE_URL ?>/pages/students.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i>Retour</a>
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()"><i class="bi bi-printer me-1"></i>Imprimer</button>
    </div>
</div>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-md-6"><div class="card shadow-sm border-0"><div class="card-body">
        <h6 class="text-muted small mb-2">Progression</h6>
        <div class="d-flex justify-content-between align-items-end mb-2"><h3 class="mb-0 fw-bold"><?= $pctProgression ?>%</h3><i class="bi bi-graph-up text-primary fs-4"></i></div>
        <div class="progress" style="height:6px;"><div class="progress-bar bg-primary" style="width:<?= $pctProgression ?>%;"></div></div>
        <small class="text-muted"><?= $leconEffectuees ?> leçons</small>
    </div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card shadow-sm border-0"><div class="card-body">
        <h6 class="text-muted small mb-2">Paiement</h6>
        <div class="d-flex justify-content-between align-items-end mb-2"><h3 class="mb-0 fw-bold"><?= $pctPaye ?>%</h3><i class="bi bi-cash-stack text-success fs-4"></i></div>
        <div class="progress" style="height:6px;"><div class="progress-bar bg-success" style="width:<?= $pctPaye ?>%;"></div></div>
        <small class="text-muted"><?= number_format($totalPaye, 2) ?> $ / <?= number_format($student['formation_prix'], 2) ?> $</small>
    </div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card shadow-sm border-0"><div class="card-body">
        <h6 class="text-muted small mb-2">Leçons</h6><h3 class="mb-0 fw-bold"><?= $totalLecons ?></h3>
        <small><span class="text-success"><?= $leconEffectuees ?> OK</span> · <span class="text-warning"><?= $leconPlanifiees ?> prévues</span> · <span class="text-danger"><?= $leconAnnulees ?> annulées</span></small>
    </div></div></div>
    <div class="col-xl-3 col-md-6"><div class="card shadow-sm border-0"><div class="card-body">
        <h6 class="text-muted small mb-2">Solde</h6>
        <h3 class="mb-0 fw-bold <?= $solde <= 0 ? 'text-success' : 'text-danger' ?>"><?= number_format(max(0, $solde), 2) ?> $</h3>
        <small class="<?= $solde <= 0 ? 'text-success' : 'text-danger' ?>"><?= $solde <= 0 ? 'Payé' : 'En attente' ?></small>
    </div></div></div>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3"><div class="card-body">
            <h5 class="card-title small fw-bold text-muted mb-3">INFORMATIONS</h5>
            <div class="text-center mb-3"><div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center mb-2" style="width:64px;height:64px;"><span class="text-primary fw-bold fs-5"><?= strtoupper(
                substr($student['prenom'], 0, 1) . substr($student['nom'], 0, 1)
            ) ?></span></div></div>
            <div class="small"><i class="bi bi-calendar-date text-muted me-2"></i>Inscrit le <?= date('d/m/Y', strtotime($student['date_inscription'] ?? 'now')) ?></div>
            <div class="small mt-2"><i class="bi bi-flag text-muted me-2"></i><?= htmlspecialchars($student['nationalite'] ?? 'N/A') ?></div>
            <div class="small mt-2"><i class="bi bi-envelope text-muted me-2"></i><?= htmlspecialchars($student['email']) ?></div>
            <div class="small mt-2"><i class="bi bi-telephone text-muted me-2"></i><?= htmlspecialchars($student['telephone'] ?? 'Non renseigné') ?></div>
        </div></div>
        <div class="card shadow-sm border-0"><div class="card-body">
            <h5 class="card-title small fw-bold text-muted mb-3">FORMATION</h5>
            <strong><?= htmlspecialchars($student['formation_nom']) ?></strong>
            <div class="row g-2 mt-2"><div class="col-6"><div class="p-2 bg-light rounded-3"><small class="text-muted d-block">Durée</small><strong><?= $student[
                'formation_duree_mois'
            ] ?> mois</strong></div></div><div class="col-6"><div class="p-2 bg-light rounded-3"><small class="text-muted d-block">Prix</small><strong><?= number_format(
     $student['formation_prix'],
     2
 ) ?> $</strong></div></div></div>
        </div></div>
    </div>
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3"><div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted">PAIEMENTS</h5></div><div class="card-body p-0">
            <?php if (empty($payments)): ?><div class="text-center py-4 text-muted"><small>Aucun paiement</small></div>
            <?php else: ?><table class="table table-sm mb-0"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Montant</th><th>Mode</th></tr></thead><tbody>
            <?php foreach ($payments as $p): ?><tr><td class="ps-3"><?= date('d/m/Y', strtotime($p['date_paiement'])) ?></td><td><?= number_format($p['montant'], 2) ?> $</td><td><?= htmlspecialchars(
     $p['methode']
 ) ?></td></tr><?php endforeach; ?>
            </tbody></table><?php endif; ?>
        </div></div>
        <div class="card shadow-sm border-0"><div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted">LEÇONS</h5></div><div class="card-body p-0">
            <?php if (empty($lessons)): ?><div class="text-center py-4 text-muted"><small>Aucune leçon</small></div>
            <?php else: ?><table class="table table-sm mb-0"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Moniteur</th><th>Véhicule</th><th>Statut</th></tr></thead><tbody>
            <?php foreach ($lessons as $l):
                $badge = match ($l['statut']) {
                    'effectuée' => 'bg-success bg-opacity-10 text-success',
                    'annulée' => 'bg-danger bg-opacity-10 text-danger',
                    default => 'bg-warning bg-opacity-10 text-warning',
                }; ?>
            <tr><td class="ps-3"><small><?= date('d/m/Y H:i', strtotime($l['date_lecon'])) ?></small></td><td><?= htmlspecialchars(
    $l['instructor_nom'] ?? 'N/A'
) ?></td><td><small><?= htmlspecialchars($l['vehicle_nom']) ?></small></td><td><span class="badge <?= $badge ?>"><?= htmlspecialchars($l['statut']) ?></span></td></tr>
            <?php
            endforeach; ?></tbody></table><?php endif; ?>
        </div></div>
    </div>
</div>

<!-- Commentaires AJAX -->
<div class="card shadow-sm border-0 mt-4" id="commentsSection">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-chat-left-text me-2"></i>Commentaires</h5>
        <span class="badge bg-secondary" id="commentCount"><?= count($comments) ?></span>
    </div>
    <div class="card-body">
        <form id="commentForm" class="mb-4">
            <div class="mb-2"><textarea id="commentContent" class="form-control" rows="2" placeholder="Ajouter une observation sur cet élève..." required></textarea></div>
            <div class="d-flex justify-content-between align-items-center">
                <div class="form-check"><input type="checkbox" name="prive" class="form-check-input" id="chkPrive"><label class="form-check-label small" for="chkPrive"><i class="bi bi-lock me-1"></i>Privé (admins uniquement)</label></div>
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-send me-1"></i>Publier</button>
            </div>
        </form>
        <div id="commentsList">
        <?php foreach ($comments as $cm):
            if (($cm['prive'] ?? 0) && !isAdmin()) {
                continue;
            } ?>
        <div class="d-flex gap-3 mb-3 pb-3 border-bottom" id="comment-<?= $cm['id'] ?>">
            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px;">
                <span class="text-primary fw-bold small"><?= strtoupper(substr($cm['auteur'], 0, 1)) ?></span>
            </div>
            <div class="flex-grow-1">
                <div class="d-flex justify-content-between">
                    <strong class="small"><?= htmlspecialchars($cm['auteur']) ?></strong>
                    <div class="d-flex align-items-center gap-2">
                        <?php if ($cm['prive'] ?? 0): ?><span class="badge bg-secondary" style="font-size:.6rem;"><i class="bi bi-lock me-1"></i>Privé</span><?php endif; ?>
                        <small class="text-muted"><?= date('d/m/Y H:i', strtotime($cm['created_at'])) ?></small>
                        <?php if (isAdmin()): ?>
                        <button class="btn btn-sm p-0 text-danger border-0 bg-transparent delete-comment" data-id="<?= $cm['id'] ?>" title="Supprimer"><i class="bi bi-trash3"></i></button>
                        <?php endif; ?>
                    </div>
                </div>
                <p class="mb-0 mt-1 small"><?= nl2br(htmlspecialchars($cm['contenu'])) ?></p>
            </div>
        </div>
        <?php
        endforeach; ?>
        <?php if (empty($comments)): ?><p class="text-center text-muted mb-0 py-2" id="emptyComments">Aucun commentaire pour cet élève.</p><?php endif; ?>
        </div>
    </div>
</div>

<script>
// AJAX — Ajouter un commentaire
document.getElementById('commentForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const content = document.getElementById('commentContent').value.trim();
    if (!content) return;
    
    const fd = new FormData();
    fd.append('ajax_comment', 'add');
    fd.append('contenu', content);
    fd.append('prive', document.getElementById('chkPrive').checked ? 1 : 0);
    
    const resp = await fetch('', { method: 'POST', body: fd });
    const data = await resp.json();
    
    if (data.success) {
        document.getElementById('commentContent').value = '';
        location.reload();
    }
});

// AJAX — Supprimer un commentaire
document.querySelectorAll('.delete-comment').forEach(btn => {
    btn.addEventListener('click', async function() {
        if (!confirm('Supprimer ce commentaire ?')) return;
        const fd = new FormData();
        fd.append('ajax_comment', 'delete');
        fd.append('cid', this.dataset.id);
        
        const resp = await fetch('', { method: 'POST', body: fd });
        const data = await resp.json();
        
        if (data.success) {
            const el = document.getElementById('comment-' + this.dataset.id);
            if (el) el.remove();
            // Mettre à jour le compteur
            const count = document.querySelectorAll('#commentsList .border-bottom').length;
            document.getElementById('commentCount').textContent = count;
            if (count === 0) {
                document.getElementById('commentsList').innerHTML = '<p class="text-center text-muted mb-0 py-2">Aucun commentaire.</p>';
            }
        }
    });
});
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>
