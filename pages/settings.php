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
        [trim($_POST['utilisateur']), $hash, $_POST['role'],
         $_POST['date_expiration'], $_POST['statut'], trim($_POST['commentaire']??'')]);
    $msg==='OK' ? $message='Compte créé !' : $error=$msg;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='edit_compte') {
    requirePermission('gestion_comptes');
    $msg = callProcedure("CALL sp_modifier_compte(?,?,?,?,?,@msg)",
        [(int)$_POST['id'], $_POST['role'], $_POST['date_expiration'],
         $_POST['statut'], trim($_POST['commentaire']??'')]);
    $msg==='OK' ? $message='Compte mis à jour !' : $error=$msg;
}
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='renouveler') {
    requirePermission('gestion_comptes');
    $msg = callProcedure("CALL sp_renouveler_compte(?,?,@msg)",
        [(int)$_POST['id'], $_POST['nouvelle_expiration']]);
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
// ── BLOQUER un utilisateur (nouveau) ──────────────────────────────────────
if ($_SERVER['REQUEST_METHOD']==='POST' && ($_POST['action']??'')==='bloquer') {
    requirePermission('gestion_comptes');
    if ((int)$_POST['id'] === (int)$_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas vous bloquer vous-même.';
    } else {
        $msg = callProcedure("CALL sp_bloquer_utilisateur(?,?,@msg)",
            [(int)$_POST['id'], trim($_POST['raison'] ?: 'Comportement inapproprié')]);
        $msg==='OK' ? $message='Utilisateur bloqué.' : $error=$msg;
    }
}
// ── DÉBLOQUER un utilisateur (nouveau) ────────────────────────────────────
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

// ── READ — toutes les vues ─────────────────────────────────────────────────
$comptes = $pdo->query("SELECT * FROM v_comptes ORDER BY utilisateur")->fetchAll();
$journal = $pdo->query("SELECT * FROM v_journal")->fetchAll();
$sysInfo = $pdo->query("SELECT * FROM v_sys_info")->fetch();

$pageTitle = 'Paramètres — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="page-header">
    <h1 class="h2"><i class="bi bi-gear me-2"></i>Paramètres</h1>
    <span class="badge bg-warning text-dark fs-6">Zone Admin</span>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show"><?= htmlspecialchars($message) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger  alert-dismissible fade show"><?= htmlspecialchars($error) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- ── Comptes utilisateurs ─────────────────────────────────────────────── -->
<div class="card mb-4">
    <div class="card-header bg-white d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <h5 class="mb-0">Comptes utilisateurs</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCompteModal">
            <i class="bi bi-person-plus"></i> Ajouter un utilisateur
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th>Identifiant</th><th>Rôle</th><th>Expiration</th><th>Statut</th><th>Commentaire</th><th>Actions</th></tr></thead>
                <tbody>
                <?php foreach ($comptes as $c):
                    $badgeColor = match($c['statut_reel']) {
                        'actif' => 'bg-success', 'expire_bientot' => 'bg-warning text-dark',
                        'expiré' => 'bg-danger', 'suspendu' => 'bg-dark', default => 'bg-secondary',
                    };
                    $badgeLabel = match($c['statut_reel']) {
                        'expire_bientot' => '⚠ Expire bientôt', 'suspendu' => '🚫 Bloqué',
                        default => ucfirst($c['statut_reel']),
                    };
                ?>
                <tr class="<?= $c['statut_reel']==='expiré'?'table-danger':($c['statut_reel']==='suspendu'?'table-secondary':'') ?>">
                    <td><strong><?= htmlspecialchars($c['utilisateur']) ?></strong>
                        <?= $c['id']==$_SESSION['user_id'] ? '<span class="badge bg-info ms-1">Vous</span>' : '' ?></td>
                    <td><span class="badge <?= $c['role']==='admin'?'bg-warning text-dark':'bg-secondary' ?>"><?= $c['role'] ?></span></td>
                    <td><?= htmlspecialchars($c['date_expiration']) ?></td>
                    <td><span class="badge <?= $badgeColor ?>"><?= $badgeLabel ?></span></td>
                    <td><small><?= htmlspecialchars($c['commentaire'] ?? '') ?></small></td>
                    <td class="text-nowrap">
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editCompteModal-<?= $c['id'] ?>" title="Modifier"><i class="bi bi-pencil"></i></button>
                        <?php if (in_array($c['statut_reel'], ['expiré','expire_bientot'])): ?>
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#renewModal-<?= $c['id'] ?>" title="Renouveler"><i class="bi bi-arrow-clockwise"></i></button>
                        <?php endif; ?>
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#mdpModal-<?= $c['id'] ?>" title="Mot de passe"><i class="bi bi-key"></i></button>

                        <?php if ($c['id'] != $_SESSION['user_id']): ?>
                            <?php if ($c['statut']==='suspendu'): ?>
                            <!-- Débloquer -->
                            <a href="?debloquer=<?= $c['id'] ?>" class="btn btn-sm btn-success" title="Débloquer"
                               onclick="return confirm('Débloquer <?= htmlspecialchars($c['utilisateur']) ?> ?')">
                                <i class="bi bi-unlock"></i>
                            </a>
                            <?php else: ?>
                            <!-- Bloquer -->
                            <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#blockModal-<?= $c['id'] ?>" title="Bloquer">
                                <i class="bi bi-lock"></i>
                            </button>
                            <?php endif; ?>
                            <a href="?delete_compte=<?= $c['id'] ?>" class="btn btn-sm btn-danger" title="Supprimer"
                               onclick="return confirm('Supprimer <?= htmlspecialchars($c['utilisateur']) ?> ?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modifier -->
                <div class="modal fade" id="editCompteModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Modifier : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_compte"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="mb-3"><label class="form-label">Rôle</label>
                            <select name="role" class="form-select">
                                <option value="admin" <?= $c['role']==='admin'?'selected':'' ?>>Administrateur</option>
                                <option value="stagiaire" <?= $c['role']==='stagiaire'?'selected':'' ?>>Stagiaire (lecture seule)</option>
                            </select></div>
                        <div class="mb-3"><label class="form-label">Date d'expiration</label>
                            <input type="datetime-local" name="date_expiration" class="form-control" value="<?= str_replace(' ','T',$c['date_expiration']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Statut manuel</label>
                            <select name="statut" class="form-select">
                                <option value="actif" <?= $c['statut']==='actif'?'selected':'' ?>>Actif</option>
                                <option value="suspendu" <?= $c['statut']==='suspendu'?'selected':'' ?>>Suspendu</option>
                            </select></div>
                        <div class="mb-3"><label class="form-label">Commentaire</label>
                            <textarea name="commentaire" class="form-control" rows="2"><?= htmlspecialchars($c['commentaire']??'') ?></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Enregistrer</button></div>
                  </form></div></div>
                </div>

                <!-- Renouveler -->
                <div class="modal fade" id="renewModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Renouveler : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="renouveler"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="alert alert-info">Le compte sera remis à <strong>Actif</strong> avec la nouvelle date.</div>
                        <div class="mb-3"><label class="form-label">Nouvelle expiration</label>
                            <input type="datetime-local" name="nouvelle_expiration" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+1 year')) ?>" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-success"><i class="bi bi-arrow-clockwise"></i> Renouveler</button></div>
                  </form></div></div>
                </div>

                <!-- Mot de passe -->
                <div class="modal fade" id="mdpModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Mot de passe : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="changer_mdp"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label><input type="password" name="nouveau_mdp" class="form-control" minlength="6" required></div>
                        <div class="mb-3"><label class="form-label">Confirmer</label><input type="password" name="confirmer_mdp" class="form-control" minlength="6" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-info text-white"><i class="bi bi-key"></i> Changer</button></div>
                  </form></div></div>
                </div>

                <!-- Bloquer (nouveau) -->
                <div class="modal fade" id="blockModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title text-danger">Bloquer : <?= htmlspecialchars($c['utilisateur']) ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="bloquer"><input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="alert alert-warning">Cet utilisateur ne pourra plus se connecter jusqu'à déblocage.</div>
                        <div class="mb-3"><label class="form-label">Raison du blocage</label>
                            <textarea name="raison" class="form-control" rows="2" placeholder="Ex : comportement inapproprié, abus de droits..." required></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-dark"><i class="bi bi-lock"></i> Bloquer</button></div>
                  </form></div></div>
                </div>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Journal des connexions (via VIEW v_journal) ───────────────────────── -->
<div class="card mb-4">
    <div class="card-header bg-white"><h5 class="mb-0">Journal des connexions (50 dernières)</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
            <table class="table table-sm mb-0">
                <thead><tr><th>Utilisateur</th><th>Heure</th><th>Statut</th><th>Message</th></tr></thead>
                <tbody>
                <?php foreach ($journal as $j): ?>
                <tr class="<?= $j['statut']==='REFUSÉE'?'table-danger':($j['statut']==='AUTORISÉE'?'table-success':'') ?>">
                    <td><?= htmlspecialchars($j['utilisateur']) ?></td>
                    <td><?= htmlspecialchars($j['heure_connexion']) ?></td>
                    <td><span class="badge <?= $j['statut']==='AUTORISÉE'?'bg-success':($j['statut']==='REFUSÉE'?'bg-danger':'bg-secondary') ?>"><?= htmlspecialchars($j['statut']) ?></span></td>
                    <td><?= htmlspecialchars($j['message'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Informations système (via VIEW v_sys_info) ────────────────────────── -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Informations système</h5></div>
    <div class="card-body">
        <ul class="list-group">
            <li class="list-group-item d-flex justify-content-between"><span>Version PHP</span><strong><?= phpversion() ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Heure serveur</span><strong><?= date('Y-m-d H:i:s') ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Élèves actifs</span><strong><?= $sysInfo['nb_eleves'] ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Moniteurs actifs</span><strong><?= $sysInfo['nb_moniteurs'] ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Véhicules actifs</span><strong><?= $sysInfo['nb_vehicules'] ?></strong></li>
            <li class="list-group-item d-flex justify-content-between"><span>Comptes utilisateurs</span><strong><?= $sysInfo['nb_comptes'] ?></strong></li>
        </ul>
        <a href="<?= BASE_URL ?>/pages/corbeille.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-trash3"></i> Voir la corbeille
        </a>
    </div>
</div>

<!-- Modale Ajouter un compte -->
<div class="modal fade" id="addCompteModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Nouveau compte utilisateur</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="action" value="add_compte">
        <div class="mb-3"><label class="form-label">Identifiant</label><input type="text" name="utilisateur" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" minlength="6" required></div>
        <div class="mb-3"><label class="form-label">Rôle</label>
            <select name="role" class="form-select">
                <option value="stagiaire">Stagiaire (lecture seule)</option>
                <option value="admin">Administrateur (accès complet)</option>
            </select></div>
        <div class="mb-3"><label class="form-label">Date d'expiration</label>
            <input type="datetime-local" name="date_expiration" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+1 year')) ?>" required></div>
        <div class="mb-3"><label class="form-label">Statut</label>
            <select name="statut" class="form-select"><option value="actif">Actif</option><option value="suspendu">Suspendu</option></select></div>
        <div class="mb-3"><label class="form-label">Commentaire</label><textarea name="commentaire" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Créer</button></div>
  </form></div></div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
