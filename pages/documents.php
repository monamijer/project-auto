<?php
/**
 * pages/documents.php — Gestion documentaire avec AJAX suggestions
 * Téléversement / téléchargement / versions / suppression / partage / recherche AJAX
 * SELECT → v_documents | CRUD → procédures stockées
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('gestion_documents');

$message = '';
$error = '';

// ── Recherche ─────────────────────────────────────────────────────────────
$q_doc   = trim($_GET['q'] ?? '');
$q_type  = $_GET['type_doc'] ?? '';
$q_eleve = (int)($_GET['eleve_id'] ?? 0);

// ── AJAX : Suggestions de recherche ───────────────────────────────────────
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    if (isset($_GET['suggest'])) {
        $term = trim($_GET['suggest']);
        $results = [];
        if (strlen($term) >= 2) {
            $stmt = $pdo->prepare("SELECT DISTINCT eleve_nom FROM v_documents WHERE eleve_nom LIKE ? LIMIT 8");
            $stmt->execute(["%$term%"]);
            $results = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }
        echo json_encode($results);
        exit();
    }
    
    // Rafraîchir le tableau via AJAX
    if (isset($_GET['refresh'])) {
        $sqlDoc = "SELECT * FROM v_documents WHERE 1=1";
        $pDoc = [];
        if ($q_doc)   { $sqlDoc .= " AND (nom_original LIKE ? OR eleve_nom LIKE ?)"; $pDoc[] = "%$q_doc%"; $pDoc[] = "%$q_doc%"; }
        if ($q_type)  { $sqlDoc .= " AND type_document = ?"; $pDoc[] = $q_type; }
        if ($q_eleve) { $sqlDoc .= " AND utilisateur_id = ?"; $pDoc[] = $q_eleve; }
        $sqlDoc .= " ORDER BY uploaded_at DESC";
        $stmtDoc = $pdo->prepare($sqlDoc);
        $stmtDoc->execute($pDoc);
        $docs = $stmtDoc->fetchAll();
        
        ob_start();
        if (empty($docs)) {
            echo '<tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>Aucun document trouvé</td></tr>';
        } else {
            foreach ($docs as $d): ?>
            <tr id="doc-row-<?= $d['id'] ?>">
                <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($d['eleve_nom']) ?></span></td>
                <td><span class="badge bg-info bg-opacity-10 text-info"><?= htmlspecialchars($d['type_document']) ?></span></td>
                <td><small><?= htmlspecialchars($d['nom_original']) ?></small></td>
                <td><small class="text-muted"><?= $d['taille_ko'] ?> Ko</small></td>
                <td><span class="badge bg-light text-dark">v<?= $d['version'] ?></span></td>
                <td><small><?= htmlspecialchars($d['uploaded_by']) ?></small></td>
                <td><small class="text-muted"><?= date('d/m/Y', strtotime($d['uploaded_at'])) ?></small></td>
                <td class="text-end pe-3"><div class="btn-group btn-group-sm">
                    <button class="btn btn-outline-primary btn-share" data-id="<?= $d['id'] ?>" title="Partager"><i class="bi bi-share"></i></button>
                    <a href="<?= BASE_URL ?>/pages/actions/download_document.php?id=<?= $d['id'] ?>" class="btn btn-outline-success" title="Télécharger"><i class="bi bi-download"></i></a>
                    <button class="btn btn-outline-secondary btn-archive" data-id="<?= $d['id'] ?>" title="Archiver"><i class="bi bi-archive"></i></button>
                    <button class="btn btn-outline-danger btn-delete" data-id="<?= $d['id'] ?>" title="Supprimer"><i class="bi bi-trash"></i></button>
                </div></td>
            </tr>
            <?php endforeach;
        }
        $html = ob_get_clean();
        echo json_encode(['html' => $html, 'count' => count($docs)]);
        exit();
    }
}

// ── Partage ───────────────────────────────────────────────────────────────
if (isset($_GET['share'])) {
    $sid   = (int)$_GET['share'];
    $token = bin2hex(random_bytes(16));
    $pdo->prepare("UPDATE documents SET share_token=?, share_expire=DATE_ADD(NOW(),INTERVAL 7 DAY) WHERE id=?")->execute([$token, $sid]);
    $shareUrl = BASE_URL . '/pages/actions/shared_doc.php?token=' . $token;
    $message  = 'Lien de partage copié !';
}

// ── TÉLÉVERSER ────────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload') {
    $studentId = (int)$_POST['utilisateur_id'];
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
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
            $studentDir = $uploadDir . '/' . $studentId;
            if (!is_dir($studentDir)) mkdir($studentDir, 0777, true);
            $nomFichier = 'doc_' . uniqid() . '.' . $ext;
            $destination = $studentDir . '/' . $nomFichier;
            if (move_uploaded_file($_FILES['fichier']['tmp_name'], $destination)) {
                chmod($destination, 0644);
                $tailleKo = round($_FILES['fichier']['size'] / 1024);
                $msg = callProcedure('CALL sp_ajouter_document(?,?,?,?,?,?,@msg)', [$studentId, $type, $_FILES['fichier']['name'], $studentId . '/' . $nomFichier, $tailleKo, $_SESSION['username']]);
                $msg === 'OK' ? ($message = 'Document téléversé !') : ($error = $msg);
            } else { $error = 'Erreur enregistrement.'; }
        }
    }
}

// ── Supprimer / Archiver ──────────────────────────────────────────────────
if (isset($_GET['delete'])) { callProcedure('CALL sp_supprimer_document(?,@msg)', [(int)$_GET['delete']]); $message = 'Supprimé.'; }
if (isset($_GET['archive'])) { callProcedure('CALL sp_supprimer_document(?,@msg)', [(int)$_GET['archive']]); $message = 'Archivé.'; }

// ── READ initial ──────────────────────────────────────────────────────────
$sqlDoc = "SELECT * FROM v_documents WHERE 1=1";
$pDoc = [];
if ($q_doc)   { $sqlDoc .= " AND (nom_original LIKE ? OR eleve_nom LIKE ?)"; $pDoc[] = "%$q_doc%"; $pDoc[] = "%$q_doc%"; }
if ($q_type)  { $sqlDoc .= " AND type_document = ?"; $pDoc[] = $q_type; }
if ($q_eleve) { $sqlDoc .= " AND utilisateur_id = ?"; $pDoc[] = $q_eleve; }
$sqlDoc .= " ORDER BY uploaded_at DESC";
$stmtDoc = $pdo->prepare($sqlDoc);
$stmtDoc->execute($pDoc);
$documents = $stmtDoc->fetchAll();

$students = $pdo->query('SELECT id, nom_complet FROM v_eleves_select')->fetchAll();
$typesDocuments = ['Permis apprenti', 'Photo identité', 'Contrat', 'CNI / Passeport', 'Certificat médical', 'Autre'];

$pageTitle = 'Documents — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<style>
.search-wrapper { position: relative; }
.suggestions-dropdown { position: absolute; top: 100%; left: 0; right: 0; z-index: 1000; background: #fff; border: 1px solid #e5e7eb; border-radius: 0 0 8px 8px; box-shadow: 0 8px 20px rgba(0,0,0,0.1); display: none; max-height: 200px; overflow-y: auto; }
.suggestions-dropdown.show { display: block; }
.suggestions-dropdown .suggestion-item { padding: 0.5rem 1rem; cursor: pointer; font-size: 0.85rem; border-bottom: 1px solid #f3f4f6; }
.suggestions-dropdown .suggestion-item:hover { background: #eef2ff; }
</style>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h3 mb-1"><i class="bi bi-file-earmark-arrow-up me-2 text-primary"></i>Documents</h1><p class="text-muted mb-0"><span id="docCount"><?= count($documents) ?></span> document(s)</p></div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#uploadModal"><i class="bi bi-cloud-upload me-1"></i>Téléverser</button>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-check-circle-fill me-2"></i><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Barre de recherche avec suggestions -->
<form method="GET" class="card shadow-sm border-0 mb-4" id="searchForm">
    <div class="card-body py-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-medium mb-1">Recherche</label>
                <div class="search-wrapper">
                    <input type="text" name="q" id="searchInput" class="form-control" value="<?= htmlspecialchars($q_doc) ?>" placeholder="Nom fichier, élève..." autocomplete="off">
                    <div class="suggestions-dropdown" id="suggestions"></div>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium mb-1">Type</label>
                <select name="type_doc" class="form-select"><option value="">Tous</option><?php foreach ($typesDocuments as $t): ?><option value="<?= $t ?>" <?= $q_type === $t ? 'selected' : '' ?>><?= $t ?></option><?php endforeach; ?></select>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-medium mb-1">Élève</label>
                <select name="eleve_id" class="form-select"><option value="0">Tous</option><?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>" <?= $q_eleve == $s['id'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?></select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search me-1"></i>Filtrer</button>
            </div>
        </div>
    </div>
</form>

<!-- Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Liste</h5><span class="badge bg-primary rounded-pill" id="totalBadge"><?= count($documents) ?></span></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="ps-3">Élève</th><th>Type</th><th>Fichier</th><th>Taille</th><th>Version</th><th>Ajouté par</th><th>Date</th><th class="text-end pe-3">Actions</th></tr></thead>
        <tbody id="documentsBody">
        <?php if (empty($documents)): ?><tr><td colspan="8" class="text-center py-5 text-muted"><i class="bi bi-file-earmark-x fs-1 d-block mb-2"></i>Aucun document</td></tr>
        <?php else: foreach ($documents as $d): ?>
        <tr id="doc-row-<?= $d['id'] ?>">
            <td class="ps-3"><span class="fw-medium"><?= htmlspecialchars($d['eleve_nom']) ?></span></td>
            <td><span class="badge bg-info bg-opacity-10 text-info"><?= htmlspecialchars($d['type_document']) ?></span></td>
            <td><small><?= htmlspecialchars($d['nom_original']) ?></small></td>
            <td><small class="text-muted"><?= $d['taille_ko'] ?> Ko</small></td>
            <td><span class="badge bg-light text-dark">v<?= $d['version'] ?></span></td>
            <td><small><?= htmlspecialchars($d['uploaded_by']) ?></small></td>
            <td><small class="text-muted"><?= date('d/m/Y', strtotime($d['uploaded_at'])) ?></small></td>
            <td class="text-end pe-3"><div class="btn-group btn-group-sm">
                <button class="btn btn-outline-primary btn-share" data-id="<?= $d['id'] ?>" title="Partager"><i class="bi bi-share"></i></button>
                <a href="<?= BASE_URL ?>/pages/actions/download_document.php?id=<?= $d['id'] ?>" class="btn btn-outline-success" title="Télécharger"><i class="bi bi-download"></i></a>
                <button class="btn btn-outline-secondary btn-archive" data-id="<?= $d['id'] ?>" title="Archiver"><i class="bi bi-archive"></i></button>
                <button class="btn btn-outline-danger btn-delete" data-id="<?= $d['id'] ?>" title="Supprimer"><i class="bi bi-trash"></i></button>
            </div></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
    </table></div></div>
</div>

<!-- Modal -->
<div class="modal fade" id="uploadModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST" enctype="multipart/form-data">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-cloud-upload me-2"></i>Téléverser</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <?= csrf_field() ?><input type="hidden" name="action" value="upload">
        <div class="mb-3"><label class="form-label">Élève</label><select name="utilisateur_id" class="form-select" required><option value="">-- Choisir --</option><?php foreach ($students as $s): ?><option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['nom_complet']) ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Type</label><select name="type_document" class="form-select" required><?php foreach ($typesDocuments as $t): ?><option value="<?= $t ?>"><?= $t ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Fichier (max 5 Mo)</label><input type="file" name="fichier" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.docx" required></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Téléverser</button></div>
</form></div></div></div>

<script>
const BASE = '<?= BASE_URL ?>';
let debounceTimer;

// ============ SUGGESTIONS AJAX ============
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(debounceTimer);
    const val = this.value.trim();
    const dropdown = document.getElementById('suggestions');
    
    if (val.length < 2) { dropdown.classList.remove('show'); return; }
    
    debounceTimer = setTimeout(async () => {
        const resp = await fetch(`?suggest=${encodeURIComponent(val)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await resp.json();
        if (data.length > 0) {
            dropdown.innerHTML = data.map(n => `<div class="suggestion-item">${n}</div>`).join('');
            dropdown.classList.add('show');
            dropdown.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', function() {
                    document.getElementById('searchInput').value = this.textContent;
                    dropdown.classList.remove('show');
                    refreshTable();
                });
            });
        } else {
            dropdown.classList.remove('show');
        }
    }, 300);
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.search-wrapper')) {
        document.getElementById('suggestions').classList.remove('show');
    }
});

// ============ RAFRAÎCHIR LE TABLEAU VIA AJAX ============
async function refreshTable() {
    const q = document.getElementById('searchInput').value;
    const type = document.querySelector('[name="type_doc"]').value;
    const eleve = document.querySelector('[name="eleve_id"]').value;
    const params = new URLSearchParams({ refresh: 1, q, type_doc: type, eleve_id: eleve });
    
    const resp = await fetch('?' + params, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await resp.json();
    document.getElementById('documentsBody').innerHTML = data.html;
    document.getElementById('docCount').textContent = data.count;
    document.getElementById('totalBadge').textContent = data.count;
    attachEvents();
}

// ============ ACTIONS SUR LES BOUTONS ============
function attachEvents() {
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Supprimer ?')) return;
            await fetch('?delete=' + this.dataset.id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            refreshTable();
        });
    });
    document.querySelectorAll('.btn-archive').forEach(btn => {
        btn.addEventListener('click', async function() {
            if (!confirm('Archiver ?')) return;
            await fetch('?archive=' + this.dataset.id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            refreshTable();
        });
    });
    document.querySelectorAll('.btn-share').forEach(btn => {
        btn.addEventListener('click', async function() {
            const resp = await fetch('?share=' + this.dataset.id, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const html = await resp.text();
            const match = html.match(/shared_doc\.php\?token=([a-f0-9]+)/);
            if (match) {
                const url = BASE + '/pages/actions/shared_doc.php?token=' + match[1];
                await navigator.clipboard.writeText(url);
                alert('✅ Lien copié (7 jours) !');
            }
        });
    });
}

// Filtres : rafraîchir au changement
document.querySelector('[name="type_doc"]').addEventListener('change', refreshTable);
document.querySelector('[name="eleve_id"]').addEventListener('change', refreshTable);

attachEvents();
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>