<?php
/**
 * pages/settings.php — Paramètres (ADMIN UNIQUEMENT)
 * SELECT → v_comptes, v_journal, v_sys_info
 * CRUD   → sp_ajouter/modifier/renouveler_compte, sp_changer_mot_de_passe,
 *          sp_bloquer_utilisateur / sp_debloquer_utilisateur, sp_supprimer_compte
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('voir_parametres');

$message = ''; $error = '';

if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='add_compte') {
    requirePermission('gestion_comptes');
    $hash = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $msg  = callProcedure("CALL sp_ajouter_compte(?,?,?,?,?,?,@msg)",
        [trim($_POST['utilisateur']), $hash, $_POST['role'], $_POST['date_expiration'], $_POST['statut'], trim($_POST['commentaire']??'')]);
    $msg==='OK' ? $message='Compte créé !' : $error=$msg;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='edit_compte') {
    requirePermission('gestion_comptes');
    $msg = callProcedure("CALL sp_modifier_compte(?,?,?,?,?,@msg)",
        [(int)$_POST['id'], $_POST['role'], $_POST['date_expiration'], $_POST['statut'], trim($_POST['commentaire']??'')]);
    $msg==='OK' ? $message='Compte mis à jour !' : $error=$msg;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='renouveler') {
    requirePermission('gestion_comptes');
    $msg = callProcedure("CALL sp_renouveler_compte(?,?,@msg)", [(int)$_POST['id'], $_POST['nouvelle_expiration']]);
    $msg==='OK' ? $message='Compte renouvelé et réactivé !' : $error=$msg;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='changer_mdp') {
    requirePermission('gestion_comptes');
    if ($_POST['nouveau_mdp'] !== $_POST['confirmer_mdp']) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($_POST['nouveau_mdp'], PASSWORD_BCRYPT);
        $msg  = callProcedure("CALL sp_changer_mot_de_passe(?,?,@msg)", [(int)$_POST['id'], $hash]);
        $msg==='OK' ? $message='Mot de passe changé !' : $error=$msg;
    }
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='bloquer') {
    requirePermission('gestion_comptes');
    if ((int)$_POST['id'] === (int)$_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas vous bloquer vous-même.';
    } else {
        $msg = callProcedure("CALL sp_bloquer_utilisateur(?,?,@msg)", [(int)$_POST['id'], trim($_POST['raison'] ?: 'Comportement inapproprié')]);
        $msg==='OK' ? $message='Utilisateur bloqué.' : $error=$msg;
    }
}
if (isset($_GET['debloquer'])) {
    requirePermission('gestion_comptes');
    $msg = callProcedure("CALL sp_debloquer_utilisateur(?,@msg)", [(int)$_GET['debloquer']]);
    $msg==='OK' ? $message='Utilisateur débloqué.' : $error=$msg;
}
if (isset($_GET['delete_compte'])) {
    requirePermission('gestion_comptes');
    if ((int)$_GET['delete_compte'] === (int)$_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas supprimer votre propre compte.';
    } else {
        $msg = callProcedure("CALL sp_supprimer_compte(?,@msg)", [(int)$_GET['delete_compte']]);
        $msg==='OK' ? $message='Compte supprimé.' : $error=$msg;
    }
}

$comptes = $pdo->query("SELECT * FROM v_comptes ORDER BY utilisateur")->fetchAll();
$journal = $pdo->query("SELECT * FROM v_journal")->fetchAll();
$sysInfo = $pdo->query("SELECT * FROM v_sys_info")->fetch();

$pageTitle = 'Paramètres — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1"><i class="bi bi-gear me-2 text-primary"></i>Paramètres</h1>
        <p class="text-muted mb-0">Administration du système</p>
    </div>
    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Zone Admin</span>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Comptes -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0"><i class="bi bi-people me-2"></i>Comptes utilisateurs</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCompteModal"><i class="bi bi-person-plus me-1"></i>Ajouter</button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th class="ps-3">Identifiant</th><th>Rôle</th><th>Expiration</th><th>Statut</th><th>Commentaire</th><th class="text-end pe-3">Actions</th></tr>
                </thead>
                <tbody>
                <?php foreach ($comptes as $c):
                    $badgeColor = match($c['statut_reel']) {
                        'actif' => 'bg-success bg-opacity-10 text-success', 'expire_bientot' => 'bg-warning bg-opacity-10 text-warning',
                        'expiré' => 'bg-danger bg-opacity-10 text-danger', 'suspendu' => 'bg-dark bg-opacity-10 text-dark', default => 'bg-secondary bg-opacity-10 text-secondary',
                    };
                    $badgeLabel = match($c['statut_reel']) {
                        'expire_bientot' => 'Expire bientôt', 'suspendu' => 'Bloqué',
                        default => ucfirst($c['statut_reel']),
                    };
                ?>
                <tr>
                    <td class="ps-3">
                        <strong><?= htmlspecialchars($c['utilisateur']) ?></strong>
                        <?= $c['id']==$_SESSION['user_id'] ? '<span class="badge bg-info bg-opacity-10 text-info ms-1">Vous</span>' : '' ?>
                    </td>
                    <td><span class="badge bg-light text-dark"><?= $c['role'] ?></span></td>
                    <td><small><?= htmlspecialchars($c['date_expiration']) ?></small></td>
                    <td><span class="badge <?= $badgeColor ?>"><?= $badgeLabel ?></span></td>
                    <td><small class="text-muted"><?= htmlspecialchars($c['commentaire'] ?? '—') ?></small></td>
                    <td class="text-end pe-3">
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editCompteModal-<?= $c['id'] ?>" title="Modifier"><i class="bi bi-pencil"></i></button>
                            <?php if (in_array($c['statut_reel'], ['expiré','expire_bientot'])): ?>
                            <button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#renewModal-<?= $c['id'] ?>" title="Renouveler"><i class="bi bi-arrow-clockwise"></i></button>
                            <?php endif; ?>
                            <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#mdpModal-<?= $c['id'] ?>" title="Mot de passe"><i class="bi bi-key"></i></button>
                            <?php if ($c['id'] != $_SESSION['user_id']): ?>
                                <?php if ($c['statut']==='suspendu'): ?>
                                <a href="?debloquer=<?= $c['id'] ?>" class="btn btn-outline-success" title="Débloquer" onclick="return confirm('Débloquer ?')"><i class="bi bi-unlock"></i></a>
                                <?php else: ?>
                                <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#blockModal-<?= $c['id'] ?>" title="Bloquer"><i class="bi bi-lock"></i></button>
                                <?php endif; ?>
                                <a href="?delete_compte=<?= $c['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer ?')" title="Supprimer"><i class="bi bi-trash"></i></a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <!-- Modals -->
                <div class="modal fade" id="editCompteModal-<?= $c['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Modifier : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_compte"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="mb-3"><label class="form-label">Rôle</label><select name="role" class="form-select"><option value="admin" <?= $c['role']==='admin'?'selected':'' ?>>Administrateur</option><option value="stagiaire" <?= $c['role']==='stagiaire'?'selected':'' ?>>Stagiaire (lecture seule)</option></select></div>
                        <div class="mb-3"><label class="form-label">Date d'expiration</label><input type="datetime-local" name="date_expiration" class="form-control" value="<?= str_replace(' ','T',$c['date_expiration']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Statut manuel</label><select name="statut" class="form-select"><option value="actif" <?= $c['statut']==='actif'?'selected':'' ?>>Actif</option><option value="suspendu" <?= $c['statut']==='suspendu'?'selected':'' ?>>Suspendu</option></select></div>
                        <div class="mb-3"><label class="form-label">Commentaire</label><textarea name="commentaire" class="form-control" rows="2"><?= htmlspecialchars($c['commentaire']??'') ?></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
                </form></div></div></div>

                <div class="modal fade" id="renewModal-<?= $c['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Renouveler : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="renouveler"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="alert alert-info py-2">Le compte sera remis à <strong>Actif</strong>.</div>
                        <div class="mb-3"><label class="form-label">Nouvelle expiration</label><input type="datetime-local" name="nouvelle_expiration" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+1 year')) ?>" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-success">Renouveler</button></div>
                </form></div></div></div>

                <div class="modal fade" id="mdpModal-<?= $c['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Mot de passe : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="changer_mdp"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label><input type="password" name="nouveau_mdp" class="form-control" minlength="6" required></div>
                        <div class="mb-3"><label class="form-label">Confirmer</label><input type="password" name="confirmer_mdp" class="form-control" minlength="6" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-info text-white">Changer</button></div>
                </form></div></div></div>

                <div class="modal fade" id="blockModal-<?= $c['id'] ?>" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title text-danger">Bloquer : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="bloquer"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="alert alert-warning py-2">Cet utilisateur ne pourra plus se connecter.</div>
                        <div class="mb-3"><label class="form-label">Raison</label><textarea name="raison" class="form-control" rows="2" required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-dark">Bloquer</button></div>
                </form></div></div></div>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Journal -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Journal des connexions</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
            <table class="table table-sm mb-0">
                <thead class="table-light"><tr><th class="ps-3">Utilisateur</th><th>Heure</th><th>Statut</th><th>Message</th></tr></thead>
                <tbody>
                <?php foreach ($journal as $j): ?>
                <tr>
                    <td class="ps-3"><?= htmlspecialchars($j['utilisateur']) ?></td>
                    <td><small><?= htmlspecialchars($j['heure_connexion']) ?></small></td>
                    <td><span class="badge <?= $j['statut']==='AUTORISÉE'?'bg-success bg-opacity-10 text-success':($j['statut']==='REFUSÉE'?'bg-danger bg-opacity-10 text-danger':'bg-secondary bg-opacity-10 text-secondary') ?>"><?= htmlspecialchars($j['statut']) ?></span></td>
                    <td><small><?= htmlspecialchars($j['message'] ?? '') ?></small></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Info système -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Informations système</h5></div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block">Élèves</small><strong class="fs-5"><?= $sysInfo['nb_eleves'] ?></strong></div></div>
            <div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block">Moniteurs</small><strong class="fs-5"><?= $sysInfo['nb_moniteurs'] ?></strong></div></div>
            <div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block">Véhicules</small><strong class="fs-5"><?= $sysInfo['nb_vehicules'] ?></strong></div></div>
        </div>
        <div class="d-flex justify-content-between align-items-center">
            <small class="text-muted">PHP <?= phpversion() ?> · Serveur <?= date('Y-m-d H:i:s') ?></small>
            <a href="<?= BASE_URL ?>/pages/corbeille.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-trash3 me-1"></i>Corbeille</a>
        </div>
    </div>
</div>

<!-- Modal Ajout compte -->
<div class="modal fade" id="addCompteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nouveau compte</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="action" value="add_compte">
        <div class="mb-3"><label class="form-label">Identifiant</label><input type="text" name="utilisateur" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" minlength="6" required></div>
        <div class="mb-3"><label class="form-label">Rôle</label><select name="role" class="form-select"><option value="stagiaire">Stagiaire</option><option value="admin">Administrateur</option></select></div>
        <div class="mb-3"><label class="form-label">Expiration</label><input type="datetime-local" name="date_expiration" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+1 year')) ?>" required></div>
        <div class="mb-3"><label class="form-label">Statut</label><select name="statut" class="form-select"><option value="actif">Actif</option><option value="suspendu">Suspendu</option></select></div>
        <div class="mb-3"><label class="form-label">Commentaire</label><textarea name="commentaire" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Créer</button></div>
</form></div></div></div>

<?php include BASE_PATH . '/includes/footer.php'; ?>