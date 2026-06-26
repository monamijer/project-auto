<?php
/**
 * pages/aide.php — Guide d'utilisation (#49)
 */
session_start();
require_once __DIR__ . '/../config/database.php';
require_once BASE_PATH . '/includes/auth.php';
requireLogin();
$pageTitle = 'Guide — Auto École Pro';
include BASE_PATH . '/includes/header.php';
?>
<style>
.guide-step{counter-increment:step;position:relative;padding-left:3rem;margin-bottom:2rem;}
.guide-step::before{content:counter(step);position:absolute;left:0;top:0;width:2.2rem;height:2.2rem;border-radius:50%;background:#4f46e5;color:#fff;font-weight:700;display:flex;align-items:center;justify-content:center;font-size:.9rem;}
</style>
<div class="page-header mb-4"><h1 class="h4 mb-0"><i class="bi bi-book me-2 text-primary"></i>Guide d'utilisation</h1></div>

<div class="row g-4">
<div class="col-md-3">
    <div class="card border-0 shadow-sm sticky-top" style="top:70px;">
        <div class="card-header bg-white py-3"><h6 class="mb-0">Sections</h6></div>
        <div class="list-group list-group-flush">
            <?php foreach (
                [
                    ['#debut', 'Démarrage'],
                    ['#roles', 'Rôles'],
                    ['#eleves', 'Élèves'],
                    ['#lecons', 'Leçons'],
                    ['#paiements', 'Paiements'],
                    ['#docs', 'Documents'],
                    ['#rapports', 'Rapports'],
                    ['#securite', 'Sécurité'],
                    ['#appels', 'Appels'],
                    ['#backup', 'Sauvegarde'],
                ]
                as [$id, $label]
            ): ?>
            <a href="<?= $id ?>" class="list-group-item list-group-item-action py-2 small"><?= $label ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="col-md-9" style="counter-reset:step;">

<div id="debut" class="card border-0 shadow-sm mb-4"><div class="card-header bg-primary text-white py-3"><h5 class="mb-0">🚀 Démarrage</h5></div><div class="card-body">
    <div class="guide-step"><h6>Connexion</h6><p class="text-muted mb-0">Accédez à <code>/project_auto/pages/login.php</code>. Admin par défaut : <strong>admin</strong></p></div>
    <div class="guide-step"><h6>Installer dépendances</h6><p class="text-muted mb-0"><code>cd /opt/lampp/htdocs/project_auto && npm install</code></p></div>
    <div class="guide-step"><h6>Démarrer serveur appels</h6><p class="text-muted mb-0"><code>node server.js</code> (laisser le terminal ouvert)</p></div>
    <div class="guide-step"><h6>Accès mobile</h6><p class="text-muted mb-0">Trouvez l'IP : <code>ip addr show</code> puis <code>http://192.168.x.x/project_auto</code></p></div>
</div></div>

<div id="roles" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">👥 Rôles</h5></div><div class="card-body p-0"><table class="table table-hover mb-0"><thead class="table-light"><tr><th class="ps-3">Rôle</th><th>Permissions</th></tr></thead><tbody>
    <?php foreach (
        [
            ['🔴 Admin', 'Tout'],
            ['🟤 Directeur', 'Sauf comptes'],
            ['🔵 Secrétaire', 'Élèves, leçons, paiements, docs'],
            ['🟢 Caissier', 'Paiements'],
            ['🟡 Moniteur', 'Leçons'],
            ['⚫ Stagiaire', 'Lecture seule'],
        ]
        as [$r, $p]
    ): ?>
    <tr><td class="ps-3 fw-medium"><?= $r ?></td><td class="text-muted small"><?= $p ?></td></tr>
    <?php endforeach; ?>
</tbody></table></div></div>

<div id="eleves" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">👤 Élèves</h5></div><div class="card-body">
    <p class="text-muted">Ajouter, modifier, voir profil, supprimer (corbeille). Matricule auto. Commentaires dans le profil.</p>
</div></div>

<div id="lecons" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">📅 Leçons</h5></div><div class="card-body">
    <p class="text-muted">Planifier → choisir élève, moniteur, véhicule, date. Marquer effectuée ✅ ou annuler ❌. Vue calendrier disponible.</p>
</div></div>

<div id="paiements" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">💰 Paiements</h5></div><div class="card-body">
    <p class="text-muted">Enregistrer → générer reçu imprimable. Les impayés apparaissent dans Alertes.</p>
</div></div>

<div id="docs" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">📁 Documents</h5></div><div class="card-body">
    <p class="text-muted">Upload (PDF, JPG, PNG, DOCX). Recherche par nom/type/élève. Partage lien 7 jours. Versionnage auto.</p>
</div></div>

<div id="rapports" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">📊 Rapports</h5></div><div class="card-body">
    <p class="text-muted">Mensuel, personnalisé, comparatif, prévisions. Export Excel + PDF imprimable.</p>
</div></div>

<div id="securite" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">🔐 Sécurité</h5></div><div class="card-body">
    <p class="text-muted">Verrouillage 5 échecs, 2FA par email, session 20 min, journal traçage, CSRF.</p>
</div></div>

<div id="appels" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">📹 Appels</h5></div><div class="card-body">
    <p class="text-muted">Démarrer <code>node server.js</code>, ouvrir Messages, cliquer 📞. Sur réseau local : activer flag Chrome.</p>
</div></div>

<div id="backup" class="card border-0 shadow-sm mb-4"><div class="card-header bg-white py-3"><h5 class="mb-0">💾 Sauvegarde</h5></div><div class="card-body">
    <p class="text-muted">Menu Backup → Lancer sauvegarde (.sql). Restaurer depuis fichier. Cron quotidien recommandé.</p>
</div></div>

</div></div>
<?php include BASE_PATH . '/includes/footer.php'; ?>
