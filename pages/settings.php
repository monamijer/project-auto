<?php
/**
 * pages/settings.php — Paramètres (ADMIN UNIQUEMENT)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
requirePermission('voir_parametres');

$message = '';
$error = '';

$roles = ['admin', 'directeur', 'secretaire', 'caissier', 'moniteur', 'stagiaire'];
$rolesLabels = ['admin' => 'Administrateur', 'directeur' => 'Directeur', 'secretaire' => 'Secrétaire', 'caissier' => 'Caissier', 'moniteur' => 'Moniteur', 'stagiaire' => 'Stagiaire (lecture seule)'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_compte') {
    requirePermission('gestion_comptes');
    $hash = password_hash($_POST['mot_de_passe'], PASSWORD_BCRYPT);
    $msg = callProcedure('CALL sp_ajouter_compte(?,?,?,?,?,?,@msg)', [
        trim($_POST['utilisateur']),
        $hash,
        $_POST['role'],
        $_POST['date_expiration'],
        $_POST['statut'],
        trim($_POST['commentaire'] ?? ''),
    ]);
    $msg === 'OK' ? ($message = 'Compte créé !') : ($error = $msg);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_compte') {
    requirePermission('gestion_comptes');
    $msg = callProcedure('CALL sp_modifier_compte(?,?,?,?,?,@msg)', [(int) $_POST['id'], $_POST['role'], $_POST['date_expiration'], $_POST['statut'], trim($_POST['commentaire'] ?? '')]);
    $msg === 'OK' ? ($message = 'Compte mis à jour !') : ($error = $msg);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'renouveler') {
    requirePermission('gestion_comptes');
    $msg = callProcedure('CALL sp_renouveler_compte(?,?,@msg)', [(int) $_POST['id'], $_POST['nouvelle_expiration']]);
    $msg === 'OK' ? ($message = 'Compte renouvelé !') : ($error = $msg);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'changer_mdp') {
    requirePermission('gestion_comptes');
    if ($_POST['nouveau_mdp'] !== $_POST['confirmer_mdp']) {
        $error = 'Les mots de passe ne correspondent pas.';
    } else {
        $hash = password_hash($_POST['nouveau_mdp'], PASSWORD_BCRYPT);
        $msg = callProcedure('CALL sp_changer_mot_de_passe(?,?,@msg)', [(int) $_POST['id'], $hash]);
        $msg === 'OK' ? ($message = 'Mot de passe changé !') : ($error = $msg);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'bloquer') {
    requirePermission('gestion_comptes');
    if ((int) $_POST['id'] === (int) $_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas vous bloquer.';
    } else {
        $msg = callProcedure('CALL sp_bloquer_utilisateur(?,?,@msg)', [(int) $_POST['id'], trim($_POST['raison'] ?: 'Comportement inapproprié')]);
        $msg === 'OK' ? ($message = 'Utilisateur bloqué.') : ($error = $msg);
    }
}
if (isset($_GET['deverrouiller'])) {
    requirePermission('gestion_comptes');
    callProcedure('CALL sp_deverrouiller_compte(?,@msg)', [(int) $_GET['deverrouiller']]);
    $message = 'Compte déverrouillé.';
}
if (isset($_GET['debloquer'])) {
    requirePermission('gestion_comptes');
    callProcedure('CALL sp_debloquer_utilisateur(?,@msg)', [(int) $_GET['debloquer']]);
    $message = 'Utilisateur débloqué.';
}
if (isset($_GET['delete_compte'])) {
    requirePermission('gestion_comptes');
    if ((int) $_GET['delete_compte'] === (int) $_SESSION['user_id']) {
        $error = 'Vous ne pouvez pas vous supprimer.';
    } else {
        callProcedure('CALL sp_supprimer_compte(?,@msg)', [(int) $_GET['delete_compte']]);
        $message = 'Compte supprimé.';
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'purger_journal') {
    $pdo->exec(
        'DELETE t1 FROM journal_connexions t1 INNER JOIN journal_connexions t2 WHERE t1.id > t2.id AND t1.utilisateur = t2.utilisateur AND ABS(TIMESTAMPDIFF(SECOND, t1.heure_connexion, t2.heure_connexion)) < 60'
    );
    $pdo->exec('DELETE FROM journal_connexions WHERE heure_connexion < DATE_SUB(NOW(), INTERVAL 30 DAY)');
    $message = 'Journal purgé !';
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_config') {
    requirePermission('voir_parametres');
    foreach ($_POST['config'] ?? [] as $key => $value) {
        $pdo->prepare('INSERT INTO config_systeme (cle, valeur) VALUES (?, ?) ON DUPLICATE KEY UPDATE valeur = ?')->execute([$key, trim($value), trim($value)]);
    }
    $message = 'Configuration enregistrée !';
}

$comptes = $pdo->query('SELECT v.*, e.tentatives_echouees, e.verrouille_jusqua FROM v_comptes v JOIN expirations_utilisateurs e ON e.id = v.id ORDER BY v.utilisateur')->fetchAll();
$activites = $pdo->query('SELECT * FROM v_journal_activites')->fetchAll();
$journal = $pdo->query('SELECT * FROM v_journal')->fetchAll();
$sysInfo = $pdo->query('SELECT * FROM v_sys_info')->fetch();

$pageTitle = 'Paramètres — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
    <div><h1 class="h3 mb-1"><i class="bi bi-gear me-2 text-primary"></i>Paramètres</h1><p class="text-muted mb-0">Administration du système</p></div>
    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">Zone Admin</span>
</div>

<?php if ($message): ?><div class="alert alert-success alert-dismissible fade show d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars(
    $message
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show d-flex align-items-center"><i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars(
    $error
) ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

<!-- Configuration école -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-building me-2"></i>Configuration de l'école</h5></div>
    <div class="card-body">
        <form method="POST" class="row g-3">
            <?= csrf_field() ?><input type="hidden" name="action" value="save_config">
            <?php
            $champs = [
                'nom_ecole' => 'Nom',
                'telephone' => 'Téléphone',
                'email_ecole' => 'Email école',
                'adresse' => 'Adresse',
                'devise' => 'Devise',
                'smtp_user' => 'Email SMTP (Gmail)',
                'smtp_pass' => 'Mot de passe app Gmail',
            ];
            foreach ($champs as $k => $l): ?>
            <div class="col-md-6"><label class="form-label"><?= $l ?></label><input type="<?= $k === 'smtp_pass'
    ? 'password'
    : 'text' ?>" name="config[<?= $k ?>]" class="form-control" value="<?= htmlspecialchars(getConfig($k)) ?>"></div>
            <?php endforeach;
            ?>
            <div class="col-12"><button type="submit" class="btn btn-primary">Enregistrer</button></div>
        </form>
    </div>
</div>

<!-- Comptes -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3"><h5 class="mb-0"><i class="bi bi-people me-2"></i>Comptes utilisateurs</h5><button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCompteModal"><i class="bi bi-person-plus me-1"></i>Ajouter</button></div>
    <div class="card-body p-0"><div class="table-responsive"><table class="table table-hover align-middle mb-0">
        <thead class="table-light"><tr><th class="ps-3">Identifiant</th><th>Rôle</th><th>Expiration</th><th>Statut</th><th>Commentaire</th><th class="text-end pe-3">Actions</th></tr></thead>
        <tbody>
        <?php foreach ($comptes as $c):

            $badgeColor = match ($c['statut_reel']) {
                'actif' => 'bg-success bg-opacity-10 text-success',
                'expire_bientot' => 'bg-warning bg-opacity-10 text-warning',
                'expiré' => 'bg-danger bg-opacity-10 text-danger',
                'suspendu' => 'bg-dark bg-opacity-10 text-dark',
                default => 'bg-secondary bg-opacity-10 text-secondary',
            };
            $badgeLabel = match ($c['statut_reel']) {
                'expire_bientot' => 'Expire bientôt',
                'suspendu' => 'Bloqué',
                default => ucfirst($c['statut_reel']),
            };
            $roleLabel = $rolesLabels[$c['role']] ?? $c['role'];
            $estVerrouille = $c['verrouille_jusqua'] && strtotime($c['verrouille_jusqua']) > time();
            ?>
        <tr class="<?= $estVerrouille ? 'table-warning' : '' ?>">
            <td class="ps-3">
                <strong><?= htmlspecialchars($c['utilisateur']) ?></strong>
                <?= $c['id'] == $_SESSION['user_id'] ? '<span class="badge bg-info bg-opacity-10 text-info ms-1">Vous</span>' : '' ?>
                <?php if ($estVerrouille): ?><span class="badge bg-dark ms-1"><i class="bi bi-lock-fill"></i> Verrouillé</span><?php endif; ?>
            </td>
            <td><span class="badge bg-light text-dark"><?= htmlspecialchars($roleLabel) ?></span></td>
            <td><small><?= htmlspecialchars($c['date_expiration']) ?></small></td>
            <td><span class="badge <?= $badgeColor ?>"><?= $badgeLabel ?></span></td>
            <td><small class="text-muted"><?= htmlspecialchars($c['commentaire'] ?? '—') ?></small></td>
            <td class="text-end pe-3"><div class="btn-group btn-group-sm">
                <button class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#editCompteModal-<?= $c['id'] ?>" title="Modifier"><i class="bi bi-pencil"></i></button>
                <?php if (in_array($c['statut_reel'], ['expiré', 'expire_bientot'])): ?><button class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#renewModal-<?= $c[
    'id'
] ?>" title="Renouveler"><i class="bi bi-arrow-clockwise"></i></button><?php endif; ?>
                <button class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#mdpModal-<?= $c['id'] ?>" title="Mot de passe"><i class="bi bi-key"></i></button>
                <?php if ($estVerrouille): ?><a href="?deverrouiller=<?= $c['id'] ?>" class="btn btn-outline-dark" title="Déverrouiller"><i class="bi bi-unlock-fill"></i></a><?php endif; ?>
                <?php if ($c['id'] != $_SESSION['user_id']): ?>
                    <?php if ($c['statut'] === 'suspendu'): ?><a href="?debloquer=<?= $c['id'] ?>" class="btn btn-outline-success" title="Débloquer"><i class="bi bi-unlock"></i></a>
                    <?php else: ?><button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#blockModal-<?= $c[
    'id'
] ?>" title="Bloquer"><i class="bi bi-lock"></i></button><?php endif; ?>
                    <a href="?delete_compte=<?= $c['id'] ?>" class="btn btn-outline-danger" onclick="return confirm('Supprimer ?')"><i class="bi bi-trash"></i></a>
                <?php endif; ?>
            </div></td>
        </tr>
        <?php
        endforeach; ?>
        </tbody>
    </table></div></div>
</div>

<!-- Journal connexions -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between"><h5 class="mb-0"><i class="bi bi-journal-text me-2"></i>Journal connexions</h5><form method="POST"><input type="hidden" name="action" value="purger_journal"><button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Purger ?')">Purger</button></form></div>
    <div class="card-body p-0"><div class="table-responsive" style="max-height:300px;overflow-y:auto;"><table class="table table-sm mb-0"><thead class="table-light"><tr><th class="ps-3">Utilisateur</th><th>Heure</th><th>Statut</th><th>Message</th></tr></thead><tbody>
    <?php foreach ($journal as $j): ?><tr><td class="ps-3"><?= htmlspecialchars($j['utilisateur']) ?></td><td><small><?= htmlspecialchars(
    $j['heure_connexion']
) ?></small></td><td><span class="badge <?= $j['statut'] === 'AUTORISÉE' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' ?>"><?= htmlspecialchars(
    $j['statut']
) ?></span></td><td><small><?= htmlspecialchars($j['message'] ?? '') ?></small></td></tr><?php endforeach; ?>
    </tbody></table></div></div>
</div>

<!-- Journal activités -->
<div class="card shadow-sm border-0 mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Journal activités</h5></div><div class="card-body p-0"><div class="table-responsive" style="max-height:350px;overflow-y:auto;"><table class="table table-sm mb-0"><thead class="table-light"><tr><th class="ps-3">Date</th><th>Utilisateur</th><th>Action</th><th>Module</th><th>Détails</th></tr></thead><tbody>
    <?php foreach ($activites as $a):
        $actBadge = match ($a['action']) {
            'AJOUT' => 'bg-success bg-opacity-10 text-success',
            'MODIFICATION' => 'bg-warning bg-opacity-10 text-warning',
            'SUPPRESSION' => 'bg-danger bg-opacity-10 text-danger',
            default => 'bg-secondary bg-opacity-10 text-secondary',
        }; ?>
    <tr><td class="ps-3"><small><?= htmlspecialchars($a['date_action']) ?></small></td><td><?= htmlspecialchars($a['utilisateur']) ?></td><td><span class="badge <?= $actBadge ?>"><?= htmlspecialchars(
    $a['action']
) ?></span></td><td><?= htmlspecialchars($a['module']) ?> <?= $a['element_id'] ? '#' . $a['element_id'] : '' ?></td><td><small class="text-muted"><?= htmlspecialchars(
    $a['details'] ?? ''
) ?></small></td></tr>
    <?php
    endforeach; ?>
</tbody></table></div></div></div>

<!-- Info système -->
<div class="card shadow-sm border-0"><div class="card-header bg-white py-3"><h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>Infos système</h5></div><div class="card-body">
    <div class="row g-3 mb-3"><div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small>Élèves</small><strong class="fs-5"><?= $sysInfo[
        'nb_eleves'
    ] ?></strong></div></div><div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small>Moniteurs</small><strong class="fs-5"><?= $sysInfo[
    'nb_moniteurs'
] ?></strong></div></div><div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small>Véhicules</small><strong class="fs-5"><?= $sysInfo['nb_vehicules'] ?></strong></div></div></div>
    <div class="d-flex justify-content-between align-items-center"><small class="text-muted">PHP <?= phpversion() ?> · <?= date(
     'Y-m-d H:i:s'
 ) ?></small><a href="<?= BASE_URL ?>/pages/corbeille.php" class="btn btn-outline-secondary btn-sm">Corbeille</a></div>
</div></div>

<!-- Modal Ajout compte -->
<div class="modal fade" id="addCompteModal" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><form method="POST">
    <div class="modal-header bg-primary text-white"><h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Nouveau compte</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
    <div class="modal-body">
        <?= csrf_field() ?><input type="hidden" name="action" value="add_compte">
        <div class="mb-3"><label class="form-label">Identifiant</label><input type="text" name="utilisateur" class="form-control" required></div>
        <div class="mb-3"><label class="form-label">Mot de passe</label><input type="password" name="mot_de_passe" class="form-control" minlength="6" required></div>
        <div class="mb-3"><label class="form-label">Rôle</label><select name="role" class="form-select"><?php foreach ($roles as $role): ?><option value="<?= $role ?>" <?= $role === 'stagiaire'
    ? 'selected'
    : '' ?>><?= $rolesLabels[$role] ?></option><?php endforeach; ?></select></div>
        <div class="mb-3"><label class="form-label">Expiration</label><input type="datetime-local" name="date_expiration" class="form-control" value="<?= date(
            'Y-m-d\TH:i',
            strtotime('+1 year')
        ) ?>" required></div>
        <div class="mb-3"><label class="form-label">Statut</label><select name="statut" class="form-select"><option value="actif">Actif</option><option value="suspendu">Suspendu</option></select></div>
        <div class="mb-3"><label class="form-label">Commentaire</label><textarea name="commentaire" class="form-control" rows="2"></textarea></div>
    </div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button><button type="submit" class="btn btn-primary">Créer</button></div>
</form></div></div></div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
