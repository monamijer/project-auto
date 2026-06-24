<?php
/**
 * pages/documents.php — Gestion documentaire
 * Téléversement / téléchargement / versions / suppression de documents
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('gestion_documents');

$message = ''; $error = '';

// ── TÉLÉVERSER ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='upload') {
    $studentId = (int) $_POST['utilisateur_id'];
    $type      = trim($_POST['type_document']);

    if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erreur lors du téléversement.';
    } else {
        $allowedExt = ['pdf','jpg','jpeg','png','docx'];
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $error = 'Type non autorisé (' . implode(', ', $allowedExt) . ').';
        } elseif ($_FILES['fichier']['size'] > 5 * 1024 * 1024) {
            $error = 'Fichier trop volumineux (max 5 Mo).';
        } else {
            $uploadDir = BASE_PATH . '/uploads/documents';
            if (!is_dir($uploadDir)) { mkdir($uploadDir, 0777, true); }
            
            $studentDir = $uploadDir . '/' . $studentId;
            if (!is_dir($studentDir)) { mkdir($studentDir, 0777, true); }

            $nomFichier = 'doc_' . uniqid() . '.' . $ext;
            $destination = $studentDir . '/' . $nomFichier;

            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $destination)) {
                chmod($destination, 0644);
                $tailleKo = round($_FILES['fichier']['size'] / 1024);
                $msg = callProcedure("CALL sp_ajouter_document(?,?,?,?,?,?,@msg)",
                    [$studentId, $type, $_FILES['fichier']['name'], $studentId.'/'.$nomFichier, $tailleKo, $_SESSION['username']]);

                if ($msg === 'OK') {
                    $message = 'Document téléversé !';
                    logActivity('AJOUT', 'documents', $studentId, $type);
                } else {
                    $error = $msg;
                    @unlink($destination);
                }
            } else {
                $error = 'Impossible d\'enregistrer le fichier.';
            }
        }
    }
}

// ── SUPPRIMER ─────────────────────────────────────────────────────────────
if (isset($_GET['delete'])) {
    $msg = callProcedure("CALL sp_supprimer_document(?,@msg)", [(int)$_GET['delete']]);
    $msg === 'OK' ? $message = 'Document supprimé.' : $error = $msg;
}

// ── READ ──────────────────────────────────────────────────────────────────
$documents = $pdo->query("SELECT * FROM v_documents")->fetchAll();
$students  = $pdo->query("SELECT id, nom_complet FROM v_eleves_select")->fetchAll();
$typesDocuments = ['Permis apprenti', 'Photo identité', 'Contrat', 'CNI / Passeport', 'Certificat médical', 'Autre'];

$pageTitle = 'Documents — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>Documents</h1>
        <p class="text-muted mb-0"><?= count($documents) ?> document(s)</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-cloud-upload me-1"></i>Téléverser
    </button>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste des documents</h5>
        <span class="badge bg-primary rounded-pill"><?= count($documents) ?> total</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3">Élève</th>
                        <th>Type</th>
                        <th>Fichier</th>
                        <th>Taille</th>
                        <th>Version</th>
                        <th>Ajouté par</th>
                        <th>Date</th>
                        <th class="text-end pe-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($documents)): ?>
                <tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>Aucun document téléversé</td></tr>
                <?php else: ?>
                <?php foreach ($documents as $d): ?>
                <tr>
                    <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($d['eleve_nom']) ?></span></td>
                    <td><span class="badge bg-info bg-opacity-10 text-info"><?= htmlspecialchars($d['type_document']) ?></span></td>
                    <td><small><?= htmlspecialchars($d['nom_original']) ?></small></td>
                    <td><small class="text-muted"><?= $d['taille_ko'] ?> Ko</small></td>
                    <td><span class="badge bg-light text-dark">v<?= $d['version'] ?></span></td>
                    <td><small><?= htmlspecialchars($d['uploaded_by']) ?></small></td>
                    <td><small class="text-muted"><?= date('d/m/Y', strtotime($d['uploaded_at'])) ?></small></td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <a href="<?= BASE_URL ?>/pages/actions/download_document.php?id=<?= $d['id'] ?>" class="btn btn-outline-success" title="Télécharger"><i class="bi bi-download"></i></a>
                            <a href="?delete=<?= $d['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer ce document ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content"><form method="POST" enctype="multipart/form-data">
        <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Téléverser un document</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="upload">
            <div class="mb-3"><label class="form-label">Élève</label><select name="utilisateur_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Type</label><select name="type_document" class="form-select" required><?php foreach ($typesDocuments as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?></select></div>
            <div class="mb-3"><label class="form-label">Fichier (PDF, JPG, PNG, DOCX — max 5 Mo)</label><input type="file" name="fichier" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required></div>
            <div class="alert alert-info small mb-0 py-2">Une nouvelle <strong>version</strong> sera créée si le type existe déjà pour cet élève.</div>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Téléverser</button></div>
    </form></div></div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>