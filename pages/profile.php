<?php
/**
 * pages/profile.php — Mon profil
 * Tout utilisateur (admin ou stagiaire) peut consulter son profil,
 * voir son propre historique de connexion, et changer son mot de passe.
 * SELECT → v_comptes, v_journal (filtré par utilisateur)
 * Actions → sp_changer_mot_de_passe, sp_modifier_mon_profil
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();

$message = ''; $error = '';

// ── Changer son propre mot de passe ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='changer_mdp') {
    // Vérifie l'ancien mot de passe avant d'autoriser le changement
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM expirations_utilisateurs WHERE id=?");
    $stmt->execute([$_SESSION['user_id']]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($_POST['ancien_mdp'], $hash)) {
        $error = 'Ancien mot de passe incorrect.';
    } elseif ($_POST['nouveau_mdp'] !== $_POST['confirmer_mdp']) {
        $error = 'Les nouveaux mots de passe ne correspondent pas.';
    } else {
        $newHash = password_hash($_POST['nouveau_mdp'], PASSWORD_BCRYPT);
        $msg = callProcedure("CALL sp_changer_mot_de_passe(?,?,@msg)", [$_SESSION['user_id'], $newHash]);
        $msg==='OK' ? $message='Mot de passe changé avec succès !' : $error=$msg;
    }
}

// ── Modifier le commentaire/bio de son profil ──────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='edit_profile') {
    $msg = callProcedure("CALL sp_modifier_mon_profil(?,?,@msg)",
        [$_SESSION['user_id'], trim($_POST['commentaire'] ?? '')]);
    $msg==='OK' ? $message='Profil mis à jour !' : $error=$msg;
}

// ── READ : mon compte + mon historique de connexion ────────────────────────
$stmt = $pdo->prepare("SELECT * FROM v_comptes WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$monCompte = $stmt->fetch();

$stmt = $pdo->prepare("SELECT * FROM journal_connexions WHERE utilisateur = ? ORDER BY heure_connexion DESC LIMIT 20");
$stmt->execute([$_SESSION['username']]);
$monHistorique = $stmt->fetchAll();

$pageTitle = 'Mon Profil — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2"><i class="bi bi-person-circle me-2"></i>Mon Profil</h1>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<div class="row">
    <!-- Infos compte -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Informations du compte</h5></div>
            <div class="card-body">
                <p><strong>Identifiant :</strong> <?= htmlspecialchars($monCompte['utilisateur']) ?></p>
                <p><strong>Rôle :</strong>
                    <span class="badge <?= isAdmin()?'bg-warning text-dark':'bg-secondary' ?>">
                        <?= isAdmin()?'Administrateur':'Stagiaire' ?>
                    </span>
                </p>
                <p><strong>Compte valide jusqu'au :</strong> <?= htmlspecialchars($monCompte['date_expiration']) ?></p>
                <p class="mb-0"><strong>Statut :</strong>
                    <span class="badge bg-success"><?= htmlspecialchars($monCompte['statut_reel']) ?></span>
                </p>
            </div>
        </div>

        <!-- Modifier commentaire -->
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0">Note personnelle</h5></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="edit_profile">
                    <textarea name="commentaire" class="form-control mb-2" rows="2"
                        placeholder="Note libre visible par les admins"><?= htmlspecialchars($monCompte['commentaire'] ?? '') ?></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Changer mot de passe -->
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header bg-white"><h5 class="mb-0"><i class="bi bi-key"></i> Changer mon mot de passe</h5></div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="changer_mdp">
                    <div class="mb-3"><label class="form-label">Mot de passe actuel</label>
                        <input type="password" name="ancien_mdp" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Nouveau mot de passe</label>
                        <input type="password" name="nouveau_mdp" class="form-control" minlength="6" required></div>
                    <div class="mb-3"><label class="form-label">Confirmer</label>
                        <input type="password" name="confirmer_mdp" class="form-control" minlength="6" required></div>
                    <button type="submit" class="btn btn-warning">Changer le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mon historique de connexion -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Mon historique de connexion (20 dernières)</h5></div>
    <div class="card-body p-0">
        <table class="table table-sm mb-0">
            <thead><tr><th>Heure</th><th>Statut</th><th>Message</th></tr></thead>
            <tbody>
            <?php foreach ($monHistorique as $h): ?>
            <tr>
                <td><?= htmlspecialchars($h['heure_connexion']) ?></td>
                <td><span class="badge <?= $h['statut']==='AUTORISÉE'?'bg-success':'bg-danger' ?>"><?= htmlspecialchars($h['statut']) ?></span></td>
                <td><?= htmlspecialchars($h['message'] ?? '') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
