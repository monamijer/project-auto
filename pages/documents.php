<?php
/**
 * pages/documents.php — Gestion documentaire
 * Téléversement / téléchargement / versions / suppression de documents
 * liés à un élève (permis apprenti, photo, contrat, CNI...).
 * SELECT → v_documents | CRUD → sp_ajouter_document / sp_supprimer_document
 * Fichiers physiques stockés dans /uploads/documents/{eleve_id}/
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('gestion_documents');

$message = ''; $error = '';
$uploadDir = BASE_PATH . '/uploads/documents';

// ── TÉLÉVERSER un document ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='upload') {
    $studentId = (int) $_POST['utilisateur_id'];
    $type      = trim($_POST['type_document']);

    if (!isset($_FILES['fichier']) || $_FILES['fichier']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erreur lors du téléversement du fichier.';
    } else {
        $allowedExt = ['pdf','jpg','jpeg','png','docx'];
        $ext = strtolower(pathinfo($_FILES['fichier']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $error = 'Type de fichier non autorisé (autorisés : ' . implode(', ', $allowedExt) . ').';
        } elseif ($_FILES['fichier']['size'] > 5 * 1024 * 1024) {
            $error = 'Fichier trop volumineux (max 5 Mo).';
        } else {
            $studentDir = $uploadDir . '/' . $studentId;
            if (!is_dir($studentDir)) { mkdir($studentDir, 0755, true); }

            // Nom physique unique pour éviter les collisions
            $nomFichier = uniqid('doc_') . '.' . $ext;
            $destination = $studentDir . '/' . $nomFichier;

            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $destination)) {
                $tailleKo = round($_FILES['fichier']['size'] / 1024);
                $msg = callProcedure("CALL sp_ajouter_document(?,?,?,?,?,?,@msg)",
                    [$studentId, $type, $_FILES['fichier']['name'], $studentId.'/'.$nomFichier,
                     $tailleKo, $_SESSION['username']]);

                if ($msg === 'OK') {
                    $message = 'Document téléversé avec succès !';
                    logActivity('AJOUT', 'documents', $studentId, $type . ' — ' . $_FILES['fichier']['name']);
                } else {
                    $error = $msg;
                    @unlink($destination); // rollback fichier si la BDD échoue
                }
            } else {
                $error = 'Impossible d\'enregistrer le fichier sur le serveur.';
            }
        }
    }
}

// ── SUPPRIMER un document (soft delete) ─────────────────────────────────────
if (isset($_GET['delete'])) {
    $msg = callProcedure("CALL sp_supprimer_document(?,@msg)", [(int)$_GET['delete']]);
    if ($msg === 'OK') {
        $message = 'Document supprimé.';
        logActivity('SUPPRESSION', 'documents', (int)$_GET['delete']);
    } else {
        $error = $msg;
    }
}

// ── READ ──────────────────────────────────────────────────────────────────
$documents = $pdo->query("SELECT * FROM v_documents")->fetchAll();
$students  = $pdo->query("SELECT id, nom_complet FROM v_eleves_select")->fetchAll();

$typesDocuments = ['Permis apprenti', 'Photo identité', 'Contrat', 'CNI / Passeport', 'Certificat médical', 'Autre'];

$pageTitle = 'Documents — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header d-flex flex-wrap justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="bi bi-file-earmark-arrow-up me-2"></i>Documents</h1>
    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadModal">
        <i class="bi bi-cloud-upload"></i> Téléverser
    </button>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Élève</th><th>Type</th><th>Fichier</th><th>Taille</th><th>Version</th><th>Téléversé par</th><th>Date</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($documents as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['eleve_nom']) ?></td>
                    <td><span class="badge bg-info"><?= htmlspecialchars($d['type_document']) ?></span></td>
                    <td><?= htmlspecialchars($d['nom_original']) ?></td>
                    <td><?= $d['taille_ko'] ?> Ko</td>
                    <td><span class="badge bg-secondary">v<?= $d['version'] ?></span></td>
                    <td><?= htmlspecialchars($d['uploaded_by']) ?></td>
                    <td><small><?= htmlspecialchars($d['uploaded_at']) ?></small></td>
                    <td>
                        <a href="<?= BASE_URL ?>/pages/actions/download_document.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-success" title="Télécharger"><i class="bi bi-download"></i></a>
                        <a href="?delete=<?= $d['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer" onclick="return confirm('Supprimer ce document ?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($documents)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">Aucun document téléversé.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modale Téléverser -->
<div class="modal fade" id="uploadModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST" enctype="multipart/form-data">
    <div class="modal-header"><h5 class="modal-title">Téléverser un document</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="action" value="upload">
        <div class="mb-3"><label class="form-label">Élève</label>
            <select name="utilisateur_id" class="form-select" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?>
            </select></div>
        <div class="mb-3"><label class="form-label">Type de document</label>
            <select name="type_document" class="form-select" required>
                <?php foreach ($typesDocuments as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?>
            </select></div>
        <div class="mb-3"><label class="form-label">Fichier (PDF, JPG, PNG, DOCX — max 5 Mo)</label>
            <input type="file" name="fichier" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required></div>
        <div class="alert alert-info small mb-0">
            <i class="bi bi-info-circle"></i> Si un document du même type existe déjà pour cet élève, une nouvelle <strong>version</strong> sera créée automatiquement.
        </div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Téléverser</button></div>
  </form></div></div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
