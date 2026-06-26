<?php
/**
 * pages/documents.php — Gestion documentaire
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('gestion_documents');

$message = '';
$error = '';

// ── TÉLÉVERSER ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload') {
    $studentId = (int) $_POST['utilisateur_id'];
    $type = trim($_POST['type_document']);
    if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erreur lors du téléversement.';
    } else {
        $allowedExt = ['pdf', 'jpg', 'jpeg', 'png', 'docx'];
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowedExt)) {
            $error = 'Type non autorisé.';
        } elseif ($_FILES['fichier']['size'] > 5 * 1024 * 1024) {
            $error = 'Fichier trop volumineux (max 5 Mo).';
        } else {
            $uploadDir = BASE_PATH . '/uploads/documents';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $studentDir = $uploadDir . '/' . $studentId;
            if (!is_dir($studentDir)) {
                mkdir($studentDir, 0777, true);
            }
            $nomFichier = 'doc_' . uniqid() . '.' . $ext;
            $destination = $studentDir . '/' . $nomFichier;
            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $destination)) {
                chmod($destination, 0644);
                $tailleKo = round($_FILES['fichier']['size'] / 1024);
                $msg = callProcedure('CALL sp_ajouter_document(?,?,?,?,?,?,@msg)', [$studentId, $type, $_FILES['fichier']['name'], $studentId . '/' . $nomFichier, $tailleKo, $_SESSION['username']]);
                $msg === 'OK' ? ($message = 'Document téléversé !') : ($error = $msg);
            } else {
                $error = 'Impossible d\'enregistrer le fichier.';
            }
        }
    }
}

// ── SUPPRIMER ─────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    callProcedure('CALL sp_supprimer_document(?,@msg)', [(int) $_GET['delete']]);
    $message = 'Document supprimé.';
}

// ── ARCHIVER ──────────────────────────────────────────────────────────────
if (isset($_GET['archive'])) {
    $msg = callProcedure('CALL sp_archiver_document(?,?,@msg)', [(int) $_GET['archive'], $_SESSION['username']]);
    $result = $pdo->query('SELECT @msg AS msg')->fetch();
    $message = ($result['msg'] ?? '') === 'OK' ? 'Document archivé !' : 'Document archivé.';
}

// ── PARTAGER ──────────────────────────────────────────────────────────────
if (isset($_GET['share'])) {
    $sid = (int) $_GET['share'];
    $token = bin2hex(random_bytes(16));
    $pdo->prepare('UPDATE documents SET share_token=?, share_expire=DATE_ADD(NOW(),INTERVAL 7 DAY) WHERE id=?')->execute([$token, $sid]);
    $shareUrl = BASE_URL . '/pages/actions/shared_doc.php?token=' . $token;
    $message = 'Lien de partage (7 jours) : <a href="' . $shareUrl . '" target="_blank">' . $shareUrl . '</a>';
}

// ── READ ──────────────────────────────────────────────────────────────────
$q_doc = trim($_GET['q'] ?? '');
$q_type = $_GET['type_doc'] ?? '';
$q_eleve = (int) ($_GET['eleve_id'] ?? 0);

$sqlDoc = 'SELECT * FROM v_documents WHERE 1=1';
$pDoc = [];
if ($q_doc) {
    $sqlDoc .= ' AND (nom_original LIKE ? OR eleve_nom LIKE ?)';
    $pDoc[] = "%$q_doc%";
    $pDoc[] = "%$q_doc%";
}
if ($q_type) {
    $sqlDoc .= ' AND type_document = ?';
    $pDoc[] = $q_type;
}
if ($q_eleve) {
    $sqlDoc .= ' AND utilisateur_id = ?';
    $pDoc[] = $q_eleve;
}
$sqlDoc .= ' ORDER BY uploaded_at DESC';
$stmtDoc = $pdo->prepare($sqlDoc);
$stmtDoc->execute($pDoc);
$documents = $stmtDoc->fetchAll();

$students = $pdo->query('SELECT id, nom_complet FROM v_eleves_select')->fetchAll();
$typesDocuments = ['Permis apprenti', 'Photo identité', 'Contrat', 'CNI / Passeport', 'Certificat médical', 'Autre'];

$pageTitle = 'Documents — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h3 mb-1"><i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>Documents</h1><p class="text-muted mb-0"><?= count($documents) ?> document(s)</p></div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="bi bi-cloud-upload me-1"></i>Téléverser</button>
</div>

<?php if (
    $message
): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2"><?= htmlspecialchars(
    $error
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Recherche -->
<form method="GET" class="card shadow-sm border-0 mb-3">
    <div class="card-body py-2">
        <div class="row g-2 align-items-end">
            <div class="col-md-4"><label class="form-label small fw-medium mb-1">Recherche</label><input type="text" name="q" class="form-control" value="<?= htmlspecialchars(
                $q_doc
            ) ?>" placeholder="Nom fichier, élève..."></div>
            <div class="col-md-3"><label class="form-label small fw-medium mb-1">Type</label><select name="type_doc" class="form-select"><option value="">Tous</option><?php foreach (
                $typesDocuments
                as $t
            ): ?><option value="<?= $t ?>" <?= $q_type === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select></div>
            <div class="col-md-3"><label class="form-label small fw-medium mb-1">Élève</label><select name="eleve_id" class="form-select"><option value="0">Tous</option><?php foreach (
                $students
                as $s
            ): ?><option value="<?= $s['id'] ?>" <?= $q_eleve == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-2"><button type="submit" class="btn btn-primary w-100 btn-sm"><i class="bi bi-search me-1"></i>Filtrer</button></div>
        </div>
    </div>
</form>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste</h5><span class="badge bg-primary rounded-pill"><?= count(
        $documents
    ) ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="ps-3">Élève</th><th>Type</th><th>Fichier</th><th>Taille</th><th>Version</th><th>Ajouté par</th><th>Date</th><th class="text-end pe-3">Actions</th></tr></thead>
        <tbody>
        <?php if (empty($documents)): ?><tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>Aucun document</td></tr>
        <?php else:foreach ($documents as $d): ?>
        <tr>
            <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($d['eleve_nom']) ?></span></td>
            <td><span class="badge bg-info bg-opacity-10 text-info"><?= htmlspecialchars($d['type_document']) ?></span></td>
            <td><small><?= htmlspecialchars($d['nom_original']) ?></small></td>
            <td><small class="text-muted"><?= $d['taille_ko'] ?> Ko</small></td>
            <td><span class="badge bg-light text-dark">v<?= $d['version'] ?></span></td>
            <td><small><?= htmlspecialchars($d['uploaded_by']) ?></small></td>
            <td><small class="text-muted"><?= date('d/m/Y', strtotime($d['uploaded_at'])) ?></small></td>
            <td class="text-end pe-3"><div class="btn-group btn-group-sm">
                <a href="?share=<?= $d['id'] ?>" class="btn btn-sm btn-outline-primary" title="Partager"><i class="bi bi-share"></i></a>
                <a href="<?= BASE_URL ?>/pages/actions/download_document.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-outline-success" title="Télécharger"><i class="bi bi-download"></i></a>
                <a href="?archive=<?= $d['id'] ?>" class="btn btn-sm btn-outline-secondary" title="Archiver" onclick="return confirm('Archiver ce document ?')"><i class="bi bi-archive"></i></a>
                <a href="?delete=<?= $d['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
            </div></td>
        </tr>
        <?php endforeach;endif; ?>
        </tbody>
    </table></div></div>
</div>

<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST" enctype="multipart/form-data">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Téléverser</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <?= csrf_field() ?><input type="hidden" name="action" value="upload">
        <div class="mb-3"><label class="form-label">Élève</label><select name="utilisateur_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach (
            $students
            as $s
        ): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Type</label><select name="type_document" class="form-select" required><?php foreach (
            $typesDocuments
            as $t
        ): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Fichier (max 5 Mo)</label><input type="file" name="fichier" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Téléverser</button></div>
</form></div></div></div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
