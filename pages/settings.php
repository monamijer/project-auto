<?php
/**
 * pages/settings.php — Paramètres (ADMIN UNIQUEMENT)
 * - Gérer les comptes (ajouter, modifier rôle, renouveler, supprimer)
 * - Changer le mot de passe d'un compte
 * - Voir le journal des connexions
 * CRUD → sp_ajouter_compte / sp_modifier_compte / sp_renouveler_compte
 *         sp_changer_mot_de_passe / sp_supprimer_compte
 * SELECT → v_comptes
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('voir_parametres'); // Bloque les stagiaires dès l'entrée

$message = ''; $error = '';

// ── AJOUTER un compte ──────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_compte') {
    requirePermission('gestion_comptes');
    // Le hash du mot de passe est créé côté PHP avant d'appeler la procédure
    $hash = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $msg  = callProcedure(
        "CALL sp_ajouter_compte(?,?,?,?,?,?,@msg)",
        [trim($_POST['utilisateur']), $hash, $_POST['role'],
         $_POST['date_expiration'], $_POST['statut'], trim($_POST['commentaire'] ?? '')]
    );
    $msg === 'OK' ? $message = 'Compte créé !' : $error = $msg;
}

// ── MODIFIER rôle / statut / expiration ───────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_compte') {
    requirePermission('gestion_comptes');
    $msg = callProcedure(
        "CALL sp_modifier_compte(?,?,?,?,?,@msg)",
        [(int)$_POST['id'], $_POST['role'], $_POST['date_expiration'],
         $_POST['statut'], trim($_POST['commentaire'] ?? '')]
    );
    $msg === 'OK' ? $message = 'Compte mis à jour !' : $error = $msg;
}

// ── RENOUVELER un compte expiré ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'renouveler') {
    requirePermission('gestion_comptes');
    $msg = callProcedure(
        "CALL sp_renouveler_compte(?,?,@msg)",
        [(int)$_POST['id'], $_POST['nouvelle_expiration']]
    );
    $msg === 'OK' ? $message = 'Compte renouvelé et réactivé !' : $error = $msg;
}

// ── CHANGER MOT DE PASSE ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'changer_mdp') {
    requirePermission('gestion_comptes');
    if ($_POST['nouveau_mdp'] !== $_POST['confirmer_mdp']) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($_POST['nouveau_mdp'], PASSWORD_BCRYPT);
        $msg  = callProcedure("CALL sp_changer_mot_de_passe(?,?,@msg)", [(int)$_POST['id'], $hash]);
        $msg === 'OK' ? $message = 'Mot de passe changé !' : $error = $msg;
    }
}

// ── SUPPRIMER un compte ────────────────────────────────────────────────────
if (isset($_GET['delete_compte'])) {
    requirePermission('gestion_comptes');
    if ((int)$_GET['delete_compte'] === (int)$_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas supprimer votre propre compte.';
    } else {
        $msg = callProcedure("CALL sp_supprimer_compte(?,@msg)", [(int)$_GET['delete_compte']]);
        $msg === 'OK' ? $message = 'Compte supprimé.' : $error = $msg;
    }
}

// ── READ ──────────────────────────────────────────────────────────────────
// v_comptes inclut statut_reel calculé (expiré, expire_bientot, actif, suspendu)
$comptes = $pdo->query("SELECT * FROM v_comptes ORDER BY utilisateur")->fetchAll();
$journal = $pdo->query("SELECT * FROM journal_connexions ORDER BY heure_connexion DESC LIMIT 50")->fetchAll();
$sysInfo = [
    'PHP'        => phpversion(),
    'Serveur'    => date('Y-m-d H:i:s'),
    'Élèves'     => $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn(),
    'Moniteurs'  => $pdo->query("SELECT COUNT(*) FROM instructeurs")->fetchColumn(),
    'Véhicules'  => $pdo->query("SELECT COUNT(*) FROM vehicules")->fetchColumn(),
];

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
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Comptes utilisateurs</h5>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCompteModal">
            <i class="bi bi-person-plus"></i> Ajouter
        </button>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr>
                    <th>Identifiant</th><th>Rôle</th><th>Expiration</th>
                    <th>Statut réel</th><th>Commentaire</th><th>Actions</th>
                </tr></thead>
                <tbody>
                <?php foreach ($comptes as $c):
                    $badgeColor = match($c['statut_reel']) {
                        'actif'           => 'bg-success',
                        'expire_bientot'  => 'bg-warning text-dark',
                        'expiré'          => 'bg-danger',
                        default           => 'bg-secondary',
                    };
                    $badgeLabel = match($c['statut_reel']) {
                        'expire_bientot' => '⚠ Expire bientôt',
                        default          => ucfirst($c['statut_reel']),
                    };
                ?>
                <tr class="<?= $c['statut_reel']==='expiré' ? 'table-danger' : ($c['statut_reel']==='expire_bientot' ? 'table-warning' : '') ?>">
                    <td><strong><?= htmlspecialchars($c['utilisateur']) ?></strong>
                        <?= $c['id']==$_SESSION['user_id'] ? '<span class="badge bg-info ms-1">Vous</span>' : '' ?>
                    </td>
                    <td>
                        <span class="badge <?= $c['role']==='admin' ? 'bg-warning text-dark':'bg-secondary' ?>">
                            <?= $c['role'] ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($c['date_expiration']) ?></td>
                    <td><span class="badge <?= $badgeColor ?>"><?= $badgeLabel ?></span></td>
                    <td><?= htmlspecialchars($c['commentaire'] ?? '') ?></td>
                    <td>
                        <!-- Modifier -->
                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                data-bs-target="#editCompteModal-<?= $c['id'] ?>"
                                title="Modifier">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if ($c['statut_reel']==='expiré' || $c['statut_reel']==='expire_bientot'): ?>
                        <!-- Renouveler -->
                        <button class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#renewModal-<?= $c['id'] ?>"
                                title="Renouveler le compte">
                            <i class="bi bi-arrow-clockwise"></i> Renouveler
                        </button>
                        <?php endif; ?>
                        <!-- Changer mdp -->
                        <button class="btn btn-sm btn-info" data-bs-toggle="modal"
                                data-bs-target="#mdpModal-<?= $c['id'] ?>"
                                title="Changer mot de passe">
                            <i class="bi bi-key"></i>
                        </button>
                        <?php if ($c['id'] != $_SESSION['user_id']): ?>
                        <!-- Supprimer -->
                        <a href="?delete_compte=<?= $c['id'] ?>" class="btn btn-sm btn-danger"
                           onclick="return confirm('Supprimer le compte <?= htmlspecialchars($c['utilisateur']) ?> ?')">
                            <i class="bi bi-trash"></i>
                        </a>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modale Modifier -->
                <div class="modal fade" id="editCompteModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Modifier : <?= htmlspecialchars($c['utilisateur']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_compte">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="mb-3"><label class="form-label">Rôle</label>
                            <select name="role" class="form-select">
                                <option value="admin"     <?= $c['role']==='admin'     ? 'selected':'' ?>>Administrateur</option>
                                <option value="stagiaire" <?= $c['role']==='stagiaire' ? 'selected':'' ?>>Stagiaire (lecture seule)</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label">Date d'expiration</label>
                            <input type="datetime-local" name="date_expiration" class="form-control"
                                   value="<?= str_replace(' ','T',$c['date_expiration']) ?>" required></div>
                        <div class="mb-3"><label class="form-label">Statut manuel</label>
                            <select name="statut" class="form-select">
                                <option value="actif"    <?= $c['statut']==='actif'    ? 'selected':'' ?>>Actif</option>
                                <option value="suspendu" <?= $c['statut']==='suspendu' ? 'selected':'' ?>>Suspendu</option>
                            </select></div>
                        <div class="mb-3"><label class="form-label">Commentaire</label>
                            <textarea name="commentaire" class="form-control" rows="2"><?= htmlspecialchars($c['commentaire'] ?? '') ?></textarea></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary">Enregistrer</button></div>
                  </form></div></div>
                </div>

                <!-- Modale Renouveler -->
                <div class="modal fade" id="renewModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Renouveler : <?= htmlspecialchars($c['utilisateur']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="renouveler">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="alert alert-info">
                            Le compte sera remis à <strong>Actif</strong> avec la nouvelle date d'expiration.
                        </div>
                        <div class="mb-3"><label class="form-label">Nouvelle date d'expiration</label>
                            <input type="datetime-local" name="nouvelle_expiration" class="form-control"
                                   value="<?= date('Y-m-d\TH:i', strtotime('+1 year')) ?>" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success"><i class="bi bi-arrow-clockwise"></i> Renouveler</button></div>
                  </form></div></div>
                </div>

                <!-- Modale Changer mot de passe -->
                <div class="modal fade" id="mdpModal-<?= $c['id'] ?>" tabindex="-1">
                  <div class="modal-dialog"><div class="modal-content"><form method="POST">
                    <div class="modal-header"><h5 class="modal-title">Mot de passe : <?= htmlspecialchars($c['utilisateur']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="changer_mdp">
                        <input type="hidden" name="id" value="<?= $c['id'] ?>">
                        <div class="mb-3"><label class="form-label">Nouveau mot de passe</label>
                            <input type="password" name="nouveau_mdp" class="form-control" minlength="6" required></div>
                        <div class="mb-3"><label class="form-label">Confirmer</label>
                            <input type="password" name="confirmer_mdp" class="form-control" minlength="6" required></div>
                    </div>
                    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-info text-white"><i class="bi bi-key"></i> Changer</button></div>
                  </form></div></div>
                </div>

                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Journal des connexions ─────────────────────────────────────────────── -->
<div class="card mb-4">
    <div class="card-header bg-white"><h5 class="mb-0">Journal des connexions (50 dernières)</h5></div>
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height:300px;overflow-y:auto;">
            <table class="table table-sm mb-0">
                <thead><tr><th>Utilisateur</th><th>Heure</th><th>Statut</th><th>Message</th></tr></thead>
                <tbody>
                <?php foreach ($journal as $j): ?>
                <tr class="<?= $j['statut']==='REFUSÉE' ? 'table-danger':($j['statut']==='AUTORISÉE' ? 'table-success':'') ?>">
                    <td><?= htmlspecialchars($j['utilisateur']) ?></td>
                    <td><?= htmlspecialchars($j['heure_connexion']) ?></td>
                    <td><span class="badge <?= $j['statut']==='AUTORISÉE' ? 'bg-success':($j['statut']==='REFUSÉE' ? 'bg-danger':'bg-secondary') ?>">
                        <?= htmlspecialchars($j['statut']) ?></span></td>
                    <td><?= htmlspecialchars($j['message'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ── Informations système ────────────────────────────────────────────────── -->
<div class="card">
    <div class="card-header bg-white"><h5 class="mb-0">Informations système</h5></div>
    <div class="card-body">
        <ul class="list-group">
        <?php foreach ($sysInfo as $k => $v): ?>
            <li class="list-group-item d-flex justify-content-between">
                <span><?= $k ?></span><strong><?= $v ?></strong>
            </li>
        <?php endforeach; ?>
        </ul>
    </div>
</div>

<!-- Modale Ajouter un compte -->
<div class="modal fade" id="addCompteModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header"><h5 class="modal-title">Nouveau compte</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <input type="hidden" name="action" value="add_compte">
        <div class="mb-3"><label class="form-label">Identifiant</label>
            <input type="text" name="utilisateur" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control" minlength="6" required></div>
        <div class="mb-3"><label class="form-label">Rôle</label>
            <select name="role" class="form-select">
                <option value="stagiaire">Stagiaire (lecture seule)</option>
                <option value="admin">Administrateur (accès complet)</option>
            </select></div>
        <div class="mb-3"><label class="form-label">Date d'expiration</label>
            <input type="datetime-local" name="date_expiration" class="form-control"
                   value="<?= date('Y-m-d\TH:i', strtotime('+1 year')) ?>" required></div>
        <div class="mb-3"><label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="actif">Actif</option>
                <option value="suspendu">Suspendu</option>
            </select></div>
        <div class="mb-3"><label class="form-label">Commentaire</label>
            <textarea name="commentaire" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
        <button type="submit" class="btn btn-primary">Créer</button></div>
  </form></div></div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
