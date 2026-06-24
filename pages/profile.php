<?php
/**
 * pages/profile.php — Mon profil
 * Tout utilisateur (admin ou stagiaire) peut consulter son profil,
 * voir son propre historique de connexion, changer son mot de passe
 * et téléverser sa photo de profil.
 * SELECT → v_comptes, v_journal (filtré par utilisateur)
 * Actions → sp_changer_mot_de_passe, sp_modifier_mon_profil
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = '';
$error = '';

// ── Téléverser photo de profil ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'upload_photo') {
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Erreur lors du téléversement.';
    } else {
        $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            $error = 'Format non autorisé (JPG, PNG, GIF, WEBP).';
        } elseif ($_FILES['photo']['size'] > 2 * 1024 * 1024) {
            $error = 'Image trop volumineuse (max 2 Mo).';
        } else {
            $uploadDir = BASE_PATH . '/uploads/profiles';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $nomFichier = 'profile_' . $_SESSION['user_id'] . '.' . $ext;
            $destination = $uploadDir . '/' . $nomFichier;

            // Supprimer l'ancienne photo si elle existe (tous formats)
            foreach (glob($uploadDir . '/profile_' . $_SESSION['user_id'] . '.*') as $ancien) {
                @unlink($ancien);
            }

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $destination)) {
                chmod($destination, 0644);
                $message = 'Photo de profil mise à jour !';
            } else {
                $error = 'Impossible d\'enregistrer la photo.';
            }
        }
    }
}

// ── Supprimer la photo de profil ───────────────────────────────────────────
if (isset($_GET['delete_photo'])) {
    $uploadDir = BASE_PATH . '/uploads/profiles';
    foreach (glob($uploadDir . '/profile_' . $_SESSION['user_id'] . '.*') as $fichier) {
        @unlink($fichier);
    }
    $message = 'Photo de profil supprimée.';
}

// ── Changer son propre mot de passe ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'changer_mdp') {
    $stmt = $pdo->prepare('SELECT mot_de_passe FROM expirations_utilisateurs WHERE id=?');
    $stmt->execute([$_SESSION['user_id']]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($_POST['ancien_mdp'], $hash)) {
        $error = 'Ancien mot de passe incorrect.';
    } elseif ($_POST['nouveau_mdp'] !== $_POST['confirmer_mdp']) {
        $error = 'Les nouveaux mots de passe ne correspondent pas.';
    } else {
        $newHash = password_hash($_POST['nouveau_mdp'], PASSWORD_BCRYPT);
        $msg = callProcedure('CALL sp_changer_mot_de_passe(?,?,@msg)', [$_SESSION['user_id'], $newHash]);
        $msg === 'OK' ? ($message = 'Mot de passe changé avec succès !') : ($error = $msg);
    }
}

// ── Modifier le commentaire/bio de son profil ──────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_profile') {
    $msg = callProcedure('CALL sp_modifier_mon_profil(?,?,@msg)', [$_SESSION['user_id'], trim($_POST['commentaire'] ?? '')]);
    $msg === 'OK' ? ($message = 'Profil mis à jour !') : ($error = $msg);
}

// ── READ : mon compte + mon historique de connexion ────────────────────────
$stmt = $pdo->prepare('SELECT * FROM v_comptes WHERE id = ?');
$stmt->execute([$_SESSION['user_id']]);
$monCompte = $stmt->fetch();

$stmt = $pdo->prepare('SELECT * FROM journal_connexions WHERE utilisateur = ? ORDER BY heure_connexion DESC LIMIT 20');
$stmt->execute([$_SESSION['username']]);
$monHistorique = $stmt->fetchAll();

// ── Vérifier si l'utilisateur a une photo de profil ───────────────────────
$photoExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$photoProfil = null;
foreach ($photoExtensions as $ext) {
    $path = BASE_PATH . '/uploads/profiles/profile_' . $_SESSION['user_id'] . '.' . $ext;
    if (file_exists($path)) {
        $photoProfil = BASE_URL . '/uploads/profiles/profile_' . $_SESSION['user_id'] . '.' . $ext;
        break;
    }
}

$pageTitle = 'Mon Profil — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-person-circle me-2 text-primary"></i>Mon Profil</h1>
        <p class="text-muted mb-0">Gérez vos informations personnelles</p>
    </div>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars(
    $message
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars(
    $error
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row g-3">
    <!-- Photo + Infos -->
    <div class="col-lg-4">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-body text-center">
                <div class="mb-3">
                    <?php if ($photoProfil): ?>
                        <img src="<?= $photoProfil ?>" alt="Photo de profil" class="rounded-circle" style="width:120px;height:120px;object-fit:cover;">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width:120px;height:120px;">
                            <span class="text-primary display-5 fw-bold"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <h5 class="mb-1"><?= htmlspecialchars($_SESSION['username']) ?></h5>
                <span class="badge <?= isAdmin() ? 'bg-warning text-dark' : 'bg-secondary' ?> mb-3"><?= isAdmin() ? 'Administrateur' : 'Stagiaire' ?></span>

                <div class="d-flex justify-content-center gap-2">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#photoModal">
                        <i class="bi bi-camera me-1"></i><?= $photoProfil ? 'Changer' : 'Ajouter' ?>
                    </button>
                    <?php if ($photoProfil): ?>
                    <a href="?delete_photo=1" class="btn btn-sm btn-outline-danger" onclick="return confirm('Supprimer la photo ?')">
                        <i class="bi bi-trash"></i>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted">INFORMATIONS</h5></div>
            <div class="card-body">
                <div class="mb-2"><small class="text-muted">Identifiant</small><br><strong><?= htmlspecialchars($monCompte['utilisateur']) ?></strong></div>
                <div class="mb-2"><small class="text-muted">Expiration</small><br><strong><?= htmlspecialchars($monCompte['date_expiration']) ?></strong></div>
                <div class="mb-0"><small class="text-muted">Statut</small><br><span class="badge bg-success bg-opacity-10 text-success"><?= htmlspecialchars($monCompte['statut_reel']) ?></span></div>
            </div>
        </div>
    </div>

    <!-- Mot de passe + Note -->
    <div class="col-lg-8">
        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted"><i class="bi bi-key me-2"></i>CHANGER MON MOT DE PASSE</h5></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="changer_mdp">
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label">Mot de passe actuel</label><input type="password" name="ancien_mdp" class="form-control" required></div>
                        <div class="col-md-4"><label class="form-label">Nouveau mot de passe</label><input type="password" name="nouveau_mdp" class="form-control" minlength="6" required></div>
                        <div class="col-md-4"><label class="form-label">Confirmer</label><input type="password" name="confirmer_mdp" class="form-control" minlength="6" required></div>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm mt-3">Changer le mot de passe</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm border-0 mb-3">
            <div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted"><i class="bi bi-pencil me-2"></i>NOTE PERSONNELLE</h5></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="edit_profile">
                    <textarea name="commentaire" class="form-control mb-2" rows="2" placeholder="Note libre visible par les admins"><?= htmlspecialchars($monCompte['commentaire'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Historique -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0 small fw-bold text-muted">HISTORIQUE DE CONNEXION</h5></div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th class="ps-3">Heure</th><th>Statut</th><th>Message</th></tr></thead>
            <tbody>
            <?php if (empty($monHistorique)): ?>
            <tr><td colspan="3" class="text-center py-4 text-muted">Aucune connexion enregistrée</td></tr>
            <?php else: ?>
            <?php foreach ($monHistorique as $h): ?>
            <tr>
                <td class="ps-3"><small><?= htmlspecialchars($h['heure_connexion']) ?></small></td>
                <td><span class="badge <?= $h['statut'] === 'AUTORISÉE' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?>"><?= htmlspecialchars(
    $h['statut']
) ?></span></td>
                <td><small><?= htmlspecialchars($h['message'] ?? '') ?></small></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Photo -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-sm"><div class="modal-content"><form method="POST" enctype="multipart/form-data">
        <div class="modal-header"><h5 class="modal-title">Photo de profil</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <div class="modal-body">
            <input type="hidden" name="action" value="upload_photo">
            <div class="mb-2"><label class="form-label">Choisir une image</label><input type="file" name="photo" class="form-control" accept=".jpg,.jpeg,.png,.gif,.webp" required></div>
            <small class="text-muted">JPG, PNG, GIF ou WEBP — max 2 Mo</small>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary btn-sm">Téléverser</button></div>
    </form></div></div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
